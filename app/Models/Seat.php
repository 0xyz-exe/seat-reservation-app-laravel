<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'seat_number',
        'row_letter',
        'column_number',
        'seat_type',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'column_number' => 'integer',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function bookingSeats(): HasMany
    {
        return $this->hasMany(BookingSeat::class);
    }

    public function isBooked(): bool
    {
        return $this->bookingSeats()
            ->whereHas('booking', function ($q) {
                $q->where('status', 'confirmed');
            })
            ->exists();
    }
}
