<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'title', 'body', 'status'])]
class Message extends Model
{
    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(MessageReply::class)->orderBy('created_at');
    }
}
