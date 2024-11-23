<?php

namespace App\Event\MysteryBox;

use App\Entity\MysteryBox;

class MysteryBoxApprovedEvent
{
    public const NAME = 'app.mystery_box.approved';

    public function __construct(
        private MysteryBox $mysteryBox
    ) {
    }

    public function getMysteryBox(): MysteryBox
    {
        return $this->mysteryBox;
    }
}
