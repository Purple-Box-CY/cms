<?php

namespace App\Service\Infrastructure;

class RedisKeys
{
    public const PREFIX_MAIN                = 'main';
    public const PREFIX_CHALLENGES          = 'challenges';
    public const PREFIX_CHALLENGE           = 'challenge';
    public const PREFIX_CONTENT             = 'content';
    public const PREFIX_MYSTERY_BOX         = 'mysterybox';
    public const PREFIX_FEED                = 'feed';
    public const PREFIX_AUTH                = 'auth';
    public const PREFIX_JWT                 = 'jwt';
    public const PREFIX_FREE_VOTE           = 'free-vote';
    public const PREFIX_EMAIL_CONFIRMATION  = 'email-confirmation';
    public const PREFIX_USER                = 'user';
    public const PREFIX_CONFIG              = 'config';
    public const KEY_CONTENT_ITEM           = 'content:%s';           //uid, id
    public const KEY_USER_BEST_CONTENT_ITEM = 'user_best_content:%s'; //uid
    public const KEY_CONTENT_MEDIA_ITEM     = 'content_media:%s';     //id

    public const KEY_CHALLENGES_TOP                    = 'top';
    public const KEY_CHALLENGES_ACTIVE                 = 'active';
    public const KEY_CHALLENGES_PAST                   = 'past';
    public const KEY_CHALLENGE_CONTENTS_NEW            = '%s:contents:new:%s';
    public const KEY_CHALLENGE_CONTENTS_TOP            = '%s:contents:top:%s';
    public const KEY_CHALLENGE_ID_RANKS_CONTENTS       = '%s:ranks:contents'; //id
    public const KEY_FEED_CONTENTS                     = 'contents';
    public const KEY_FEED_CONTENTS_IDS                 = 'contents_ids';
    public const KEY_TIPS_PRICES                       = 'tips:prices';
    public const KEY_CUSTOM_TIPS_PRICES                = 'custom_tips:prices';
    public const KEY_REAL_GIFTS_PRICES                 = 'real_gifts:prices';
    public const KEY_ONLINE_GIFTS_PRICES               = 'online_gifts:prices';
    public const KEY_HOT_CONTENT_PRICES                = 'hot_content:prices';
    public const KEY_MYSTERY_BOXES_PRICES              = 'mysterybox:prices';
    public const KEY_MYSTERY_BOXES_USER_CONTENTS_COUNT = 'mysterybox:%s:contents_count'; //uid
    public const KEY_MYSTERY_BOXES_USER_INFO           = 'mysterybox:%s:info';           //uid

    public const KEY_USER_ITEM            = '%s:profile'; //uid|username|id
    public const KEY_USER_INFO            = '%s:info';
    public const KEY_USER_CONTENTS        = 'user:%s:contents:%s';
    public const KEY_USER_CONTENTS_ACTIVE = 'user:%s:contents:active:%s';
    public const KEY_USER_CONTENTS_ALL    = 'user:%s:contents:all:%s';
    public const KEY_USER_VIEW_CONTENT    = 'user_viewed_content:%s';
    //public const KEY_VIEW                 = 'view:%s';
    //public const KEY_VIEW_PROCESSING      = 'view_processing:%s';
    public const KEY_USER_SWIPE_CONTENT = 'user_swiped_content:%s';
    public const KEY_USER_LAST_ACTIVE   = 'user:user_active_action:%s';
    public const KEY_USER_ONLINE        = 'user:online:%s';
    public const KEY_USER_ONLINE_CHECK  = 'user:online_check:%s';
    public const KEY_USER_NOTIFICATION  = 'user:notification:%s';     //user uid
    public const KEY_USER_NEED_RELOGIN  = 'user:need_relogin:%s';     //user uid
    public const KEY_USER_RELOGIN_SSE   = 'user:sse:need_relogin:%s'; //user uid
    public const KEY_USER_CHATS         = 'user:%s:chats';

    public const KEY_CONFIG_LIST_PUBLIC               = 'config:list:public';
    public const KEY_CONFIG_LIST_ALL                  = 'config:list:all';
    public const KEY_CONFIG_UPDATE                    = 'config:update';
    public const KEY_ARTICLE_ITEM                     = 'article:%s'; //alias
    public const QUEUE_MAIN                           = 'main';
    public const QUEUE_CONVERT_VIDEO                  = 'convert_video';
    public const QUEUE_MYSTERY_BOX_CONVERT_MEDIA      = 'mystery_box_convert_media';
    public const QUEUE_MYSTERY_BOX_REFRESH_PREVIEW    = 'mystery_box_refresh_preview';
    public const QUEUE_MYSTERY_BOX_APPROVED           = 'mystery_box_approved';
    public const QUEUE_REMOVE_LOCAL_VIDEO             = 'remove_local_video';
    public const QUEUE_REMOVE_LOCAL_MYSTERY_BOX_MEDIA = 'remove_mystery_box_media';
    public const QUEUE_UPLOAD_CDN_VIDEO               = 'upload_cdn_video';
    public const QUEUE_UPLOAD_CDN_MYSTERY_BOX_MEDIA   = 'upload_cdn_mystery_box_media';
    public const QUEUE_USER_VIEW_CONTENT              = 'user_view_content';
    public const QUEUE_USER_VIEW_PROFILE              = 'user_view_profile';
    public const QUEUE_USER_SHARING_CONTENT           = 'user_sharing_content';
    public const QUEUE_USER_VOTE_CONTENT              = 'user_vote_content';
    public const QUEUE_CHAT_NEW_MESSAGE               = 'chat_new_message';
    public const QUEUE_CHAT_START                     = 'chat_start';
    public const QUEUE_CHAT_USER_ONLINE               = 'chat_user_online'; //user_id
    public const QUEUE_CHATS_USER_IS_TALKER           = 'chats_user_is_talker';
    public const QUEUE_MAIL                           = 'mail_to_send';
    public const QUEUE_PUSH                           = 'push_to_send';
    public const QUEUE_VIEW                           = 'view';
    public const QUEUE_PAYMENT_SUCCESS                = 'payment_success';
    public const QUEUE_FRONT_LOGS                     = 'front_logs';
    public const QUEUE_REGISTER_ANONYM                = 'register_anonym';
    public const QUEUE_UPDATE_ANONYM                  = 'update_anonym';
    public const QUEUE_REAL_GIFT_COMPLETED            = 'real_gift_completed';
    public const QUEUE_OPEN_PERSONAL_LINKS            = 'open_personal_links';
    public const string QUEUE_NOTIFICATION_SENT              = 'notification_sent';

    public const PRICES_KEYS = [
        self::KEY_TIPS_PRICES,
        self::KEY_CUSTOM_TIPS_PRICES,
        self::KEY_REAL_GIFTS_PRICES,
        self::KEY_ONLINE_GIFTS_PRICES,
        self::KEY_HOT_CONTENT_PRICES,
        self::KEY_MYSTERY_BOXES_PRICES,
    ];

    public const AVAILABLE_QUEUES = [
        self::QUEUE_CHAT_NEW_MESSAGE,
        self::QUEUE_CHAT_START,
        self::QUEUE_CHAT_USER_ONLINE,
        self::QUEUE_CONVERT_VIDEO,
        self::QUEUE_FRONT_LOGS,
        self::QUEUE_MAIL,
        self::QUEUE_MAIN,
        self::QUEUE_MYSTERY_BOX_CONVERT_MEDIA,
        self::QUEUE_MYSTERY_BOX_REFRESH_PREVIEW,
        self::QUEUE_MYSTERY_BOX_APPROVED,
        self::QUEUE_PAYMENT_SUCCESS,
        self::QUEUE_PUSH,
        self::QUEUE_PUSH,
        self::QUEUE_VIEW,
        self::QUEUE_REGISTER_ANONYM,
        self::QUEUE_UPDATE_ANONYM,
        self::QUEUE_REMOVE_LOCAL_MYSTERY_BOX_MEDIA,
        self::QUEUE_REMOVE_LOCAL_VIDEO,
        self::QUEUE_UPLOAD_CDN_VIDEO,
        self::QUEUE_UPLOAD_CDN_MYSTERY_BOX_MEDIA,
        self::QUEUE_USER_VIEW_CONTENT,
        self::QUEUE_USER_VIEW_PROFILE,
        self::QUEUE_USER_SHARING_CONTENT,
        self::QUEUE_USER_VOTE_CONTENT,
        self::QUEUE_REAL_GIFT_COMPLETED,
        self::QUEUE_OPEN_PERSONAL_LINKS,
        self::QUEUE_CHATS_USER_IS_TALKER,
    ];
}
