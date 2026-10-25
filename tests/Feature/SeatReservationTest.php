<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Event;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeatReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_events_and_seat_matrix(): void
    {
        $user = User::factory()->create();

        $event = Event::create([
            'title' => 'Star Wars Marathon',
            'category' => 'Cinema',
            'venue' => 'Screen 5',
            'show_date' => now()->addDay()->toDateString(),
            'start_time' => '18:00',
            'ticket_price' => 12.00,
            'total_rows' => 3,
            'seats_per_row' => 6,
            'status' => 'active',
        ]);
        $event->generateSeats();

        $response = $this->actingAs($user)->get(route('events.show', $event));
        $response->assertStatus(200);
        $response->assertSee('Star Wars Marathon');
        $response->assertSee('A1');
    }

    public function test_user_can_book_available_seats(): void
    {
        $user = User::factory()->create();

        $event = Event::create([
            'title' => 'Interstellar',
            'category' => 'Cinema',
            'venue' => 'Screen 1',
            'show_date' => now()->addDays(2)->toDateString(),
            'start_time' => '20:00',
            'ticket_price' => 15.00,
            'total_rows' => 3,
            'seats_per_row' => 4,
            'status' => 'active',
        ]);
        $event->generateSeats();

        $seat1 = $event->seats()->where('seat_number', 'A1')->first();
        $seat2 = $event->seats()->where('seat_number', 'A2')->first();

        $response = $this->actingAs($user)->post(route('bookings.store', $event), [
            'seats' => [$seat1->id, $seat2->id],
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_seats' => 2,
            'status' => 'confirmed',
        ]);

        $booking = Booking::first();
        $response->assertRedirect(route('bookings.show', $booking));

        $this->assertDatabaseHas('booking_seats', ['booking_id' => $booking->id, 'seat_id' => $seat1->id]);
        $this->assertDatabaseHas('booking_seats', ['booking_id' => $booking->id, 'seat_id' => $seat2->id]);
    }

    public function test_cannot_book_already_reserved_seat(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $event = Event::create([
            'title' => 'Concert Live',
            'category' => 'Concert',
            'venue' => 'Arena',
            'show_date' => now()->addDays(3)->toDateString(),
            'start_time' => '19:00',
            'ticket_price' => 50.00,
            'total_rows' => 2,
            'seats_per_row' => 4,
            'status' => 'active',
        ]);
        $event->generateSeats();

        $seat = $event->seats()->first();

        // User 1 books seat
        $this->actingAs($user1)->post(route('bookings.store', $event), [
            'seats' => [$seat->id],
            'customer_name' => 'User One',
            'customer_email' => 'user1@example.com',
        ]);

        // User 2 attempts to book the same seat
        $response = $this->actingAs($user2)->post(route('bookings.store', $event), [
            'seats' => [$seat->id],
            'customer_name' => 'User Two',
            'customer_email' => 'user2@example.com',
        ]);

        $response->assertSessionHasErrors('seats');
        $this->assertEquals(1, Booking::count());
    }

    public function test_user_can_cancel_booking(): void
    {
        $user = User::factory()->create();

        $event = Event::create([
            'title' => 'Bus Trip',
            'category' => 'Bus Transit',
            'venue' => 'Platform 1',
            'show_date' => now()->addDay()->toDateString(),
            'start_time' => '08:00',
            'ticket_price' => 25.00,
            'total_rows' => 2,
            'seats_per_row' => 4,
            'status' => 'active',
        ]);
        $event->generateSeats();

        $seat = $event->seats()->first();

        $booking = Booking::create([
            'booking_code' => 'RES-CANCEL1',
            'user_id' => $user->id,
            'event_id' => $event->id,
            'total_seats' => 1,
            'total_price' => 25.00,
            'status' => 'confirmed',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ]);
        BookingSeat::create(['booking_id' => $booking->id, 'seat_id' => $seat->id, 'price' => 25.00]);

        $this->actingAs($user)->delete(route('bookings.destroy', $booking))
            ->assertRedirect(route('bookings.index'));

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
    }

    public function test_admin_can_create_event_with_seats(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('events.store'), [
            'title' => 'Oppenheimer Special',
            'category' => 'Cinema',
            'venue' => 'IMAX Hall 4',
            'show_date' => now()->addDays(5)->toDateString(),
            'start_time' => '21:00',
            'ticket_price' => 18.00,
            'total_rows' => 4,
            'seats_per_row' => 8,
        ]);

        $event = Event::where('title', 'Oppenheimer Special')->first();
        $this->assertNotNull($event);
        $response->assertRedirect(route('events.show', $event));

        // 4 rows x 8 cols = 32 seats generated
        $this->assertEquals(32, $event->seats()->count());
    }
}
