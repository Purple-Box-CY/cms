<?php

namespace App\Service;

class ImageService
{
    public const string USER_PHOTO_PROFILE_DIR = 'user_photo_profile';
    public const string MARKERS_IMAGE_DIR      = 'markers';

    public function __construct(
        private string $publicUploadsDir,
    ) {
    }

    public function getUserPhotoProfileUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::USER_PHOTO_PROFILE_DIR);
    }


    public function getMarkersImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::MARKERS_IMAGE_DIR);
    }

}