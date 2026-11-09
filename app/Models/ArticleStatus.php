<?php

namespace App\Models;

enum ArticleStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
