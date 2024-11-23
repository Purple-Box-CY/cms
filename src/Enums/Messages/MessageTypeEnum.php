<?php

namespace App\Enums\Messages;

enum MessageTypeEnum: string
{
    case DEFAULT_INFO_MESSAGE                                = 'default_info_message';
    case PREVIEW_MYSTERY_BOX                                 = 'preview mystery box';
    case TEXT_MYSTERY_BOX                                    = 'text mystery box';
    case ATTACHMENT_TIPS                                     = 'attachment tips';
    case TEXT_TIPS                                           = 'text tips';
    case ATTACHMENT_CUSTOM_TIPS                              = 'attachment custom tips';
    case TEXT_BY_CUSTOM_TIPS                                 = 'text by custom tips';
    case ANONYM_MESSAGE_SELECTED_HOT_CONTENT_CATEGORY        = 'anonym message selected hot content category';
    case WELCOME_PHOTO_MESSAGE_HOT_CONTENT                   = 'welcome photo message hot content';
    case ATTACHMENT_DONATE_REAL_GIFT                         = 'attachment donate real gift';
    case MESSAGE_DONATE_REAL_GIFT                            = 'message donate real gift';
    case ATTACHMENT_ONLINE_GIFT                              = 'attachment online gift';
    case MESSAGE_ONLINE_GIFT                                 = 'message online gift';
    case THANKS_MESSAGE                                      = 'thanks message';
    case TIPS_THANKS_MESSAGE                                 = 'Tips thanks message';
    case ONLINE_GIFT_THANKS_MESSAGE                          = 'online gift thanks message';
    case DONATE_REAL_GIFT_THANKS_MESSAGE                     = 'donate real gift thanks message';
    case BUTTON_MYSTERY_BOX                                  = 'button mystery box';
    case BUTTON_CUSTOM_TIPS                                  = 'button custom tips';
    case HOT_CONTENT_BUTTONS                                 = 'hot_content_buttons';
    case ATTACH_FILE                                         = 'Attach file';
    case ATTACHMENT_MEDIA_CUSTOM_TIPS                        = 'attachment media custom tips';
    case ATTACHMENTS_MEDIAS_CUSTOM_TIPS                      = 'attachments medias custom tips';
    case WELCOME_MESSAGE                                     = 'welcome message';
    case WELCOME_PHOTO                                       = 'welcome photo';
    case WELCOME_PHOTO_MESSAGE                               = 'welcome photo message';
    case SECOND_WELCOME_MESSAGE_PHOTO                        = 'second welcome message photo';
    case SECOND_WELCOME_MESSAGE_PHOTO_TEXT                   = 'second welcome message photo text';
    case WELCOME_ANSWER_AFTER_PHOTO                          = 'welcome answer after photo';
    case ATTACHMENT_MEDIA_MYSTERY_BOX                        = 'attachment media mystery box';
    case FAN_SELECTED_BUTTON_BODY_PHOTO_CATEGORY_IN_CHAT_NOW = 'fan selected button body photo category in chat now';
    case CHAT_NOW_BODY_PHOTO                                 = 'chat now body photo';
    case HOT_CONTENT_MESSAGE                                 = 'Hot content message';
    case MESSAGE_HOT_CONTENT_BLUR                            = 'message hot content blur';
    case HOT_CONTENT_MYSTERY_BOX_MESSAGE                     = 'hot content mystery box message';
    case HOT_CONTENT_MESSAGE_4                               = 'Hot content message 4';
    case HOT_CONTENT_MESSAGE_4A                              = 'Hot content message 4a';
    case HOT_CONTENT_MESSAGE_5                               = 'Hot content message 5';
    case HOT_CONTENT_MESSAGE_5A                              = 'Hot content message 5a';
    case HOT_CONTENT_BLUR                                    = 'hot_content_blur';
    case START_CHAT_NOW_V3_MESSAGE_1                         = 'start chat now 3.0, message 1';
    case START_CHAT_NOW_V3_MESSAGE_2A                        = 'start chat now 3.0, message 2a';
    case START_CHAT_NOW_V3_MESSAGE_2B                        = 'start chat now 3.0, message 2b';
    case START_CHAT_NOW_V3_MESSAGE_3                         = 'start chat now 3.0, message 3';
    case START_CHAT_NOW_V3_MESSAGE_4                         = 'start chat now 3.0, message 4';
    case CHAT_NOW_BUTTONS                                    = 'chat_now_buttons';
    case FAN_SELECTED_BUTTON_DIALOG_TYPE_IN_CHAT_NOW         = 'fan selected button dialog type in chat now';
    case CHAT_NOW_PHOTO                                      = 'chat now photo';
}
