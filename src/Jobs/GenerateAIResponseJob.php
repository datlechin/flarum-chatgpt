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

// 在文件顶部添加正确的接口引用
use Psr\Log\LoggerInterface;

class GenerateAIResponseJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected int $discussionId,
        protected int $actorId,
        protected ?int $userId = null
    ) {
    }

    // 修正参数类型声明
    public function handle(OpenAIClient $client, LoggerInterface $logger)
    {
        try {
            $logger->info('开始处理AI回复', ['discussion' => $this->discussionId]);
            
            $discussion = Discussion::findOrFail($this->discussionId);
            $discussion->loadMissing(['firstPost', 'posts']);

            $actor = User::findOrFail($this->actorId);
            $user = $this->userId ? User::find($this->userId) : null;

            // 修改为组合标题和内容
            // 修改提示词模板
            $prompt = "请完整回答以下问题：\n标题：{$discussion->title}\n内容：{$discussion->firstPost->content}\n直接回答即可，尽可能的详细，内容只包含答案。";

            $logger->debug('调用OpenAI API', [
                'title' => $discussion->title, // 新增标题记录
                'content' => $discussion->firstPost->content,
                'model' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.model')
            ]);

            // 修改API调用参数
            $content = $client->completions($prompt);

            if (empty($content)) {
                $logger->warning('OpenAPI返回空内容', [
                    'model' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.model'),
                    'max_tokens' => app(SettingsRepositoryInterface::class)->get('datlechin-chatgpt.max_tokens')
                ]);
                return;
            }

            // 保留原有的权限检查
            $actor->assertCan('useChatGPTAssistant', $discussion);

            // 删除此处重复的 $content = $client->completions(...)
            // if ($content) { 修改为：
            $post = CommentPost::reply(
                $discussion->id,
                $content, // 使用已获取的content
                $user->id ?? $actor->id,
                null
            );

            $post->created_at = Carbon::now();
            if (!$post->save()) {
                $logger->error('回复保存失败', ['discussion' => $this->discussionId]);
                return;
            }

            // 更新discussion的最后回复信息
            $discussion = $post->discussion;
            $discussion->last_posted_at = $post->created_at;
            $discussion->last_posted_user_id = $post->user_id;
            $discussion->last_post_id = $post->id;
            $discussion->last_post_number = $post->number;
            $discussion->save();

        } catch (\Throwable $e) {
            $logger->error('AI回复生成失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'discussion' => $this->discussionId
            ]);
            throw $e; // 保留异常传播
        }
    }

    public function retryUntil()
    {
        // 修改前：使用未定义的now()
        // return now()->addMinutes(5);
        
        // 修改后：使用Carbon静态方法
        return Carbon::now()->addMinutes(5);
    }
}
