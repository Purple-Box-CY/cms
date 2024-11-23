<?php

namespace App\Enums\Messages;

enum MessageContentTypeEnum: string
{
    case PURCHASE = 'purchase';
    case BUTTON   = 'button';
    case VIDEO    = 'video';
    case IMAGE    = 'image';
    case TEXT     = 'text';
}
