<?php

namespace Datlechin\FlarumChatGPT\Listener;

use Carbon\Carbon;
use Flarum\OpenAI\OpenAIClient;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostChatGPTAnswer
{
    public function __construct(
        protected PostRepository $posts,
        protected SettingsRepositoryInterface $settings,
        protected Dispatcher $events,
        protected OpenAIClient $client
    ) {
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
            $tagIds = Arr::pluck($discussion->tags, 'id');

            if (! array_intersect($enabledTagIds, $tagIds)) {
                return;
            }
        }

        if ($userId = $this->settings->get('datlechin-chatgpt.user_prompt')) {
            $user = User::find($userId);
        }

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $response = $this->client->completions($discussion->firstPost->content);
        
        if (empty($response) || !isset($response['choices'][0]['message']['content'])) {
            return;
        }

        $content = $response['choices'][0]['message']['content'];

        if (! $content) {
            return;
        }

        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $user->id ?? $actor->id,
            null,
        );

        $post->created_at = Carbon::now();

        $post->save();
    }
}
