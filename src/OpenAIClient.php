<?php

namespace Kubano09\ChatgptWriter;

use OpenAI;
use Flarum\Settings\SettingsRepositoryInterface;

class OpenAIClient
{
    /** @var \OpenAI\Client */
    private $client;

    /** @var SettingsRepositoryInterface */
    private $settings;

    public function __construct(string $apiKey, SettingsRepositoryInterface $settings)
    {
        // Requiere el paquete: "openai-php/client": "^0.10"
        $this->client   = OpenAI::client($apiKey);
        $this->settings = $settings;
    }

    /**
     * Genera texto usando Chat Completions.
     *
     * @param array $messages Array de mensajes estilo OpenAI:
     *   [
     *     ['role' => 'system', 'content' => '...'],
     *     ['role' => 'user',   'content' => '...'],
     *     // etc.
     *   ]
     * @param string|null $model        (opcional) sobrescribe el modelo
     * @param int|null    $maxTokens    (opcional) sobrescribe max tokens
     * @param float|null  $temperature  (opcional) sobrescribe temperatura
     *
     * @return array Respuesta completa del API convertida a array
     */
    public function chat(array $messages, ?string $model = null, ?int $maxTokens = null, ?float $temperature = null): array
    {
        // Lee defaults desde la configuración de Flarum (Admin → Ajustes), con claves propias
        $model       = $model       ?? $this->settings->get('kubano09-chatgpt-writer.model', 'gpt-4o-mini');
        $maxTokens   = $maxTokens   ?? (int) $this->settings->get('kubano09-chatgpt-writer.max_tokens', 400);
        $temperature = $temperature ?? (float) $this->settings->get('kubano09-chatgpt-writer.temperature', 0.7);

        // Normaliza mensajes (por si vienen strings sueltos)
        $messages = array_map(function ($m) {
            if (is_string($m)) {
                return ['role' => 'user', 'content' => $m];
            }
            // Asegura claves mínimas
            return [
                'role'    => $m['role']    ?? 'user',
                'content' => $m['content'] ?? ''
            ];
        }, $messages);

        // Llama al endpoint de Chat Completions del SDK
        $response = $this->client->chat()->create([
            'model'       => $model,
            'messages'    => $messages,
            'max_tokens'  => $maxTokens,
            'temperature' => $temperature,
        ]);

        // Devuelve como array ‘plano’ (útil para serializar en JSON)
        return $response->toArray();
    }

    /**
     * Lista modelos disponibles (si el SDK/credenciales lo permiten).
     * Útil para poblar un selector en la página de ajustes.
     */
    public function models(): array
    {
        try {
            // Algunos SDKs exponen ->models()->list() / otros ->models()->retrieve()
            // Aquí devolvemos el array tal cual si está soportado.
            $res = $this->client->models()->list();
            return method_exists($res, 'toArray') ? $res->toArray() : (array) $res;
        } catch (\Throwable $e) {
            // Silencia si no está permitido listar modelos; devuelve vacío
            return [];
        }
    }
}
