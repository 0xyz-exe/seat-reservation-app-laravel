<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Digital Ticket: ') }} {{ $booking->booking_code }}
            </h2>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700">
                    Print Ticket
                </button>
                <a href="{{ route('bookings.index') }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200">
                    &larr; All Bookings
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Ticket Card (Boarding Pass Layout) -->
            <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden">
                <!-- Top Brand Header -->
                <div class="bg-rose-600 text-white p-6 flex justify-between items-center">
                    <div>
                        <span class="text-xs uppercase tracking-widest font-extrabold text-rose-200">SEATPASS DIGITAL PASS</span>
                        <h3 class="text-2xl font-black mt-1">{{ $booking->event->title }}</h3>
                        <p class="text-xs text-rose-100 mt-0.5">{{ $booking->event->venue }} &bull; {{ $booking->event->category }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-rose-200 uppercase font-semibold">Status</span>
                        <div class="mt-1">
                            @if($booking->status === 'confirmed')
                                <span class="px-3 py-1 bg-white text-rose-700 rounded-full text-xs font-black uppercase">Confirmed</span>
                            @else
                                <span class="px-3 py-1 bg-rose-900 text-rose-200 rounded-full text-xs font-black uppercase">Cancelled</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Ticket Details Body -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase">Show Date</span>
                            <p class="text-sm font-bold text-gray-900 mt-1">{{ $booking->event->show_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase">Time</span>
                            <p class="text-sm font-bold text-gray-900 mt-1">{{ $booking->event->start_time }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase">Passes</span>
                            <p class="text-sm font-bold text-gray-900 mt-1">{{ $booking->total_seats }} {{ Str::plural('Ticket', $booking->total_seats) }}</p>
                        </div>
                        <div>
                            <span class="text-[11px] font-semibold text-gray-400 uppercase">Total Paid</span>
                            <p class="text-sm font-bold text-emerald-600 mt-1 font-mono">${{ number_format($booking->total_price, 2) }}</p>
                        </div>
                    </div>

                    <!-- Reserved Seats Badges -->
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Assigned Seat Numbers</span>
                        <div class="flex flex-wrap gap-2 mt-3">
                            @foreach($booking->seats as $seat)
                                <div class="px-4 py-2 rounded-xl bg-slate-900 text-white font-mono font-bold text-sm shadow flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-400" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"></path></svg>
                                    <span>{{ $seat->seat_number }}</span>
                                    <span class="text-[10px] text-gray-400 uppercase font-sans">({{ $seat->seat_type }})</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Passenger / Customer Info -->
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 text-xs grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-400 uppercase">Ticket Holder:</span>
                            <p class="font-bold text-gray-900 mt-0.5">{{ $booking->customer_name }}</p>
                            <p class="text-gray-500">{{ $booking->customer_email }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase">Booking Reference:</span>
                            <p class="font-mono font-bold text-rose-600 text-base mt-0.5">{{ $booking->booking_code }}</p>
                            <p class="text-gray-400">Reserved on {{ $booking->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- Visual Barcode Representation -->
                    <div class="pt-4 border-t border-dashed border-gray-300 text-center">
                        <div class="h-12 max-w-sm mx-auto flex justify-between items-center px-4 bg-slate-900 rounded-lg">
                            @for ($i = 0; $i < 36; $i++)
                                <div class="bg-white h-8" style="width: {{ ($i % 3 == 0) ? '3px' : (($i % 2 == 0) ? '1px' : '2px') }};"></div>
                            @endfor
                        </div>
                        <p class="text-[10px] font-mono tracking-widest text-gray-400 mt-2">{{ $booking->booking_code }}-VERIFIED-PASS</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
