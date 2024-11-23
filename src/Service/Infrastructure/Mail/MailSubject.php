<?php

namespace App\Service\Infrastructure\Mail;

use App\Entity\Mail\MailType;

class MailSubject
{
    public const USER_BLOCKED                                        = 'Account Blocked';
    public const USER_UNBLOCKED                                      = 'Account Unblocked';
    public const USER_APPROVED                                       = 'Account Approved';
    public const USER_DECLINED                                       = 'Account Declined';
    public const USER_AVATAR_APPROVED                                = 'Avatar Approved';
    public const USER_AVATAR_DECLINED                                = 'Avatar Declined';
    public const USER_IMAGE_PROFILE_APPROVED                         = 'Image profile Approved';
    public const USER_IMAGE_PROFILE_DECLINED                         = 'Image profile Declined';
    public const USER_AUDIO_PROFILE_APPROVED                         = 'Audio profile Approved';
    public const USER_AUDIO_PROFILE_DECLINED                         = 'Audio profile Declined';
    public const CONTENT_APPROVED                                    = 'Video Approved';
    public const CONTENT_BLOCKED                                     = 'Video Blocked';
    public const CONTENT_STATUS_USER_NOT_APPROVED                    = 'Video not approved';
    public const MYSTERY_BOX_APPROVED                                = 'Mystery box Approved';
    public const MYSTERY_BOX_BLOCKED                                 = 'Mystery box Blocked';
    public const BUYER_BOUGHT_MYSTERY_BOX                            = 'Your Mystery box';
    public const BUYER_BOUGHT_WITHOUT_MYSTERY_BOX                    = 'Your Mystery box';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX    = 'User purchased paid votes';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX = 'User purchased paid votes. You need to create a mystery box!';
    public const NEW_UNREAD_MESSAGE                                  = 'You\'ve Received a New Message!';
    public const CONFIRMATION_REGISTRATION                           = 'Confirm your email address';
    public const RESET_PASSWORD                                      = 'Your password reset request';
    public const NEW_FOLLOWER                                        = 'You have a new follower';
    public const USER_VIEWS                                          = 'You have new views';

    public const SUBJECTS_BY_TYPES = [
        MailType::USER_BLOCKED                                        => self::USER_BLOCKED,
        MailType::USER_UNBLOCKED                                      => self::USER_UNBLOCKED,
        MailType::USER_APPROVED                                       => self::USER_APPROVED,
        MailType::USER_DECLINED                                       => self::USER_DECLINED,
        MailType::USER_AVATAR_APPROVED                                => self::USER_AVATAR_APPROVED,
        MailType::USER_AVATAR_DECLINED                                => self::USER_AVATAR_DECLINED,
        MailType::USER_IMAGE_PROFILE_APPROVED                         => self::USER_IMAGE_PROFILE_APPROVED,
        MailType::USER_IMAGE_PROFILE_DECLINED                         => self::USER_IMAGE_PROFILE_DECLINED,
        MailType::USER_AUDIO_PROFILE_APPROVED                         => self::USER_AUDIO_PROFILE_APPROVED,
        MailType::USER_AUDIO_PROFILE_DECLINED                         => self::USER_AUDIO_PROFILE_DECLINED,
        MailType::CONTENT_APPROVED                                    => self::CONTENT_APPROVED,
        MailType::CONTENT_BLOCKED                                     => self::CONTENT_BLOCKED,
        MailType::CONTENT_STATUS_USER_NOT_APPROVED                    => self::CONTENT_STATUS_USER_NOT_APPROVED,
        MailType::MYSTERY_BOX_APPROVED                                => self::MYSTERY_BOX_APPROVED,
        MailType::MYSTERY_BOX_BLOCKED                                 => self::MYSTERY_BOX_BLOCKED,
        MailType::BUYER_BOUGHT_MYSTERY_BOX                            => self::BUYER_BOUGHT_MYSTERY_BOX,
        MailType::BUYER_BOUGHT_WITHOUT_MYSTERY_BOX                    => self::BUYER_BOUGHT_WITHOUT_MYSTERY_BOX,
        MailType::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX    => self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX,
        MailType::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX => self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX,
        MailType::NEW_UNREAD_MESSAGE                                  => self::NEW_UNREAD_MESSAGE,
        MailType::CONFIRMATION_REGISTRATION                           => self::CONFIRMATION_REGISTRATION,
        MailType::RESET_PASSWORD                                      => self::RESET_PASSWORD,
        MailType::NEW_FOLLOWER                                        => self::NEW_FOLLOWER,
        MailType::USER_VIEWS                                          => self::USER_VIEWS,
    ];
}