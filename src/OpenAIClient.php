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

        // 使用通用的HTTP客户端替代OpenAI SDK
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
            $response = $this->client->post('/v1/chat/completions', [
                'json' => [
                    'model' => $this->settings->get('datlechin-chatgpt.model', 'gpt-3.5-turbo'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => '你是一个有帮助的论坛助手'
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
                throw new \Exception('无效的API响应格式: ' . json_encode($response));
            }
            
            return trim($response['choices'][0]['message']['content']);

        } catch (\Throwable $e) {
            $this->logger->error('API请求失败: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
