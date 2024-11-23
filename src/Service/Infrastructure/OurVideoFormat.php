<?php

namespace App\Service\Infrastructure;

use FFMpeg\Format\FormatInterface;

class OurVideoFormat implements FormatInterface
{
    public const HLS_TIME = 2;
    public function __construct(
        private readonly string $segmentFileName,
        private readonly int $crf,
    )
    {
    }

    public function getPasses(): int
    {
        return 1;
    }

    public function getExtraParams(): array
    {
        return [
            '-c:a', 'aac',
            '-c:v', 'libx264',
            '-movflags', 'faststart',
            '-pix_fmt', 'yuv420p',
            '-crf', $this->crf,
            '-hls_time', self::HLS_TIME,
            '-force_key_frames', 'expr:gte(t,n_forced*2)',
            '-hls_list_size', 0,
            '-hls_segment_filename', $this->segmentFileName,
        ];
    }
}