<?php

namespace App\Service\Infrastructure;

use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\File;

class S3Service
{
    public function __construct(
        private S3Client   $s3Client,
        private string     $s3Bucket,
        private string     $cdnDomain,
        private LogService $logger,
    ) {
    }

    public function deleteFileFromCdn(?string $filePath)
    {
        if (!$filePath) {
            return;
        }
        try {
            $oldFileParams = parse_url($filePath);
            if (is_array($oldFileParams) && isset($oldFileParams['path'])) {
                $key = substr($oldFileParams['path'], 1);
                $this->s3Client->deleteMatchingObjects($this->s3Bucket, $key);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Failed to delete image from aws',
                [
                    'error'  => $e->getMessage(),
                    'method' => __METHOD__,
                ]);
        }
    }

    public function uploadFileToCDN(
        File   $file,
        string $newFilePath,
        array  $options = [],
    ): string {
        $res = $this->s3Client->upload(
            bucket: $this->s3Bucket,
            key: $newFilePath,
            body: $file->getContent(),
            options: $options,
        );

        return sprintf('%s/%s', $this->cdnDomain, $newFilePath);
    }
}
