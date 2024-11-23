<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserPhotoProfileRepository;
use App\Service\Infrastructure\S3Service;
use App\Service\Infrastructure\ServerService;
use Symfony\Component\HttpFoundation\File\File;

class UserImageService
{
    public function __construct(
        private ServerService  $serverService,
        private S3Service      $s3Service,
        private ImageService   $imageService,
        private UserPhotoProfileRepository $photoProfileRepository,
    ) {
    }


    public function uploadPhotoProfileToCDN(User $user): void
    {
        $file = $user->getPhotoProfileFile();
        $photoProfileData = $user->getPhotoProfileData();

        if (!$file) {
            return;
        }

        $newFilePath = trim(sprintf('%s/%s', $this->imageService->getUserPhotoProfileUploadDir(), $file), '/');
        $newFileFullPath = sprintf('%s/%s', $this->serverService->getProjectDir(), $newFilePath);

        if (!file_exists($newFileFullPath)) {
            return;
        }

        $fileObject = new File($newFileFullPath);
        $res = $this->s3Service->uploadFileToCDN(
            file: $fileObject,
            newFilePath: $newFilePath,
            options: [
                'params' => [
                    'ContentType' => 'image/jpg',
                ],
            ],
        );

        unlink($newFileFullPath);

        $photoProfileData->setOriginal($newFilePath);
        $this->photoProfileRepository->save($photoProfileData);
    }
}