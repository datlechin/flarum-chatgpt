<?php

namespace Kubano09\ChatgptWriter\Providers;

use Flarum\Foundation\AbstractServiceProvider;
use Flarum\Settings\SettingsRepositoryInterface;
use Kubano09\ChatgptWriter\OpenAIClient;

class ServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->container->singleton(OpenAIClient::class, function ($container) {
            /** @var SettingsRepositoryInterface $settings */
            $settings = $container->make(SettingsRepositoryInterface::class);

            // 1) Preferir ENV (recomendado)
            $apiKey = getenv('OPENAI_API_KEY') ?: '';

            // 2) Si no hay ENV, opción B: settings (si más adelante pones UI en admin)
            if (!$apiKey) {
                $apiKey = (string) $settings->get('kubano09-chatgpt-writer.api_key', '');
            }

            if (!$apiKey) {
                // Evita fallo duro: podrías lanzar excepción si prefieres
                $apiKey = 'missing-api-key';
            }

            return new OpenAIClient($apiKey, $settings);
        });
    }
}
