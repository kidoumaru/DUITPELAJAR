<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'subject_type',
        'subject_id',
        'subject_label',
        'description',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Create an activity log entry for an admin action.
     */
    public static function record(
        int $adminId,
        string $action,
        string $subjectType,
        ?int $subjectId,
        string $subjectLabel,
        ?string $description = null
    ): void {
        static::create([
            'admin_id' => $adminId,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $subjectLabel,
            'description' => $description,
        ]);
    }
}
