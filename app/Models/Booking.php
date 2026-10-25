<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'event_id',
        'total_seats',
        'total_price',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'total_seats' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function seats(): BelongsToMany
    {
        return $this->belongsToMany(Seat::class, 'booking_seats');
    }
}
