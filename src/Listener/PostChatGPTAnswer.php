<?php

namespace Kubano09\ChatgptWriter\Listener;

use Carbon\Carbon;
use Kubano09\ChatgptWriter\OpenAIClient;
use Flarum\Discussion\Event\Started;
use Flarum\Post\CommentPost;
use Flarum\Post\PostRepository;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;

class PostChatGPTAnswer
{
    public function __construct(
        protected SettingsRepositoryInterface $settings,
        protected OpenAIClient $client,
        protected PostRepository $posts
    ) {
    }

    /**
     * Listener para el evento de discusión creada.
     * Registra este listener en extend.php con:
     * (new Extend\Event())->listen(Started::class, [PostChatGPTAnswer::class, 'handle'])
     */
    public function handle(Started $event): void
    {
        $discussion = $event->discussion;
        $actor      = $event->actor;

        // 1) ¿Está activada la autorrespuesta?
        $enabled = (bool) $this->settings->get('kubano09-chatgpt-writer.autoreply_enabled', false);
        if (!$enabled) {
            return;
        }

        // 2) ¿El usuario tiene permiso para que se use la IA?
        if (!$actor->hasPermission('kubano09-chatgpt-writer.use')) {
            return;
        }

        // 3) Evita bucles: no contestes si el autor es el propio bot (si tienes un user bot)
        $botUserId = (int) $this->settings->get('kubano09-chatgpt-writer.bot_user_id', 0);
        if ($botUserId && $actor->id === $botUserId) {
            return;
        }

        // 4) Construye el prompt con título y primer post
        $title = $discussion->title ?? '';
        $firstPost = $this->posts->query()->where('discussion_id', $discussion->id)->orderBy('created_at', 'asc')->first();
        $body = $firstPost?->content ?? '';

        $systemPrompt = (string) $this->settings->get(
            'kubano09-chatgpt-writer.system_prompt',
            'Eres un asistente que responde con cortesía y claridad a preguntas en un foro de drones. Sé breve y útil.'
        );

        $maxTokens   = (int) $this->settings->get('kubano09-chatgpt-writer.max_tokens', 400);
        $temperature = (float) $this->settings->get('kubano09-chatgpt-writer.temperature', 0.7);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => "Título: {$title}\n\nContenido:\n{$body}\n\nResponde de forma breve y útil."]
        ];

        // 5) Llama a OpenAI
        try {
            $response = $this->client->chat($messages, null, $maxTokens, $temperature);

            // Extrae el texto (según SDK puede ser choices[0].message.content)
            $content = $response['choices'][0]['message']['content'] ?? '';
            $content = trim((string) $content);
            if ($content === '') {
                return;
            }
        } catch (\Throwable $e) {
            // Log opcional: \Log::error('ChatGPT auto-reply error: '.$e->getMessage());
            return;
        }

        // 6) Decide el autor del post (botUser si existe, si no el actor)
        $authorId = $botUserId ?: $actor->id;
        /** @var User|null $user */
        $user = $authorId ? User::find($authorId) : null;

        // 7) Publica la respuesta
        $post = CommentPost::reply(
            $discussion->id,
            $content,
            $user?->id ?? $actor->id,
            null
        );

        // Opcional: ajustar la hora de creación
        $post->created_at = Carbon::now();

        $post->save();
    }
}
