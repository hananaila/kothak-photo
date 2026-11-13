<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'capacity',
    ];

    protected $casts = [
        'date' => 'date',
        'capacity' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->whereNotIn('status', ['cancelled', 'Ditolak']);
    }

    public function getRemainingQuotaAttribute(): int
    {
        $booked = $this->activeBookings()->count();
        return max(0, $this->capacity - $booked);
    }
}

