<?php

/*
 * This file is part of datlechin/flarum-chatgpt.
 *
 * Copyright (c) 2023 Ngo Quoc Dat.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Datlechin\FlarumChatGPT;

use Datlechin\FlarumChatGPT\Access\DiscussionPolicy;
use Datlechin\FlarumChatGPT\Listener\PostChatGPTAnswer;
use Flarum\OpenAI\OpenAIClient;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Started;
use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    (new Extend\Routes('api'))
        ->get('/datlechin-chatgpt/models', 'datlechin-chatgpt.models', Api\Controller\ShowOpenAiModelsController::class),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\ServiceProvider())
        ->register(function ($container) {
            $container->singleton(OpenAIClient::class, function ($container) {
                $settings = $container->make('Flarum\Settings\SettingsRepositoryInterface');
                $apiKey = $settings->get('datlechin-chatgpt.api_key');
                
                if (empty($apiKey)) {
                    throw new \Exception('OpenAI API key is not set.');
                }
                
                return new OpenAIClient($apiKey, $settings);
            });
        }),

    (new Extend\Settings())
        ->default('datlechin-chatgpt.model', 'gpt-3.5-turbo')
        ->default('datlechin-chatgpt.enable_on_discussion_started', true)
        ->default('datlechin-chatgpt.max_tokens', 100)
        ->default('datlechin-chatgpt.temperature', 0.7)
        ->default('datlechin-chatgpt.user_prompt_badge_text', 'Assistant')
        ->serializeToForum('chatGptUserPromptId', 'datlechin-chatgpt.user_prompt')
        ->serializeToForum('chatGptBadgeText', 'datlechin-chatgpt.user_prompt_badge_text'),

    (new Extend\Event())
        ->listen(Started::class, PostChatGPTAnswer::class),

    (new Extend\Policy())
        ->modelPolicy(Discussion::class, DiscussionPolicy::class),
];
