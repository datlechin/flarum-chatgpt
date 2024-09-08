<?php

namespace Datlechin\FlarumChatGPT;

use Exception;
use Flarum\Settings\SettingsRepositoryInterface;
use OpenAI;
use OpenAI\Client;
use OpenAI\Resources\Models;
use Psr\Log\LoggerInterface;

class OpenAIClient
{
    public ?Client $client = null;

    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('datlechin-chatgpt.api_key');

        if (empty($apiKey)) {
            $this->logger->error('OpenAI API key is not set.');
            return;
        }

        $this->client = OpenAI::client($apiKey);
    }

    public function completions(string $content = null): ?string
    {
        try {
            $result = $this->client->completions()->create([
                'model' => $this->settings->get('datlechin-chatgpt.model', 'text-davinci-003'),
                'prompt' => $content,
                'max_tokens' => (int) $this->settings->get('datlechin-chatgpt.max_tokens', 100),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            return null;
        }

        return $result->choices[0]->text;
    }

    public function models(): Models
    {
        return $this->client->models();
    }
}
