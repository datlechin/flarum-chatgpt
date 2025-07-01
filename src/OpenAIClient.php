<?php

namespace Flarum\OpenAI;

use OpenAI;

class OpenAIClient
{
    private $client;

    public function __construct(string $apiKey)
    {
        $this->client = OpenAI::client($apiKey);
    }

    public function completions(string $prompt, int $maxTokens = 150, float $temperature = 0.7): array
    {
        $response = $this->client->chat()->completions()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
        ]);

        return $response->toArray();
    }

    public function models()
    {
        return $this->client->models();
    }
}
