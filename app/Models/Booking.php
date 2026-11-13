<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'schedule_id',
        'booking_date',
        'status',
        'queue_number',
        'studio_backdrop',
        'whatsapp_sent_at',
        'checked_in_at',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}

