<?php

namespace App\Service\Infrastructure;

use FFMpeg\FFMpeg;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;
use FFMpeg\Media\AdvancedMedia;

class FFmpegService
{

    private FFMpeg $ffmpeg;

    public function __construct(
        string $ffmpegBinariesPath,
        string $ffprobeBinariesPath,
    ) {
        $ffmpegBinariesPath = $ffmpegBinariesPath ?? '/usr/bin/ffmpeg';
        $ffprobeBinariesPath = $ffprobeBinariesPath ?? '/usr/bin/ffprobe';

        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => $ffmpegBinariesPath,
            'ffprobe.binaries' => $ffprobeBinariesPath,
            'timeout'          => 0,
        ]);
    }

    private function getFFMpeg(): FFMpeg
    {
        return $this->ffmpeg;
    }

    public function open(string $pathfile): Audio|Video
    {
        return  $this->getFFMpeg()->open($pathfile);
    }

    public function openAdvanced(string $pathfile): AdvancedMedia
    {
        return  $this->getFFMpeg()->openAdvanced([$pathfile]);
    }
}
