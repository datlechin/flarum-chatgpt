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
        $models = explode(',', $this->settings->get('datlechin-chatgpt.available_models', 'gpt-3.5-turbo,gpt-4'));
        
        return new JsonResponse([
            'data' => array_map('trim', $models)
        ]);
    }
}
