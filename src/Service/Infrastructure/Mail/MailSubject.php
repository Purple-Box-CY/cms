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
    public const CONFIRMATION_REGISTRATION                           = 'Confirm your email address';
    public const RESET_PASSWORD                                      = 'Your password reset request';

    public const SUBJECTS_BY_TYPES = [
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