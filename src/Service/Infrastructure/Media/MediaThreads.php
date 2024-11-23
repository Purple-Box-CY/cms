<?php

namespace App\Service\Infrastructure\Media;

class MediaThreads
{
    public const SIZE_1080_1920 = '1080x1920';
    public const SIZE_720_1280 = '720x1280';
    public const SIZE_480_854 = '480x854';

    public const SIZE_L = 'L';
    public const SIZE_M = 'M';
    public const SIZE_S = 'S';
    public const SIZE_XS = 'XS';

    public const THREADS = [
        self::SIZE_L  => self::L,
        self::SIZE_M  => self::M,
        self::SIZE_S  => self::S,
        self::SIZE_XS => self::XS,
    ];

    public const L = [
        self::SIZE_480_854  => [
            'crf' => 36,
            'w'   => 480,
            'h'   => 854,
        ],
        self::SIZE_720_1280  => [
            'crf' => 24,
            'w'   => 720,
            'h'   => 1280,
        ],
        self::SIZE_1080_1920 => [
            'crf' => 28,
            'w'   => 1080,
            'h'   => 1920,
        ],
    ];

    public const M = [
        self::SIZE_480_854  => [
            'crf' => 36,
            'w'   => 480,
            'h'   => 854,
        ],
        self::SIZE_720_1280  => [
            'crf' => 28,
            'w'   => 720,
            'h'   => 1280,
        ],
        self::SIZE_1080_1920 => [
            'crf' => 30,
            'w'   => 1080,
            'h'   => 1920,
        ],
    ];

    public const S = [
        self::SIZE_480_854  => [
            'crf' => 36,
            'w'   => 480,
            'h'   => 854,
        ],
        self::SIZE_720_1280  => [
            'crf' => 30,
            'w'   => 720,
            'h'   => 1280,
        ],
        self::SIZE_1080_1920 => [
            'crf' => 32,
            'w'   => 1080,
            'h'   => 1920,
        ],
    ];

    public const XS = [
        self::SIZE_480_854  => [
            'crf' => 36,
            'w'   => 480,
            'h'   => 854,
        ],
        self::SIZE_720_1280  => [
            'crf' => 32,
            'w'   => 720,
            'h'   => 1280,
        ],
        self::SIZE_1080_1920 => [
            'crf' => 32,
            'w'   => 1080,
            'h'   => 1920,
        ],
    ];
}