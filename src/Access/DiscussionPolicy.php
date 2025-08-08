<?php

namespace Kubano09\ChatgptWriter\Access;

use Flarum\Discussion\Discussion;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class DiscussionPolicy extends AbstractPolicy
{
    protected SettingsRepositoryInterface $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Controla si el usuario puede usar el asistente de IA en discusiones.
     * El permiso se define como: "kubano09-chatgpt-writer.use"
     * Recuerda registrarlo en tu extender de permisos o a través de un extender personalizado.
     */
    public function useChatGPTAssistant(User $actor, Discussion $discussion): bool
    {
        return $actor->hasPermission('kubano09-chatgpt-writer.use');
    }
}
