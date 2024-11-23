<?php

namespace App\Enums\Posts;

enum PostSocialTypeEnum: string
{
    case INSTAGRAM = 'instagram';
    case THREADS   = 'threads';
    case TWITTER   = 'twitter';
    case FACEBOOK  = 'facebook';
    case REDDIT    = 'reddit';
    case YOUTUBE   = 'youtube';
    case TIKTOK    = 'tiktok';

    public const array AVAILABLE_TYPES = [
        self::INSTAGRAM->value,
        self::THREADS->value,
        self::TWITTER->value,
        self::FACEBOOK->value,
        self::REDDIT->value,
        self::YOUTUBE->value,
        self::TIKTOK->value,
    ];

    public static function values(): array
    {
        $values = [];
        foreach (self::cases() as $status) {
            $values[$status->value]=$status->value;
        }
        return $values;
    }
}
