<?php

namespace App\Service\Infrastructure;

use Exception;
use FFMpeg\Media\AdvancedMedia;
use FFMpeg\Filters\AdvancedMedia\ComplexFilterInterface;

class OurMultithreadVideoFilter implements ComplexFilterInterface
{
    public function __construct(
        private readonly array $sizes,
    )
    {
    }

    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return 'Our Filter';
    }

    public function getMinimalFFMpegVersion(): string
    {
        return '0.3';
    }

    private function generateFFmpegFilterComplex() {
        $splitParts = [];
        $filterParts = [];

        foreach ($this->sizes as $index => $size) {
            // Проверяем, что размеры заданы корректно
            if (count($size) != 2) {
                throw new Exception("Invalid size format. Sizes should be in the format [width, height].");
            }

            $width = $size[0];
            $height = $size[1];
            $vIndex = $index + 1;

            $filterParts[] = sprintf(
                '[v%d]scale=w=%d:h=%d:force_original_aspect_ratio=decrease,pad=ceil(iw/2)*2:ceil(ih/2)*2[v%dout]',
                $vIndex, $width, $height, $vIndex
            );
            $splitParts[] = sprintf('[v%d]', $vIndex);
        }

        $splitFilter = sprintf('[0:v]split=%d%s;', count($this->sizes), implode('', $splitParts));

        return $splitFilter . implode(';', $filterParts);
    }

    public function applyComplex(AdvancedMedia $media): array
    {
        return [
            '-filter_complex', $this->generateFFmpegFilterComplex(),
        ];
    }

    public function getInLabels(): string
    {
        return '';
    }

    public function getOutLabels(): string
    {
        return '';
    }
}
