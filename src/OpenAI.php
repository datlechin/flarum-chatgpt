<?php

namespace Datlechin\FlarumChatGPT;

use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use OpenAI as OpenAIClient;
use OpenAI\Client;

class OpenAI
{
    protected SettingsRepositoryInterface $settings;

    protected ?string $apiKey = null;

    public ?Client $client = null;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $this->apiKey = $this->settings->get('datlechin-chatgpt.api_key');
        $this->client = OpenAIClient::client($this->apiKey);
    }

    public function completions(string $content = null): ?string
    {
        if (! $this->apiKey) {
            return null;
        }

        $maxTokens = (int) $this->settings->get('datlechin-chatgpt.max_tokens', 100);

        try {
            $result = $this->client->completions()->create([
                'model' => $this->settings->get('datlechin-chatgpt.model', 'text-davinci-003'),
                'prompt' => $content,
                'max_tokens' => $maxTokens,
            ]);
        } catch (Exception $e) {
            return null;
        }

        return $result->choices[0]->text;
    }
}
