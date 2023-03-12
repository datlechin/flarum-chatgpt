<?php

namespace Datlechin\FlarumChatGPT\Listener;

use Carbon\Carbon;
use Datlechin\FlarumChatGPT\OpenAI;
use Flarum\Discussion\Event\Started;
use Flarum\Foundation\DispatchEventsTrait;
use Flarum\Post\CommentPost;
use Flarum\Post\Event\Saving;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Illuminate\Contracts\Events\Dispatcher;

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
    public function handle(Started $event)
    {
        if (! $this->settings->get('datlechin-chatgpt.enable_on_discussion_started', true)) {
            return;
        }

        $discussion = $event->discussion;
        $actor = $event->actor;

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $firstPost = $discussion->firstPost;

        $content = $this->openAI->completions($firstPost->content);

        if (! $content) {
            return;
        }

        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $actor->id,
            null,
        );

        $post->created_at = Carbon::now();

        $this->events->dispatch(new Saving($post, $actor));

        $post->save();
    }
}
