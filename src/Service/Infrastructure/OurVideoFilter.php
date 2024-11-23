<?php

namespace App\Service\Infrastructure;

use FFMpeg\Filters\Video\VideoFilterInterface;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video;

class OurVideoFilter implements VideoFilterInterface
{
    public function __construct(private string $videoFolder)
    {
    }

    public function getPriority()
    {
        return 0;
    }

    public function apply(Video $video, VideoInterface $format)
    {
        $segmentOutputPath = $this->videoFolder . '/' . 'segment_%03d.ts';

        return [
            '-c:v', 'libx264',
            '-c:a', 'aac',
            '-pix_fmt', 'yuv420p', //8bit
            '-profile', 'high',
            '-crf', '21',
            '-strict', 'experimental',
            '-hls_time', '2',
            '-force_key_frames', 'expr:gte(t,n_forced*2)',
            '-hls_list_size', '0',
            '-hls_segment_filename', $segmentOutputPath,
            '-map_metadata:g', '-1',
            '-map_metadata:s:v', '-1',
            '-map_metadata:s:a', '-1',
            '-map_chapters', '-1',
            '-movflags', 'faststart',
            '-vf', 'scale=w=1080:h=1920:force_original_aspect_ratio=decrease,pad=ceil(iw/2)*2:ceil(ih/2)*2'
        ];
    }
}
