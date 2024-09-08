<?php

namespace Datlechin\FlarumChatGPT\Listener;

use Carbon\Carbon;
use Datlechin\FlarumChatGPT\OpenAIClient;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostChatGPTAnswer
{
    public function __construct(
        protected Dispatcher $events,
        protected SettingsRepositoryInterface $settings,
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

        $content = $this->client->completions($discussion->firstPost->content);

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
