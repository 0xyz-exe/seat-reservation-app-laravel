<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Event;
use App\Models\Seat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = $user->isAdmin() ? Booking::with(['event', 'user']) : $user->bookings()->with('event');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'seats' => ['required', 'array', 'min:1'],
            'seats.*' => ['exists:seats,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $selectedSeatIds = $validated['seats'];

        // Verify seats belong to this event
        $seats = Seat::where('event_id', $event->id)->whereIn('id', $selectedSeatIds)->get();
        if ($seats->count() !== count($selectedSeatIds)) {
            return back()->withErrors(['seats' => 'One or more invalid seats selected.']);
        }

        // Verify none of the seats are already booked
        $alreadyBooked = BookingSeat::whereIn('seat_id', $selectedSeatIds)
            ->whereHas('booking', function ($q) {
                $q->where('status', 'confirmed');
            })
            ->exists();

        if ($alreadyBooked) {
            return back()->withErrors(['seats' => 'One or more of your selected seats was just taken. Please choose another seat.']);
        }

        $totalPrice = $seats->sum('price');
        $bookingCode = 'RES-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'booking_code' => $bookingCode,
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'total_seats' => $seats->count(),
            'total_price' => $totalPrice,
            'status' => 'confirmed',
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'] ?? null,
        ]);

        foreach ($seats as $seat) {
            BookingSeat::create([
                'booking_id' => $booking->id,
                'seat_id' => $seat->id,
                'price' => $seat->price,
            ]);
        }

        return redirect()->route('bookings.show', $booking)->with('status', 'Seats reserved successfully! Your booking reference is ' . $booking->booking_code);
    }

    public function show(Booking $booking): View
    {
        $this->authorizeAccess($booking);

        $booking->load(['event', 'seats', 'user']);

        return view('bookings.show', compact('booking'));
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $this->authorizeAccess($booking);

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')->with('status', 'Booking ' . $booking->booking_code . ' cancelled and seats released.');
    }

    private function authorizeAccess(Booking $booking): void
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $booking->user_id !== $user->id) {
            abort(403, 'Unauthorized booking access.');
        }
    }
}
