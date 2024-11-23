<?php

namespace App\Enums\Amplitude;

enum AmplitudeEventEnum: string
{
    case ACCOUNT_SYNC                                         = 'Account Sync';
    case MEDIA_UNPACKED                                       = 'Media Unpacked';
    case MESSENGER_CHAT_TIPS_MSG_SENT                         = 'messenger_chat_tips_msg_sent';
    case MESSENGER_CHAT_MYSTERYBOX_MSG_SENT                   = 'messenger_chat_mysterybox_msg_sent';
    case MESSENGER_CHAT_USER_MSG_SENT                         = 'messenger_chat_user_msg_sent';
    case MESSENGER_CHAT_USER_MSG_RECEIVED                     = 'messenger_chat_user_msg_received';
    case MESSENGER_CHAT_CUSTOM_TIPS_MSG_SENT                  = 'messenger_chat_custom_tips_msg_sent';
    case MESSENGER_CHAT_CUSTOM_TIPS_MEDIA_MSG_SENT            = 'messenger_chat_custom_tips_media_msg_sent';
    case MESSENGER_CHAT_HOT_CONTENT_MEDIA_MSG_SENT            = 'messenger_chat_hot_content_media_msg_sent';
    case MESSENGER_CHAT_HOT_CONTENT_MYSTERY_BOX_TEXT_MSG_SENT = 'messenger_chat_hot_content_mystery_box_text_msg_sent';
    case MESSENGER_CHAT_DONATE_REAL_GIFT_MSG_SENT             = 'messenger_chat_donate_real_gift_msg_sent';
    case MESSENGER_CHAT_ONLINE_GIFT_MSG_SENT                  = 'messenger_chat_online_gift_msg_sent';
    case MESSENGER_CHAT_PAID_MYSTERYBOX_MSG_SENT              = 'messenger_chat_paid_mysterybox_msg_sent';
    case MESSENGER_NEW_CHAT_CREATED                           = 'New Chat Created';
    case MESSAGE_SENT_FROM_USER                               = 'Message Sent From User';
    case MESSAGE_SENT_TO_USER                                 = 'Message Sent To User';
    case MESSAGE_READ_BY_USER                                 = 'Message Read By User';
    case PAYMENT_SUCCEEDED                                    = 'Payment Succeeded';
    case PUSH_SENT                                            = 'push_sent';
    case ACCOUNT_CREATED                                      = 'account_created';
    case REGISTRATION_COMPLETED                               = 'Registration Completed';
    case REGISTRATION_FAILED                                  = 'Registration Failed';
    case EMAIL_VERIFIED                                       = 'Email Verified';
    case MODEL_APPROVED                                       = 'Model Approved';
    case PAYMENT_REQUEST_SENT                                 = 'Payment Request Sent';
    case PAYMENT_STARTED                                      = 'Payment Started';
    case PAYMENT_FAILED                                       = 'Payment Failed';
    case VOTED                                                = 'Voted';
    case MODEL_FOLLOWED                                       = 'Model Followed';
    case INSTAGRAM_ACCOUNT_CONNECTED                          = 'Instagram Account Connected';
    case NOTIFICATION_SENT                                    = 'Notification Sent';
    case LOGGED_OUT                                           = 'Logged Out';
    case LOGGED_IN                                            = 'Logged In';
    case EMAIL_SENT                                           = 'Email Sent';
    case EMAIL_OPENED                                         = 'Email Opened';
    case EMAIL_CLICKED                                        = 'Email Clicked';
}
