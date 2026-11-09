<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsGoal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'integer',
            'current_amount' => 'integer',
            'target_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTargetAttribute(): string
    {
        return 'Rp '.number_format($this->target_amount, 0, ',', '.');
    }

    public function getFormattedCurrentAttribute(): string
    {
        return 'Rp '.number_format($this->current_amount, 0, ',', '.');
    }

    public function getRemainingAmountAttribute(): int
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function isAchieved(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }
}
