<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'month',
        'year',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function getSpentAmountAttribute(): int
    {
        return (int) Transaction::where('user_id', $this->user_id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->when($this->category_id, function ($q) {
                $q->where('category_id', $this->category_id);
            })
            ->expense()
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): int
    {
        return max(0, $this->amount - $this->spent_amount);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return round(($this->spent_amount / $this->amount) * 100, 1);
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->percentage >= 100) {
            return 'danger'; // red
        }
        if ($this->percentage >= 75) {
            return 'warning'; // yellow/amber
        }

        return 'success'; // green
    }
}
