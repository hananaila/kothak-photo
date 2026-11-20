<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'schedule_id',
        'booking_date',
        'booking_type',
        'participants_count',
        'status',
        'queue_number',
        'studio_backdrop',
        'package_price',
        'add_ons_total',
        'total_price',
        'notes',
        'whatsapp_sent_at',
        'checked_in_at',
        'called_at',
        'completed_at',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
        'package_price' => 'decimal:2',
        'add_ons_total' => 'decimal:2',
        'total_price' => 'decimal:2',
        'participants_count' => 'integer',
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

    public function bookingAddOns(): HasMany
    {
        return $this->hasMany(BookingAddOn::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting_verification' => 'Menunggu Verifikasi',
            'confirmed' => 'Terkonfirmasi',
            'checked_in' => 'Check-in (Siap)',
            'in_studio' => 'Sedang di Studio',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'waiting_verification' => 'bg-amber-100 text-amber-900 border-amber-400',
            'confirmed' => 'bg-blue-100 text-blue-900 border-blue-400',
            'checked_in' => 'bg-purple-100 text-purple-900 border-purple-400',
            'in_studio' => 'bg-emerald-500 text-slate-900 border-slate-900 font-extrabold animate-pulse',
            'completed' => 'bg-emerald-100 text-emerald-900 border-emerald-400',
            'cancelled' => 'bg-rose-100 text-rose-900 border-rose-400',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }
}
