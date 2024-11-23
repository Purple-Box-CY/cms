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
    public const CONFIRMATION_REGISTRATION                           = 'mail/registration/confirmation_email.html.twig';
    public const RESET_PASSWORD                                      = 'mail/registration/reset_password.html.twig';

    public const TEMPLATES_BY_TYPES = [
        MailType::USER_BLOCKED                                        => self::USER_BLOCKED,
        MailType::USER_UNBLOCKED                                      => self::USER_UNBLOCKED,
        MailType::USER_APPROVED                                       => self::USER_APPROVED,
        MailType::USER_DECLINED                                       => self::USER_DECLINED,
        MailType::USER_AVATAR_APPROVED                                => self::USER_AVATAR_APPROVED,
        MailType::USER_AVATAR_DECLINED                                => self::USER_AVATAR_DECLINED,
        MailType::CONFIRMATION_REGISTRATION                           => self::CONFIRMATION_REGISTRATION,
        MailType::RESET_PASSWORD                                      => self::RESET_PASSWORD,
    ];
}