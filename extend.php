<?php

namespace Kubano09\ChatgptWriter;

use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Started;
use Flarum\Extend;
use Kubano09\ChatgptWriter\Access\DiscussionPolicy;
use Kubano09\ChatgptWriter\Api\Controller\ShowOpenAiModelsController;
use Kubano09\ChatgptWriter\Api\Controller\GenerateController;
use Kubano09\ChatgptWriter\Listener\PostChatGPTAnswer;
use Kubano09\ChatgptWriter\Providers\ServiceProvider;

return [
    (new Extend\Frontend('forum'))->js(__DIR__ . '/js/dist/forum.js'),
    (new Extend\Frontend('admin'))->js(__DIR__ . '/js/dist/admin.js'),

    (new Extend\ServiceProvider())->register(ServiceProvider::class),

    (new Extend\Routes('api'))
        ->get('/chatgpt-writer/models', 'chatgpt-writer.models', ShowOpenAiModelsController::class)
        ->post('/chatgpt-writer/generate', 'chatgpt-writer.generate', GenerateController::class),

    (new Extend\Settings())
        ->default('kubano09-chatgpt-writer.model', 'gpt-4o-mini')
        ->default('kubano09-chatgpt-writer.max_tokens', 400)
        ->default('kubano09-chatgpt-writer.temperature', 0.7)
        ->default('kubano09-chatgpt-writer.autoreply_enabled', false)
        ->default('kubano09-chatgpt-writer.user_prompt_badge_text', 'Assistant'),

    (new Extend\Event())
        ->listen(Started::class, PostChatGPTAnswer::class),

    (new Extend\Policy())
        ->modelPolicy(Discussion::class, DiscussionPolicy::class),

    (new Extend\Permission())
        ->registerPermission('kubano09-chatgpt-writer.use', 'reply')
        ->icon('fas fa-wand-magic-sparkles'),
];
