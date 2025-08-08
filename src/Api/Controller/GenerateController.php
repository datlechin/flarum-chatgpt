<?php

namespace Kubano09\ChatgptWriter\Api\Controller;

use Flarum\Http\RequestUtil;
use Kubano09\ChatgptWriter\OpenAIClient;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GenerateController implements RequestHandlerInterface
{
    public function __construct(protected OpenAIClient $client) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertPermission('kubano09-chatgpt-writer.use');

        $body   = (array) $request->getParsedBody();
        $prompt = trim((string)($body['prompt'] ?? ''));
        if ($prompt === '') {
            return new JsonResponse(['error' => 'Prompt vacío'], 422);
        }

        try {
            $messages = [
                ['role' => 'system', 'content' => 'Eres un asistente que redacta textos claros y concisos para un foro.'],
                ['role' => 'user',   'content' => $prompt],
            ];
            $res  = $this->client->chat($messages);
            $text = $res['choices'][0]['message']['content'] ?? '';
            return new JsonResponse(['text' => (string) $text]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => 'Fallo generando texto', 'detail' => $e->getMessage()], 500);
        }
    }
}
