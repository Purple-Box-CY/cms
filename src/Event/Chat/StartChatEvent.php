<?php

namespace App\Event\Chat;

use App\Entity\PrivateChannel;

class StartChatEvent
{
    public const NAME = 'app.chat.start';

    public function __construct(
        private PrivateChannel $privateChannel,
    ) {
    }

    public function getPrivateChannel(): PrivateChannel
    {
        return $this->privateChannel;
    }
}
