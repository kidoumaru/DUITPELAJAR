<?php

namespace App\Models;

enum Role: string
{
    case Admin = 'admin';
    case User = 'user';
}
