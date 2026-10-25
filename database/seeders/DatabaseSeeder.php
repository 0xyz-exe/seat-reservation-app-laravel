<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $testUser = User::create([
            'name' => 'Marcus Vance',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        // 2. Events
        $event1 = Event::create([
            'title' => 'Dune: Part Two (IMAX)',
            'category' => 'Cinema',
            'venue' => 'Grand Cinema Hall 1',
            'show_date' => now()->addDays(2)->toDateString(),
            'start_time' => '19:45',
            'ticket_price' => 16.50,
            'total_rows' => 5,
            'seats_per_row' => 8,
            'description' => 'Experience the epic sci-fi journey with dual 4K laser projection and immersive 12-channel surround sound.',
            'status' => 'active',
        ]);
        $event1->generateSeats();

        $event2 = Event::create([
            'title' => 'Coldplay: Music of the Spheres',
            'category' => 'Concert',
            'venue' => 'National Arena Auditorium',
            'show_date' => now()->addDays(12)->toDateString(),
            'start_time' => '20:00',
            'ticket_price' => 75.00,
            'total_rows' => 6,
            'seats_per_row' => 8,
            'description' => 'World tour live music experience featuring stunning light shows and sustainable kinetic staging.',
            'status' => 'active',
        ]);
        $event2->generateSeats();

        $event3 = Event::create([
            'title' => 'Intercity Night Express 401',
            'category' => 'Bus Transit',
            'venue' => 'Terminal Central Platform 4',
            'show_date' => now()->addDays(4)->toDateString(),
            'start_time' => '22:30',
            'ticket_price' => 28.00,
            'total_rows' => 6,
            'seats_per_row' => 4,
            'description' => 'Executive reclining sleeper coach with personal climate control and onboard high-speed Wi-Fi.',
            'status' => 'active',
        ]);
        $event3->generateSeats();

        // 3. Seed Bookings
        // Booking 1 for Marcus (testUser) on Event 1 (Seats C3 & C4)
        $seatC3 = $event1->seats()->where('seat_number', 'C3')->first();
        $seatC4 = $event1->seats()->where('seat_number', 'C4')->first();

        $booking1 = Booking::create([
            'booking_code' => 'RES-DUN742',
            'user_id' => $testUser->id,
            'event_id' => $event1->id,
            'total_seats' => 2,
            'total_price' => $seatC3->price + $seatC4->price,
            'status' => 'confirmed',
            'customer_name' => $testUser->name,
            'customer_email' => $testUser->email,
            'customer_phone' => '+1 (555) 019-2831',
        ]);

        BookingSeat::create(['booking_id' => $booking1->id, 'seat_id' => $seatC3->id, 'price' => $seatC3->price]);
        BookingSeat::create(['booking_id' => $booking1->id, 'seat_id' => $seatC4->id, 'price' => $seatC4->price]);

        // Booking 2 for someone else on Event 1 (Seats B4 & B5)
        $seatB4 = $event1->seats()->where('seat_number', 'B4')->first();
        $seatB5 = $event1->seats()->where('seat_number', 'B5')->first();

        $booking2 = Booking::create([
            'booking_code' => 'RES-B88190',
            'user_id' => $admin->id,
            'event_id' => $event1->id,
            'total_seats' => 2,
            'total_price' => $seatB4->price + $seatB5->price,
            'status' => 'confirmed',
            'customer_name' => 'Alice Cooper',
            'customer_email' => 'alice@example.com',
        ]);

        BookingSeat::create(['booking_id' => $booking2->id, 'seat_id' => $seatB4->id, 'price' => $seatB4->price]);
        BookingSeat::create(['booking_id' => $booking2->id, 'seat_id' => $seatB5->id, 'price' => $seatB5->price]);

        // Booking 3 for Marcus on Event 3 (Seat A1)
        $seatA1 = $event3->seats()->where('seat_number', 'A1')->first();
        $booking3 = Booking::create([
            'booking_code' => 'RES-BUS109',
            'user_id' => $testUser->id,
            'event_id' => $event3->id,
            'total_seats' => 1,
            'total_price' => $seatA1->price,
            'status' => 'confirmed',
            'customer_name' => $testUser->name,
            'customer_email' => $testUser->email,
        ]);
        BookingSeat::create(['booking_id' => $booking3->id, 'seat_id' => $seatA1->id, 'price' => $seatA1->price]);
    }
}
