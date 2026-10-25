<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Seat Reservation Dashboard') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('events.index') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-700 transition">
                    Browse Shows & Pick Seats
                </a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('events.create') }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                        + New Event / Show
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Shows / Trips</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $stats['total_events'] }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Available for reservation</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ Auth::user()->isAdmin() ? 'Total Bookings' : 'My Bookings' }}
                    </p>
                    <p class="text-3xl font-extrabold text-rose-600 mt-2">{{ $stats['total_bookings'] }}</p>
                    <p class="text-xs text-emerald-600 font-medium mt-1">Confirmed status</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Reserved Seats</p>
                    <p class="text-3xl font-extrabold text-indigo-600 mt-2">{{ $stats['total_seats_reserved'] }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Individual tickets issued</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        {{ Auth::user()->isAdmin() ? 'Total Revenue' : 'Total Spent' }}
                    </p>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">${{ number_format($stats['total_spent'], 2) }}</p>
                    <p class="text-xs text-gray-400 font-medium mt-1">Confirmed transactions</p>
                </div>
            </div>

            <!-- Content Split -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Upcoming Shows -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">Featured Shows & Events</h3>
                        <a href="{{ route('events.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                            View All Shows &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($upcomingEvents as $evt)
                            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-md transition flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-50 text-rose-700">
                                            {{ $evt->category }}
                                        </span>
                                        <span class="text-xs font-bold text-emerald-600">${{ number_format($evt->ticket_price, 2) }}</span>
                                    </div>
                                    <h4 class="font-bold text-gray-900 mt-2">{{ $evt->title }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $evt->venue }}</p>
                                    <p class="text-xs font-medium text-slate-700 mt-2">
                                        {{ $evt->show_date->format('l, M d') }} &bull; {{ $evt->start_time }}
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t flex justify-between items-center">
                                    <span class="text-xs text-gray-400">{{ $evt->seats_per_row * $evt->total_rows }} total seats</span>
                                    <a href="{{ route('events.show', $evt) }}" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-700 transition">
                                        Select Seats &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Bookings Feed -->
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">My Recent Tickets</h3>
                        <a href="{{ route('bookings.index') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                            All Tickets &rarr;
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($myBookings as $bk)
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs space-y-1">
                                <div class="flex justify-between font-bold">
                                    <span class="text-gray-900 truncate max-w-[140px]">{{ $bk->event->title }}</span>
                                    <span class="text-emerald-700 font-mono">${{ number_format($bk->total_price, 2) }}</span>
                                </div>
                                <div class="text-gray-500 flex justify-between">
                                    <span>Code: <strong class="text-indigo-600 font-mono">{{ $bk->booking_code }}</strong></span>
                                    <span>{{ $bk->total_seats }} {{ Str::plural('seat', $bk->total_seats) }}</span>
                                </div>
                                <div class="pt-1 flex justify-between items-center text-[11px]">
                                    <span class="text-gray-400">{{ $bk->event->show_date->format('M d') }}</span>
                                    <a href="{{ route('bookings.show', $bk) }}" class="text-rose-600 font-semibold hover:underline">View Pass &rarr;</a>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 italic py-6 text-center">No bookings yet. Pick a show above!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
