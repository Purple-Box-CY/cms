<?php

namespace App\Service;

class ImageService
{
    public const USER_PHOTO_PROFILE_DIR = 'user_photo_profile';

    public function __construct(
        private string $publicUploadsDir,
    ) {
    }

    public function getUserPhotoProfileUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::USER_PHOTO_PROFILE_DIR);
    }

}