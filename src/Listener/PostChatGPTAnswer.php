<?php

namespace Datlechin\FlarumChatGPT\Listener;

use Carbon\Carbon;
use Datlechin\FlarumChatGPT\OpenAI;
use Flarum\Discussion\Event\Started;
use Flarum\Foundation\DispatchEventsTrait;
use Flarum\Post\CommentPost;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\User\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostChatGPTAnswer
{
    use DispatchEventsTrait;

    protected $events;

    protected SettingsRepositoryInterface $settings;

    protected OpenAI $openAI;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, OpenAI $openAI)
    {
        $this->events = $events;
        $this->settings = $settings;
        $this->openAI = $openAI;
    }

    /**
     * @throws PermissionDeniedException
     */
    public function handle(Started $event): void
    {
        if (! $this->settings->get('datlechin-chatgpt.enable_on_discussion_started', true)) {
            return;
        }

        $discussion = $event->discussion;
        $actor = $event->actor;
        $enabledTagIds = $this->settings->get('datlechin-chatgpt.enabled-tags', []);

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

        $userPromptId = $user->id ?? $actor->id;

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $firstPost = $discussion->firstPost;

        $content = $this->openAI->completions($firstPost->content);

        if (! $content) {
            return;
        }

        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $userPromptId,
            null,
        );

        $post->created_at = Carbon::now();

        $post->save();
    }
}
