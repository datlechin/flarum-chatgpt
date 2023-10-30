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
use Flarum\User\User;
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
    public function handle(Started $event): void
    {
        if (! $this->settings->get('datlechin-chatgpt.enable_on_discussion_started', true)) {
            return;
        }

        $discussion = $event->discussion;


        /** get discussion's all tag,if one of them is is_chatgpt,go on , or return*/
        $discussionTags = $discussion->tags;
        $ischatgpt = false;
        foreach ($discussionTags as $discussionTag) {
            if ((bool) $discussionTag->is_chatgpt) {
                $ischatgpt = true;
                break;
            }
        }
        if (!$ischatgpt) return;

        $actor = $event->actor;

        if ($userId = $this->settings->get('datlechin-chatgpt.user_prompt')) {
            $user = User::find($userId);
        }

        $userPromptId = $user->id ?? $actor->id;

        $actor->assertCan('useChatGPTAssistant', $discussion);

        $firstPost = $discussion->firstPost;

        $content = $this->openAI->completions($firstPost->title.$firstPost->content);

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
