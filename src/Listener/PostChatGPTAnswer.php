<?php

namespace Datlechin\FlarumChatGPT\Listener;

use Carbon\Carbon;
use Datlechin\FlarumChatGPT\OpenAIClient;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Post\Event\Posted;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Guest;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use s9e\TextFormatter\Utils;

class PostChatGPTAnswer
{
    public function __construct(
        protected Dispatcher $events,
        protected SettingsRepositoryInterface $settings,
        protected OpenAIClient $client,
        protected LoggerInterface $logger
    ) {
    }

    public function subscribe(Dispatcher $events)
    {
        $events->listen(Started::class, [$this, 'handle']);
        $events->listen(Posted::class, [$this, 'handleMention']);
    }

    public function handle(Started $event): void
    {
        if (! $this->settings->get('datlechin-chatgpt.enable_on_discussion_started', true)) {
            return;
        }

        $discussion = $event->discussion;
        $actor = $event->actor;
        $enabledTagIds = $this->settings->get('datlechin-chatgpt.enabled-tags', '[]');

        if ($enabledTagIds = json_decode($enabledTagIds, true)) {
            $discussion = $event->discussion;
            $tagIds = Arr::pluck($discussion->tags, 'id');
            
            if (! array_intersect($enabledTagIds, $tagIds)) {
                return;
            }
        }

        if ($userId = $this->settings->get('datlechin-chatgpt.user_prompt')) {
            $user = User::find($userId);
        }

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $discussion->loadMissing(['firstPost', 'tags']);
        
        try {
            $job = new \Datlechin\FlarumChatGPT\Jobs\GenerateAIResponseJob(
                $discussion->id,
                $actor->id,
                $user->id ?? null,
                "{$discussion->title}\n{$discussion->firstPost->content}"
            );
            
            $job->handle(
                $this->client,
                $this->logger
            );

        } catch (\Throwable $e) {
            $this->logger->error('AI reply generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() 
            ]);
        }
    }

    private function extractQuestion(string $content): ?string 
    {
        $question = preg_replace('/@[^\s]+/', '', $content);
        $question = trim($question);
        return !empty($question) ? $question : null;
    }

    private function isBotMentioned(Post $post, string $botUserId): bool
    {
        if (!$post->parsed_content) {
            $this->logger->debug('No parsed content');
            return false;
        }

        $xml = $post->parsed_content;
        
        $hasMention = false;
        if (strpos($xml, 'USERMENTION') !== false) {
            $mentions = Utils::getAttributeValues($xml, 'USERMENTION', 'id');
            $hasMention = in_array($botUserId, $mentions);
        }
        
        if (!$hasMention && preg_match('/@"[^"]+"\#p\d+/', $post->content)) {
            preg_match_all('/@"[^"]+"\#p(\d+)/', $post->content, $matches);
            if (!empty($matches[1])) {
                $postIds = $matches[1];
                foreach ($postIds as $postId) {
                    $mentionedPost = Post::find($postId);
                    if ($mentionedPost && $mentionedPost->user_id == $botUserId) {
                        $hasMention = true;
                        break;
                    }
                }
            }
        }

        $this->logger->debug('Bot mention check', [
            'content' => $post->content,
            'has_mention' => $hasMention,
            'bot_id' => $botUserId
        ]);

        return $hasMention;
    }

    private function buildContext($discussion, $currentPost): string
    {
        $context = "话题标题: {$discussion->title}\n\n";
        
        if ($discussion->firstPost) {
            $context .= "话题内容: " . Utils::removeFormatting($discussion->firstPost->parsed_content) . "\n\n";
        }
        
        $recentPosts = $discussion->posts()
            ->whereVisibleTo($currentPost->user)
            ->where('id', '<', $currentPost->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->reverse();

        if ($recentPosts->isNotEmpty()) {
            $context .= "最近的对话记录:\n";
            foreach ($recentPosts as $post) {
                $username = $post->user ? $post->user->username : 'unknown';
                $content = Utils::removeFormatting($post->parsed_content);
                $content = $this->extractQuestion($content);
                $context .= "{$username}: {$content}\n\n";
            }
        }
        
        return $context;
    }

    private function buildPrompt(string $context, string $question): string
    {
        $hasChinese = preg_match('/[\x{4e00}-\x{9fa5}]/u', $question);
        
        if ($hasChinese) {
            return <<<EOT
请根据以下对话上下文回答问题。

背景信息:
{$context}

当前问题:
{$question}

要求:
1. 请直接回答问题，避免重复问题内容
2. 回答要有理有据，并保持友好的语气
3. 如果上下文信息不足，可以自由发挥
4. 回复中不要包含已知信息
5. 保持回答的专业性、准确性
6. 使用简体中文回答

EOT;
        }

        return <<<EOT
Please answer the question based on the following context.

Background:
{$context}

Current question:
{$question}

Requirements:
1. Answer directly without repeating the question
2. Provide well-reasoned answers with a friendly tone
3. Use your knowledge if context is insufficient
4. Avoid including known information
5. Maintain professionalism and accuracy
6. Answer in English

EOT;
    }

    private function processResponse(string $prompt, array $context = []): string
    {
        try {
            $response = $this->client->completions($prompt);
            
            if (empty($response)) {
                throw new \RuntimeException('Empty response from AI');
            }
            
            $this->logger->debug('AI response received', [
                'prompt_length' => mb_strlen($prompt),
                'response_length' => mb_strlen($response)
            ]);
            
            return $response;
            
        } catch (\Throwable $e) {
            $this->logger->error('Failed to get AI response', array_merge([
                'error' => $e->getMessage(),
                'prompt_length' => mb_strlen($prompt)
            ], $context));
            throw $e;
        }
    }

    private function createReplyPost(string $content, $discussion, $actor, $botUser): CommentPost
    {
        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $botUser->id,
            $actor
        );
        
        $post->save();
        
        $this->logger->debug('Reply post created', [
            'post_id' => $post->id,
            'discussion_id' => $discussion->id,
            'bot_id' => $botUser->id,
            'content_length' => mb_strlen($content)
        ]);
        
        return $post;
    }

    public function handleMention(Posted $event)
    {
        try {
            $post = $event->post;
            $discussion = $post->discussion;

            $botUserId = $this->settings->get('datlechin-chatgpt.user_prompt');
            if ($post->user_id == $botUserId) {
                $this->logger->debug('Skipping bot\'s own post');
                return;
            }

            $cacheKey = "chatgpt_replied_{$post->id}";
            if (app('cache')->has($cacheKey)) {
                $this->logger->debug('Already replied to this post', ['post_id' => $post->id]);
                return;
            }

            if (!$discussion) {
                $this->logger->warning('Discussion not found', ['post_id' => $post->id]);
                return;
            }

            if (!$botUserId || !($botUser = User::find($botUserId))) {
                $this->logger->warning('Bot user not configured or not found');
                return;
            }

            if (!$this->isBotMentioned($post, $botUserId)) {
                $this->logger->debug('Bot not mentioned');
                return;
            }

            $question = $this->extractQuestion($post->content);
            if (!$question) {
                $this->logger->debug('No valid question found');
                return;
            }

            $event->actor->assertCan('useChatGPTAssistant', $discussion);
            
            $discussion->loadMissing(['firstPost', 'posts', 'tags']);

            $context = $this->buildContext($discussion, $post);
            
            $prompt = $this->buildPrompt($context, $question);

            $this->logger->debug('Processing question', [
                'post_id' => $post->id,
                'discussion_id' => $discussion->id,
                'question' => $question,
                'context_length' => mb_strlen($context)
            ]);

            $response = $this->processResponse($prompt, [
                'post_id' => $post->id,
                'discussion_id' => $discussion->id
            ]);

            app('cache')->put($cacheKey, true, Carbon::now()->addMinutes(5));

            $replyPost = CommentPost::reply(
                $discussion->id,
                $response,
                $botUser->id,
                $event->actor
            );

            $replyPost->unsetEventDispatcher();
            $replyPost->save();

            $this->logger->info('Response posted successfully', [
                'post_id' => $replyPost->id,
                'discussion_id' => $discussion->id,
                'response_length' => mb_strlen($response),
                'bot_id' => $botUser->id
            ]);

        } catch (\Throwable $e) {
            $this->logError('Response generation failed', $e, [
                'post_id' => $post->id ?? null,
                'discussion_id' => $discussion->id ?? null,
                'actor_id' => $event->actor->id ?? null
            ]);
            throw $e;
        }
    }

    private function logError(string $message, \Throwable $e, array $context = []): void
    {
        $this->logger->error($message, array_merge([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], $context));
    }
}