<?php

namespace App\Service;

use App\Service\Infrastructure\LogService;
use App\Service\Infrastructure\Media\MediaThreads;
use FFMpeg\Media\AdvancedMedia;
use App\Service\Infrastructure\FFmpegService;
use App\Service\Infrastructure\OurVideoFilter;
use FFMpeg\Filters\FiltersCollection;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use App\Service\Infrastructure\OurVideoFormat;
use App\Service\Infrastructure\OurMultithreadVideoFilter;

class MediaService
{
    private const MAX_SIZE_SEGMENT = 600000;
    private const THREADS          = [
        [
            'crf' => 28,
            'w'   => 1080,
            'h'   => 1920,
        ],
        [
            'crf' => 24,
            'w'   => 720,
            'h'   => 1280,
        ],
        [
            'crf' => 24,
            'w'   => 480,
            'h'   => 854,
        ],
    ];

    public function __construct(
        private FFmpegService $ffmpegServise,
        private LogService    $logger,
    ) {
    }

    private function generateMasterPlaylist(
        string $videoFolderPath,
        array  $threads,
        string $playListFileName = null
    ): void {
        $masterPlaylist = "#EXTM3U\n";
        foreach ($threads as $THREAD) {
            $crf = $THREAD['crf'];
            $w = $THREAD['w'];
            $h = $THREAD['h'];
            $segmentFileName = sprintf('%s/segment_%sp_crf%s_000.ts', $videoFolderPath, $w, $crf);
            $size = filesize($segmentFileName);
            $bandwidth = $size * 8 / OurVideoFormat::HLS_TIME;
            $resolution = sprintf('%sx%s', $w, $h);
            $masterPlaylist .= sprintf("#EXT-X-STREAM-INF:BANDWIDTH=%s,RESOLUTION=%s\n", $bandwidth, $resolution);
            $masterPlaylist .= $this->generatePlaylistFilename($w, $crf)."\n";
        }
        file_put_contents(sprintf('%s/%s', $videoFolderPath, $playListFileName), $masterPlaylist);
    }

    private function generatePlaylistFilename(string $w, int $crf)
    {
        return sprintf('playlist_%sp_crf%s.m3u8', $w, $crf);
    }

    public function convertVideo(
        string $rawFilePath,
        string $videoFolderPath,
        string $playlistOutputPath,
        string $playlistFileName,
        bool   $multithreading = false,
    ): AdvancedMedia|Video {
        if ($multithreading) {
            //$filters = new FiltersCollection();
            //$sizes = [];
            //$video = $this->ffmpegServise->openAdvanced($rawFilePath);
            //foreach ($threads as $i => $THREAD) {
            //    $crf = $THREAD['crf'];
            //    $w = $THREAD['w'];
            //    $h = $THREAD['h'];
            //    $segmentFileName = sprintf('%s/segment_%sp_crf%s_%%03d.ts', $videoFolderPath, $w, $crf);
            //    $output = sprintf('%s/%s', $videoFolderPath, $this->generatePlaylistFilename($w, $crf));
            //    $format = new OurVideoFormat($segmentFileName, $crf);
            //    $stream = sprintf('[v%sout]', $i + 1);
            //    $video->map([$stream, '0:a',], $format, $output);
            //    $sizes[] = [$w, $h];
            //}
            //$filters->add(new OurMultithreadVideoFilter($sizes));
            //$video->setFiltersCollection($filters);
            //$command = $video->getFinalCommand();
            //$video->save();

            $threads = null;
            foreach (MediaThreads::THREADS as $threadSize => $threads) {
                $this->logger->debug(sprintf('Thread size %s', $threadSize),
                    [
                        'video_folder_path' => $videoFolderPath,
                    ]);

                $thread = $threads[MediaThreads::SIZE_1080_1920];

                //пробуем сконвертировать только в высоком разрешении
                $video = $this->generateMultithreadingSegments($rawFilePath, $videoFolderPath, [$thread]);

                //проверяем, что файл 0-сегмента меньше 600кб
                $w = $thread['w'];
                $crf = $thread['crf'];
                $segment0path = sprintf('%s/segment_%sp_crf%s_000.ts', $videoFolderPath, $w, $crf);
                $segment0size = filesize($segment0path);
                $segmentMaxSize = self::MAX_SIZE_SEGMENT;

                $segment0sizeIsGood = $segment0size <= $segmentMaxSize;
                $logMessage = $segment0sizeIsGood
                    ? 'Size of the 0th segment is good'
                    : 'Size of the 0th segment is larger than necessary';

                $this->logger->debug($logMessage,
                    [
                        'segment_path'     => $segment0path,
                        'segment_size'     => $segment0size,
                        'segment_max size' => $segmentMaxSize,
                    ]);

                //удалим файлы первого сегмента
                array_map('unlink', glob(sprintf('%s/segment_%sp_crf%s_*.ts', $videoFolderPath, $w, $crf)));
                array_map('unlink', glob(sprintf('%s/playlist_*.m3u8', $videoFolderPath)));

                //если файл больше, пробуем взять другие настройки тредов
                if (!$segment0sizeIsGood && $threadSize !== MediaThreads::SIZE_XS) {
                    continue;
                }

                //если резмер 0-сегмента устраивает, конвертируем для всех разрешений этого сета
                $this->logger->debug(sprintf('Convert finish thread size %s', $threadSize),
                    [
                        'video_folder_path' => $videoFolderPath,
                    ]);

                $video = $this->generateMultithreadingSegments($rawFilePath, $videoFolderPath, $threads);
                break;
            }

            $this->generateMasterPlaylist($videoFolderPath, $threads, $playlistFileName);
        } else {
            $filters = new FiltersCollection();
            $format = new X264();
            $format->setPasses(1);
            $video = $this->ffmpegServise->open($rawFilePath);
            $filters->add(new OurVideoFilter($videoFolderPath));
            $video->setFiltersCollection($filters);
            $command = $video->getFinalCommand($format, $playlistOutputPath);
            try {
                $video->save($format, $playlistOutputPath);
            } catch (\Exception $e) {
                $this->logger->error('Failed to convert video',
                    [
                        'command' => $command,
                        'error'   => $e->getMessage(),
                    ]);

                throw $e;
            }
        }

        return $video;
    }

    public function getDurationVideo(string $rawFilePath): int
    {
        try {
            $open = $this->ffmpegServise->open($rawFilePath);
            $format = $open->getFormat();
            $duration = $format->get('duration');

            return floor($duration);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to get duration',
                [
                    'error'     => $e->getMessage(),
                    'file_path' => $rawFilePath,
                ]);

            return 0;
        }
    }

    private function generateMultithreadingSegments(
        string $rawFilePath,
        string $videoFolderPath,
        array  $threads,
    ): AdvancedMedia|Video {
        $filters = new FiltersCollection();
        $sizes = [];
        $video = $this->ffmpegServise->openAdvanced($rawFilePath);
        $i = 1;
        foreach ($threads as $THREAD) {
            $crf = $THREAD['crf'];
            $w = $THREAD['w'];
            $h = $THREAD['h'];
            $segmentFileName = sprintf('%s/segment_%sp_crf%s_%%03d.ts', $videoFolderPath, $w, $crf);
            $output = sprintf('%s/%s', $videoFolderPath, $this->generatePlaylistFilename($w, $crf));
            $format = new OurVideoFormat($segmentFileName, $crf);
            $stream = sprintf('[v%sout]', $i++);
            $video->map([$stream, '0:a?',], $format, $output);
            $sizes[] = [$w, $h];
        }
        $filters->add(new OurMultithreadVideoFilter($sizes));
        $video->setFiltersCollection($filters);
        $command = $video->getFinalCommand();
        $this->logger->debug('Try ffmpeg command',
            [
                'rawFilePath'     => $rawFilePath,
                'videoFolderPath' => $videoFolderPath,
                'threads'         => $threads,
                'command'         => $command,
            ]);

        try {
            $video->save();
        } catch (\Exception $e) {
            $this->logger->error('Failed to convert multithreading video',
                [
                    'command' => $command,
                    'error'   => $e->getMessage(),
                ]);

            throw $e;
        }

        return $video;
    }
}