<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'venue',
        'show_date',
        'start_time',
        'ticket_price',
        'total_rows',
        'seats_per_row',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'show_date' => 'date',
            'ticket_price' => 'decimal:2',
            'total_rows' => 'integer',
            'seats_per_row' => 'integer',
        ];
    }

    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function generateSeats(): void
    {
        $alphabet = range('A', 'Z');
        for ($r = 0; $r < $this->total_rows; $r++) {
            $rowLetter = $alphabet[$r];
            // Last row or center row can be VIP if desired
            $isVip = ($r === $this->total_rows - 1);
            $price = $isVip ? round($this->ticket_price * 1.35, 2) : $this->ticket_price;

            for ($c = 1; $c <= $this->seats_per_row; $c++) {
                $this->seats()->create([
                    'seat_number' => $rowLetter . $c,
                    'row_letter' => $rowLetter,
                    'column_number' => $c,
                    'seat_type' => $isVip ? 'vip' : 'standard',
                    'price' => $price,
                ]);
            }
        }
    }

    public function getBookedSeatIdsAttribute(): array
    {
        return BookingSeat::whereHas('booking', function ($q) {
            $q->where('event_id', $this->id)->where('status', 'confirmed');
        })->pluck('seat_id')->toArray();
    }
}
