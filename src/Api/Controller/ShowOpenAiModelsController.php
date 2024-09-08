<?php

namespace Datlechin\FlarumChatGPT\Api\Controller;

use Datlechin\FlarumChatGPT\OpenAIClient;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Cache\Store;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ShowOpenAiModelsController implements RequestHandlerInterface
{
    public function __construct(
        protected OpenAIClient $client,
        protected Store $cache,
        protected SettingsRepositoryInterface $settings
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->cache->get('datlechin-chatgpt.models') ?: [];

        if (empty($data) && $this->settings->get('datlechin-chatgpt.api_key')) {
            $data = $this->client->models()->list()->data;
            $this->cache->put('datlechin-chatgpt.models', $data, 60 * 60);
        }

        return new JsonResponse($data);
    }
}
