<?php

namespace App\Service\Infrastructure\Mail;

use App\Entity\Mail\MailType;

class MailTemplate
{
    public const USER_BLOCKED                                        = 'mail/user/block.html.twig';
    public const USER_UNBLOCKED                                      = 'mail/user/unblock.html.twig';
    public const USER_APPROVED                                       = 'mail/user/approve.html.twig';
    public const USER_DECLINED                                       = 'mail/user/decline.html.twig';
    public const USER_AVATAR_APPROVED                                = 'mail/user/avatar_approve.html.twig';
    public const USER_AVATAR_DECLINED                                = 'mail/user/avatar_decline.html.twig';
    public const USER_IMAGE_PROFILE_APPROVED                         = 'mail/user/image_profile_approve.html.twig';
    public const USER_IMAGE_PROFILE_DECLINED                         = 'mail/user/image_profile_decline.html.twig';
    public const USER_AUDIO_PROFILE_APPROVED                         = 'mail/user/audio_profile_approve.html.twig';
    public const USER_AUDIO_PROFILE_DECLINED                         = 'mail/user/audio_profile_decline.html.twig';
    public const CONTENT_APPROVED                                    = 'mail/content/approve.html.twig';
    public const CONTENT_BLOCKED                                     = 'mail/content/block.html.twig';
    public const CONTENT_STATUS_USER_NOT_APPROVED                    = 'mail/content/user_not_approved.html.twig';
    public const MYSTERY_BOX_APPROVED                                = 'mail/mysterybox/approve.html.twig';
    public const MYSTERY_BOX_BLOCKED                                 = 'mail/mysterybox/blocked.html.twig';
    public const BUYER_BOUGHT_MYSTERY_BOX                            = 'mail/mysterybox/buyer_bought_mystery_box.html.twig';
    public const BUYER_BOUGHT_WITHOUT_MYSTERY_BOX                    = 'mail/mysterybox/buyer_bought_without_mystery_box.html.twig';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITH_MYSTERY_BOX    = 'mail/votes/for_creator_about_bought_votes_with_mystery_box.html.twig';
    public const MAIL_CREATOR_ABOUT_BOUGHT_VOTES_WITHOUT_MYSTERY_BOX = 'mail/votes/for_creator_about_bought_votes_without_mystery_box.html.twig';
    public const NEW_UNREAD_MESSAGE                                  = 'mail/messages/new_unread_message.html.twig';
    public const CONFIRMATION_REGISTRATION                           = 'mail/registration/confirmation_email.html.twig';
    public const RESET_PASSWORD                                      = 'mail/registration/reset_password.html.twig';
    public const NEW_FOLLOWER                                        = 'mail/followers/new_follower.html.twig';
    public const USER_VIEWS                                          = 'mail/views/view.html.twig';

    public const TEMPLATES_BY_TYPES = [
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