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
        try {
            // 检查是否启用自动回复
            if (! $this->settings->get('datlechin-chatgpt.enable_on_discussion_started', true)) {
                return;
            }

            $discussion = $event->discussion;
            $actor = $event->actor;

            // 检查标签权限
            $enabledTagIds = $this->settings->get('datlechin-chatgpt.enabled-tags', '[]');
            if ($enabledTagIds = json_decode($enabledTagIds, true)) {
                $discussion->loadMissing(['tags']);
                $tagIds = Arr::pluck($discussion->tags, 'id');
                
                if (! array_intersect($enabledTagIds, $tagIds)) {
                    $this->logger->debug('Discussion tags not enabled for auto-reply', [
                        'discussion_id' => $discussion->id,
                        'tags' => $tagIds
                    ]);
                    return;
                }
            }

            // 获取机器人用户
            if (!($userId = $this->settings->get('datlechin-chatgpt.user_prompt')) || 
                !($botUser = User::find($userId))) {
                $this->logger->warning('Bot user not configured or not found');
                return;
            }

            $actor->assertCan('useChatGPTAssistant', $discussion);

            $discussion->loadMissing(['firstPost', 'tags']);
            
            // 构建更丰富的提示
            $context = "话题标题: {$discussion->title}\n\n";
            $context .= "话题内容: " . Utils::removeFormatting($discussion->firstPost->parsed_content) . "\n\n";
            
            $prompt = <<<EOT
请根据以下话题内容生成一个详细的回复。

背景信息:
{$context}

要求:
1. 请提供详细的分析和见解
2. 回答要全面且深入
3. 至少包含3-4个重点内容
4. 回应要有实用价值
5. 保持专业且友好的语气
6. 回复长度至少200字
7. 使用背景信息相同的语言回答
8. 如果是提问,请直接给出答案
9. 如果是讨论,请给出建设性意见
10. 适当使用分段提高可读性

请生成回复:

EOT;

            try {
                
                $response = $this->processResponse($prompt, [
                    'discussion_id' => $discussion->id,
                    'actor_id' => $actor->id
                ]);

                if (empty($response) || mb_strlen($response) < 200) {
                    $this->logger->warning('AI response too short', [
                        'discussion_id' => $discussion->id,
                        'length' => mb_strlen($response ?? '')
                    ]);
                    return;
                }
                
                $cacheKey = "chatgpt_replied_discussion_{$discussion->id}";
                if (app('cache')->has($cacheKey)) {
                    $this->logger->debug('Already replied to this discussion');
                    return;
                }
               
                $replyPost = $this->createReplyPost(
                    $response,
                    $discussion,
                    $actor,
                    $botUser
                );

                app('cache')->put($cacheKey, true, Carbon::now()->addMinutes(5));

                $this->logger->info('Auto-reply posted successfully', [
                    'post_id' => $replyPost->id,
                    'discussion_id' => $discussion->id,
                    'response_length' => mb_strlen($response)
                ]);

            } catch (\Throwable $e) {
                $this->logError('Auto-reply generation failed', $e, [
                    'discussion_id' => $discussion->id,
                    'actor_id' => $actor->id
                ]);
            }
        } catch (\Throwable $e) {
            $this->logError('Handle event failed', $e, [
                'discussion_id' => $discussion->id ?? null,
                'actor_id' => $actor->id ?? null
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
            ->orderBy('number', 'asc')  
            ->limit(5)
            ->get();

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
6. 使用相同的语言回答

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
6. Answer in the same language.

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
        $lastPost = $discussion->posts()->orderBy('number', 'desc')->first();
        $nextNumber = $lastPost ? $lastPost->number + 1 : 1;
        
        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $botUser->id,
            $actor
        );
        
        $post->number = $nextNumber;
        $post->created_at = Carbon::now();
        
        // 禁用事件分发器
        $post->unsetEventDispatcher();
        $post->save();
        
        // 更新讨论的最后回复信息
        $discussion->refreshCommentCount();
        $discussion->last_posted_at = $post->created_at;
        $discussion->last_posted_user_id = $botUser->id;
        $discussion->last_post_id = $post->id;
        $discussion->last_post_number = $post->number;
        $discussion->save();
        
        $this->logger->debug('Reply post created and discussion updated', [
            'post_id' => $post->id,
            'discussion_id' => $discussion->id,
            'post_number' => $post->number,
            'bot_id' => $botUser->id,
            'content_length' => mb_strlen($content),
            'comment_count' => $discussion->comment_count
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

            $replyPost = $this->createReplyPost(
                $response,
                $discussion,
                $event->actor,
                $botUser
            );

            $this->logger->info('Response posted successfully', [
                'post_id' => $replyPost->id,
                'post_number' => $replyPost->number,
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