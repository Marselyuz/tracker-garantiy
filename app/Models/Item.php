<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'warranty_end_date' => 'date',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    /**
     * Статус гарантии считается на лету от даты окончания —
     * active / expiring (<=14 дней) / expired.
     */
    public function getWarrantyStatusAttribute(): string
    {
        $end = Carbon::parse($this->warranty_end_date)->startOfDay();
        $today = Carbon::today();

        if ($end->lt($today)) {
            return 'expired';
        }

        if ($today->diffInDays($end) <= 14) {
            return 'expiring';
        }

        return 'active';
    }

    public function getDaysLeftAttribute(): int
    {
        $end = Carbon::parse($this->warranty_end_date)->startOfDay();
        return Carbon::today()->diffInDays($end, false);
    }
}
