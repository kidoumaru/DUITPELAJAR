<?php

namespace App\Models;

enum MessageStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Done = 'done';
}
