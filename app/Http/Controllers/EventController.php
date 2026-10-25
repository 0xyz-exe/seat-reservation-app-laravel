<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        $query = Event::withCount('seats');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('show_date')->paginate(9)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function show(Event $event): View
    {
        $event->load(['seats' => function ($q) {
            $q->orderBy('row_letter')->orderBy('column_number');
        }]);

        $bookedSeatIds = $event->booked_seat_ids;

        // Group seats by row
        $seatsByRow = $event->seats->groupBy('row_letter');

        return view('events.show', compact('event', 'seatsByRow', 'bookedSeatIds'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Cinema,Concert,Bus Transit,Theater'],
            'venue' => ['required', 'string', 'max:255'],
            'show_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string'],
            'ticket_price' => ['required', 'numeric', 'min:1'],
            'total_rows' => ['required', 'integer', 'min:2', 'max:10'],
            'seats_per_row' => ['required', 'integer', 'min:4', 'max:16'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $event = Event::create($validated);
        $event->generateSeats();

        return redirect()->route('events.show', $event)->with('status', 'Show created and seating grid initialized.');
    }

    public function edit(Event $event): View
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:Cinema,Concert,Bus Transit,Theater'],
            'venue' => ['required', 'string', 'max:255'],
            'show_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'ticket_price' => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        $event->update($validated);

        return redirect()->route('events.show', $event)->with('status', 'Event details updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $event->delete();

        return redirect()->route('events.index')->with('status', 'Event deleted.');
    }
}
