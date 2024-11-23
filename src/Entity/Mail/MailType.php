<?php

namespace App\Entity\Mail;

class MailType
{
    public const CONFIRMATION_REGISTRATION                           = 'confirmation_registration';
    public const RESET_PASSWORD                                      = 'reset_password';
    public const USER_APPROVED                                       = 'user_approved';
    public const USER_AVATAR_APPROVED                                = 'user_avatar_approved';
    public const USER_AVATAR_DECLINED                                = 'user_avatar_declined';
    public const USER_AUDIO_PROFILE_APPROVED                         = 'user_audio_profile_approved';
    public const USER_AUDIO_PROFILE_DECLINED                         = 'user_audio_profile_declined';
    public const USER_BLOCKED                                        = 'user_blocked';
    public const USER_DECLINED                                       = 'user_declined';
    public const USER_IMAGE_PROFILE_APPROVED                         = 'user_image_profile_approved';
    public const USER_IMAGE_PROFILE_DECLINED                         = 'user_image_profile_declined';
    public const USER_UNBLOCKED                                      = 'user_unblocked';

    public const IMPORTANT_MAILS = [
        self::RESET_PASSWORD,
        self::CONFIRMATION_REGISTRATION,
        self::USER_BLOCKED,
        self::USER_UNBLOCKED,
        self::USER_APPROVED,
        self::USER_DECLINED,
        self::USER_AVATAR_APPROVED,
        self::USER_AVATAR_DECLINED,
    ];

    public const AVAILABLE_TYPES = [
        self::RESET_PASSWORD                                      => self::RESET_PASSWORD,
        self::USER_APPROVED                                       => self::USER_APPROVED,
        self::USER_AVATAR_APPROVED                                => self::USER_AVATAR_APPROVED,
        self::USER_AVATAR_DECLINED                                => self::USER_AVATAR_DECLINED,
        self::USER_BLOCKED                                        => self::USER_BLOCKED,
        self::USER_DECLINED                                       => self::USER_DECLINED,
    ];
}
