<?php

namespace App\Entity\Mail;

class MailType
{
    public const BUYER_BOUGHT_MYSTERY_BOX                            = 'buyer_bought_mystery_box';
    public const BUYER_BOUGHT_WITHOUT_MYSTERY_BOX                    = 'buyer_bought_without_mystery_box';
    public const CONFIRMATION_REGISTRATION                           = 'confirmation_registration';
    public const CONTENT_APPROVED                                    = 'content_approved';
    public const CONTENT_BLOCKED                                     = 'content_blocked';
    public const CONTENT_STATUS_USER_NOT_APPROVED                    = 'content_status_user_not_approved';
    public const MYSTERY_BOX_APPROVED                                = 'mystery_box_approved';
    public const MYSTERY_BOX_BLOCKED                                 = 'mystery_box_blocked';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX    = 'mail_creator_about_bought_votes_with_mystery_box';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX = 'mail_creator_about_bought_votes_without_mystery_box';
    public const NEW_FOLLOWER                                        = 'new_follower';
    public const NEW_UNREAD_MESSAGE                                  = 'new_unread_message';
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
    public const USER_VIEWS                                          = 'user_views';

    public const IMPORTANT_MAILS = [
        self::RESET_PASSWORD,
        self::CONFIRMATION_REGISTRATION,
        self::USER_BLOCKED,
        self::USER_UNBLOCKED,
        self::USER_APPROVED,
        self::USER_DECLINED,
        self::USER_AVATAR_APPROVED,
        self::USER_AVATAR_DECLINED,
        self::USER_IMAGE_PROFILE_APPROVED,
        self::USER_IMAGE_PROFILE_DECLINED,
        self::USER_AUDIO_PROFILE_APPROVED,
        self::USER_AUDIO_PROFILE_DECLINED,
        self::CONTENT_APPROVED,
        self::CONTENT_BLOCKED,
        self::CONTENT_STATUS_USER_NOT_APPROVED,
        self::NEW_FOLLOWER,
    ];

    public const AVAILABLE_TYPES = [
        self::BUYER_BOUGHT_MYSTERY_BOX                            => self::BUYER_BOUGHT_MYSTERY_BOX,
        self::BUYER_BOUGHT_WITHOUT_MYSTERY_BOX                    => self::BUYER_BOUGHT_WITHOUT_MYSTERY_BOX,
        self::CONFIRMATION_REGISTRATION                           => self::CONFIRMATION_REGISTRATION,
        self::CONTENT_APPROVED                                    => self::CONTENT_APPROVED,
        self::CONTENT_BLOCKED                                     => self::CONTENT_BLOCKED,
        self::CONTENT_STATUS_USER_NOT_APPROVED                    => self::CONTENT_STATUS_USER_NOT_APPROVED,
        self::MYSTERY_BOX_APPROVED                                => self::MYSTERY_BOX_APPROVED,
        self::MYSTERY_BOX_BLOCKED                                 => self::MYSTERY_BOX_BLOCKED,
        self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX    => self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX,
        self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX => self::MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX,
        self::NEW_FOLLOWER                                        => self::NEW_FOLLOWER,
        self::NEW_UNREAD_MESSAGE                                  => self::NEW_UNREAD_MESSAGE,
        self::RESET_PASSWORD                                      => self::RESET_PASSWORD,
        self::USER_APPROVED                                       => self::USER_APPROVED,
        self::USER_AVATAR_APPROVED                                => self::USER_AVATAR_APPROVED,
        self::USER_AVATAR_DECLINED                                => self::USER_AVATAR_DECLINED,
        self::USER_AUDIO_PROFILE_APPROVED                         => self::USER_AUDIO_PROFILE_APPROVED,
        self::USER_AUDIO_PROFILE_DECLINED                         => self::USER_AUDIO_PROFILE_DECLINED,
        self::USER_BLOCKED                                        => self::USER_BLOCKED,
        self::USER_DECLINED                                       => self::USER_DECLINED,
        self::USER_IMAGE_PROFILE_APPROVED                         => self::USER_IMAGE_PROFILE_APPROVED,
        self::USER_IMAGE_PROFILE_DECLINED                         => self::USER_IMAGE_PROFILE_DECLINED,
        self::USER_UNBLOCKED                                      => self::USER_UNBLOCKED,
        self::USER_VIEWS                                          => self::USER_VIEWS,
    ];
}
