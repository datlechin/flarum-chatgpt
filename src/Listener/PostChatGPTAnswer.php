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
// 修改构造函数注入日志接口
use Psr\Log\LoggerInterface;

class PostChatGPTAnswer
{
    public function __construct(
        protected Dispatcher $events,
        protected SettingsRepositoryInterface $settings,
        protected OpenAIClient $client,
        protected LoggerInterface $logger
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

        // 确保关联关系已加载（新增部分）
        $discussion->loadMissing(['firstPost', 'tags']);

        // 删除所有队列相关代码
        // 增加直接同步调用
        try {
            // 修改后的同步调用
            $job = new \Datlechin\FlarumChatGPT\Jobs\GenerateAIResponseJob(
                $discussion->id,
                $actor->id,
                $user->id ?? null
            );
            
            $job->handle(
                resolve(OpenAIClient::class),
                resolve(LoggerInterface::class)
            );

            $this->logger->info('AI回复已同步生成');

        } catch (\Throwable $e) {
            $this->logger->error('AI回复生成失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
