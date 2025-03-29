<?php

namespace Datlechin\FlarumChatGPT\Jobs;

use Carbon\Carbon;
use Datlechin\FlarumChatGPT\OpenAIClient;
use Flarum\Discussion\Discussion;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

class GenerateAIResponseJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected string $prompt;

    public function __construct(
        protected int $discussionId,
        protected int $actorId, 
        protected ?int $userId = null,
        protected ?string $prompt = null  
    ) {
    }

    public function handle(OpenAIClient $client, LoggerInterface $logger)
    {
        try {
            $logger->info('Start processing AI responses', ['discussion' => $this->discussionId]);
            
            $discussion = Discussion::findOrFail($this->discussionId);
            $discussion->loadMissing(['firstPost', 'posts']);

            $actor = User::findOrFail($this->actorId);
            $user = $this->userId ? User::find($this->userId) : null;

            if (!$this->prompt) {
                $this->prompt = "{$discussion->title}\n{$discussion->firstPost->content}";
            }

            $logger->debug('Calling the OpenAI API', [
                'title' => $discussion->title,
                'content' => $discussion->firstPost->content,
                'model' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.model')
            ]);

            $content = $client->completions($this->prompt);

            if (empty($content)) {
                $logger->warning('OpenAI API returns empty content', [
                    'model' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.model'),
                    'max_tokens' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.max_tokens')
                ]);
                return;
            }

            $actor->assertCan('useChatGPTAssistant', $discussion);

            $post = CommentPost::reply(
                $discussion->id,
                $content,
                $user->id ?? $actor->id,
                null
            );

            $post->created_at = Carbon::now();
            if (!$post->save()) {
                $logger->error('Failed to save', ['discussion' => $this->discussionId]);
                return;
            }

            $discussion = $post->discussion;
            $discussion->last_posted_at = $post->created_at;
            $discussion->last_posted_user_id = $post->user_id;
            $discussion->last_post_id = $post->id;
            $discussion->last_post_number = $post->number;
            $discussion->comment_count = $discussion->comment_count + 1;
            $discussion->save();

        } catch (\Throwable $e) {
            $logger->error('AI reply generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'discussion' => $this->discussionId
            ]);
            throw $e;
        }
    }

    public function retryUntil()
    {
        return Carbon::now()->addMinutes(5);
    }
}
