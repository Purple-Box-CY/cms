<?php

namespace App\Service;

use App\Entity\Marker;
use App\Repository\MarkerRepository;
use App\Service\Infrastructure\S3Service;
use App\Service\Infrastructure\ServerService;
use App\Service\Utility\DomainHelper;
use Symfony\Component\HttpFoundation\File\File;

class MarkerService
{
    public function __construct(
        private S3Service        $s3Service,
        private ImageService     $imageService,
        private ServerService    $serverService,
        private MarkerRepository $markerRepository,
    ) {
    }

    public function uploadMarkerImageToCDN(Marker $marker): void
    {
        //$oldFile = $marker->getImageUrl();
        $newFile = $marker->getImageFile();

        if (!$newFile) {
            return;
        }

        $newFilePath = trim(sprintf('%s/%s', $this->imageService->getMarkersImageUploadDir(), $newFile), '/');
        $newFileFullPath = sprintf('%s/%s', $this->serverService->getProjectDir(), $newFilePath);
        $fileObject = new File($newFileFullPath);
        $options = [
            'params' => [
                'ContentType' => 'image/jpg',
            ],
        ];
        $this->s3Service->uploadFileToCDN(
            file: $fileObject,
            newFilePath: $newFilePath,
            options: $options,
        );
        //$this->s3Service->deleteFileFromCdn($oldFile);
        unlink($newFileFullPath);

        //todo save only path without domain
        $cdnFile = sprintf('%s/%s', DomainHelper::getCdnDomain(), $newFilePath);

        $marker->setImageUrl($cdnFile);
        $this->markerRepository->save($marker);
    }
}