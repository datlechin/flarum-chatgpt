<?php

namespace Flarum\OpenAI;

use OpenAI;
use Flarum\Settings\SettingsRepositoryInterface;

class OpenAIClient
{
    private $client;
    private $settings;

    public function __construct(string $apiKey, SettingsRepositoryInterface $settings)
    {
        $this->client = OpenAI::client($apiKey);
        $this->settings = $settings;
    }

    public function completions(string $prompt, int $maxTokens = null, float $temperature = null): array
    {
        $model = $this->settings->get('datlechin-chatgpt.model', 'gpt-3.5-turbo');
        $maxTokens = $maxTokens ?? (int) $this->settings->get('datlechin-chatgpt.max_tokens', 150);
        $temperature = $temperature ?? (float) $this->settings->get('datlechin-chatgpt.temperature', 0.7);

        $response = $this->client->chat()->completions()->create([
            'model' => $model,
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
