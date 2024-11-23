<?php

namespace App\Service;

class ImageService
{
    public const CHALLENGES_IMAGE_DIR = 'challenges';
    public const WELCOME_PHOTO_IMAGE_DIR = 'welcome';
    public const BODY_PHOTO_IMAGE_DIR = 'body_photos';
    public const REAL_GIFTS_IMAGE_DIR = 'real_gifts';
    public const ONLINE_GIFTS_IMAGE_DIR = 'online_gifts';
    public const USER_PHOTO_PROFILE_DIR = 'user_photo_profile';

    public function __construct(
        private string $publicUploadsDir,
    ) {
    }

    public function getChallengeImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::CHALLENGES_IMAGE_DIR);
    }

    public function getRealGiftsImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::REAL_GIFTS_IMAGE_DIR);
    }

    public function getOnlineGiftsImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::ONLINE_GIFTS_IMAGE_DIR);
    }

    public function getWelcomePhotoImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::WELCOME_PHOTO_IMAGE_DIR);
    }

    public function getBodyPhotoImageUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::BODY_PHOTO_IMAGE_DIR);
    }

    public function getUserPhotoProfileUploadDir(): string
    {
        return sprintf('%s/%s', $this->publicUploadsDir, self::USER_PHOTO_PROFILE_DIR);
    }

    public function getPublicUploadsDir(): string
    {
        return $this->publicUploadsDir;
    }
}