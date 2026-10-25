<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bookings & Tickets') }}
            </h2>
            <a href="{{ route('events.index') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700 transition">
                + New Reservation
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3">Booking Code</th>
                                <th class="px-6 py-3">Show / Event</th>
                                <th class="px-6 py-3">Seats</th>
                                <th class="px-6 py-3">Customer</th>
                                <th class="px-6 py-3">Total Paid</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-rose-600">
                                        {{ $booking->booking_code }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $booking->event->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->event->show_date->format('M d, Y') }} &bull; {{ $booking->event->start_time }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">
                                        {{ $booking->total_seats }} {{ Str::plural('seat', $booking->total_seats) }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        <div class="font-medium text-gray-900">{{ $booking->customer_name }}</div>
                                        <div>{{ $booking->customer_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-emerald-700">
                                        ${{ number_format($booking->total_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($booking->status === 'confirmed')
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">Confirmed</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-rose-50 text-rose-700">Cancelled</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('bookings.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs">View Pass</a>
                                        @if($booking->status === 'confirmed')
                                            <form method="POST" action="{{ route('bookings.destroy', $booking) }}" class="inline" onsubmit="return confirm('Cancel this reservation? Seats will be released back to the event.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 font-semibold text-xs">Cancel</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        No reservations made yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($bookings->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
