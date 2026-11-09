<?php

namespace App\Models;

enum AnnouncementStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
