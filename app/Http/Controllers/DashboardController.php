<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Event;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $stats = [
            'total_events' => Event::where('status', 'active')->count(),
            'total_bookings' => $user->isAdmin() ? Booking::where('status', 'confirmed')->count() : $user->bookings()->where('status', 'confirmed')->count(),
            'total_seats_reserved' => $user->isAdmin() 
                ? BookingSeat::whereHas('booking', fn($q) => $q->where('status', 'confirmed'))->count()
                : BookingSeat::whereHas('booking', fn($q) => $q->where('user_id', $user->id)->where('status', 'confirmed'))->count(),
            'total_spent' => $user->isAdmin()
                ? Booking::where('status', 'confirmed')->sum('total_price')
                : $user->bookings()->where('status', 'confirmed')->sum('total_price'),
        ];

        $upcomingEvents = Event::where('status', 'active')
            ->where('show_date', '>=', now()->toDateString())
            ->orderBy('show_date')
            ->take(4)
            ->get();

        $myBookings = $user->bookings()
            ->with(['event', 'seats'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'upcomingEvents', 'myBookings'));
    }
}
