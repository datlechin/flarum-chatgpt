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
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Tags\Tag;
use Flarum\Discussion\Event\Started;
use Datlechin\FlarumChatGPT\Listener\PostChatGPTAnswer;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\Model(Tag::class))
        ->cast('is_chatgpt', 'boolean'),
    (new Extend\Settings())
        ->default('datlechin-chatgpt.model', 'text-davinci-003')
        ->default('datlechin-chatgpt.enable_on_discussion_started', true)
        ->default('datlechin-chatgpt.max_tokens', 100)
        ->default('datlechin-chatgpt.user_prompt_badge_text', 'Assistant')
        ->serializeToForum('chatGptUserPromptId', 'datlechin-chatgpt.user_prompt')
        ->serializeToForum('chatGptBadgeText', 'datlechin-chatgpt.user_prompt_badge_text'),
    (new Extend\Event())
        ->listen(TagCreating::class, TagCreating::class)
        ->listen(TagSaving::class, TagEditing::class),

    (new Extend\Event())
        ->listen(Started::class, PostChatGPTAnswer::class),
    (new Extend\Policy())
        ->modelPolicy(Discussion::class, DiscussionPolicy::class),
    (new Extend\ApiSerializer(TagSerializer::class))
        ->attributes(function (TagSerializer $serializer, Tag $tag, array $attributes) {
            $attributes['isQnA'] = (bool) $tag->is_qna;
            return $attributes;
        }),
];
