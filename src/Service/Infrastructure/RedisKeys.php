<?php

namespace App\Service\Infrastructure;

class RedisKeys
{
    public const PREFIX_MAIN   = 'main';
    public const PREFIX_AUTH   = 'auth';
    public const PREFIX_JWT    = 'jwt';
    public const PREFIX_USER   = 'user';
    public const PREFIX_CONFIG = 'config';

    public const KEY_USER_ITEM = '%s:profile'; //uid|username|id
    public const KEY_USER_INFO = '%s:info';

    public const KEY_USER_NEED_RELOGIN = 'user:need_relogin:%s';     //user uid
    public const KEY_USER_RELOGIN_SSE  = 'user:sse:need_relogin:%s'; //user uid

    public const        KEY_CONFIG_LIST_PUBLIC     = 'config:list:public';
    public const        KEY_CONFIG_LIST_ALL        = 'config:list:all';
    public const        KEY_ARTICLE_ITEM           = 'article:%s'; //alias
    public const string KEY_MARKERS                = 'markers:%s';
    public const string KEY_MARKER                 = 'marker:%s';
    public const        QUEUE_MAIN                 = 'main';
    public const        QUEUE_CONVERT_VIDEO        = 'convert_video';
    public const        QUEUE_UPLOAD_CDN_VIDEO     = 'upload_cdn_video';
    public const        QUEUE_USER_VIEW_CONTENT    = 'user_view_content';
    public const        QUEUE_USER_VIEW_PROFILE    = 'user_view_profile';
    public const        QUEUE_USER_SHARING_CONTENT = 'user_sharing_content';
    public const        QUEUE_USER_VOTE_CONTENT    = 'user_vote_content';
    public const        QUEUE_CHAT_NEW_MESSAGE     = 'chat_new_message';
    public const        QUEUE_CHAT_START           = 'chat_start';
    public const        QUEUE_CHAT_USER_ONLINE     = 'chat_user_online'; //user_id
    public const        QUEUE_CHATS_USER_IS_TALKER = 'chats_user_is_talker';
    public const        QUEUE_MAIL                 = 'mail_to_send';
    public const        QUEUE_PAYMENT_SUCCESS      = 'payment_success';
    public const        QUEUE_FRONT_LOGS           = 'front_logs';
    public const        QUEUE_REAL_GIFT_COMPLETED  = 'real_gift_completed';
    public const        QUEUE_OPEN_PERSONAL_LINKS  = 'open_personal_links';

    public const AVAILABLE_QUEUES = [
        self::QUEUE_CHAT_NEW_MESSAGE,
        self::QUEUE_CHAT_START,
        self::QUEUE_CHAT_USER_ONLINE,
        self::QUEUE_CONVERT_VIDEO,
        self::QUEUE_FRONT_LOGS,
        self::QUEUE_MAIL,
        self::QUEUE_MAIN,
        self::QUEUE_PAYMENT_SUCCESS,
        self::QUEUE_UPLOAD_CDN_VIDEO,
        self::QUEUE_USER_VIEW_CONTENT,
        self::QUEUE_USER_VIEW_PROFILE,
        self::QUEUE_USER_SHARING_CONTENT,
        self::QUEUE_USER_VOTE_CONTENT,
        self::QUEUE_REAL_GIFT_COMPLETED,
        self::QUEUE_OPEN_PERSONAL_LINKS,
        self::QUEUE_CHATS_USER_IS_TALKER,
    ];
}
