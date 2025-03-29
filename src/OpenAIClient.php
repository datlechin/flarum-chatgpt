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
    public function __construct(protected SettingsRepositoryInterface $settings, protected LoggerInterface $logger)
    {
        $apiKey = $this->settings->get('datlechin-chatgpt.api_key');
        $apiBase = $this->settings->get('datlechin-chatgpt.api_base');
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $apiBase,
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function completions(string $prompt)
    {
        try {
            
            $detectResponse = $this->client->post('/v1/chat/completions', [
                'json' => [
                    'model' => $this->settings->get('datlechin-chatgpt.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a language detector. Only respond with the detected language name in English, nothing else.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.1,
                    'max_tokens' => 10,
                ]
            ]);

            $detectResult = json_decode($detectResponse->getBody(), true);
            $detectedLanguage = trim($detectResult['choices'][0]['message']['content']);

            
            $response = $this->client->post('/v1/chat/completions', [
                'json' => [
                    'model' => $this->settings->get('datlechin-chatgpt.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You MUST respond in {$detectedLanguage} language ONLY. Do not use any other language."
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => (int) $this->settings->get('datlechin-chatgpt.max_tokens', 100),
                ]
            ]);

            $response = json_decode($response->getBody(), true);
            
            if (!isset($response['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid API response format: ' . json_encode($response));
            }
            
            return trim($response['choices'][0]['message']['content']);

        } catch (\Throwable $e) {
            $this->logger->error('API request failed: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
