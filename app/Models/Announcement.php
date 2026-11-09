<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'body', 'status', 'author_id', 'published_at'])]
class Announcement extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AnnouncementStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopeVisibleToUsers($query)
    {
        return $query->where('status', AnnouncementStatus::Published)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
