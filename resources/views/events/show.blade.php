<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $event->title }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $event->venue }} &bull; {{ $event->show_date->format('l, F d, Y') }} at {{ $event->start_time }}
                </p>
            </div>
            <a href="{{ route('events.index') }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-200">
                &larr; All Shows
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        selectedSeats: [],
        seatPrices: {},
        toggleSeat(id, number, price) {
            const index = this.selectedSeats.findIndex(s => s.id === id);
            if (index > -1) {
                this.selectedSeats.splice(index, 1);
            } else {
                this.selectedSeats.push({ id: id, number: number, price: parseFloat(price) });
            }
        },
        isSelected(id) {
            return this.selectedSeats.some(s => s.id === id);
        },
        totalPrice() {
            return this.selectedSeats.reduce((sum, s) => sum + s.price, 0).toFixed(2);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Layout Split: Seat Map & Reservation Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Visual Seat Map Card -->
                <div class="lg:col-span-2 bg-slate-900 text-white rounded-2xl p-6 sm:p-8 shadow-lg flex flex-col justify-between">
                    <div>
                        <!-- Screen / Stage Banner -->
                        <div class="mb-10 text-center">
                            <div class="h-3 w-3/4 mx-auto bg-gradient-to-r from-rose-500 via-indigo-500 to-rose-500 rounded-full shadow-[0_0_20px_rgba(244,63,94,0.6)]"></div>
                            <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mt-2">
                                SCREEN / STAGE ORIENTATION
                            </span>
                        </div>

                        <!-- Seating Grid -->
                        <div class="space-y-4 max-w-lg mx-auto">
                            @foreach($seatsByRow as $rowLetter => $seats)
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Row Letter Indicator -->
                                    <span class="w-6 text-center font-bold text-xs text-slate-400 font-mono">{{ $rowLetter }}</span>

                                    <!-- Seats in this Row -->
                                    <div class="flex gap-2 items-center">
                                        @foreach($seats as $seat)
                                            @php
                                                $isBooked = in_array($seat->id, $bookedSeatIds);
                                                $isCenterAisle = ($seat->column_number == floor($event->seats_per_row / 2));
                                            @endphp

                                            <button 
                                                type="button"
                                                @if($isBooked) disabled @else @click="toggleSeat({{ $seat->id }}, '{{ $seat->seat_number }}', {{ $seat->price }})" @endif
                                                class="w-9 h-9 rounded-lg flex items-center justify-center text-xs font-bold font-mono transition-all transform hover:scale-105 relative
                                                {{ $isBooked 
                                                    ? 'bg-slate-800 text-slate-600 border border-slate-700 cursor-not-allowed opacity-50' 
                                                    : '' }}"
                                                :class="isSelected({{ $seat->id }}) 
                                                    ? 'bg-rose-600 text-white shadow-lg shadow-rose-900/60 ring-2 ring-white scale-105' 
                                                    : (!{{ $isBooked ? 'true' : 'false' }} ? '{{ $seat->seat_type === 'vip' ? 'bg-indigo-950 text-indigo-300 border border-indigo-700 hover:bg-indigo-900' : 'bg-slate-800 text-slate-200 border border-slate-600 hover:bg-slate-700' }}' : '')"
                                                title="Seat {{ $seat->seat_number }} ({{ ucfirst($seat->seat_type) }}) - ${{ number_format($seat->price, 2) }}">
                                                @if($isBooked)
                                                    <span class="text-rose-500/80 text-[10px]">&times;</span>
                                                @else
                                                    <span>{{ $seat->column_number }}</span>
                                                @endif
                                            </button>

                                            @if($isCenterAisle)
                                                <!-- Center Aisle Gap -->
                                                <div class="w-4"></div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <!-- Right Row Letter -->
                                    <span class="w-6 text-center font-bold text-xs text-slate-400 font-mono">{{ $rowLetter }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="mt-10 pt-6 border-t border-slate-800 flex flex-wrap justify-center gap-6 text-xs text-slate-300">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-slate-800 border border-slate-600"></div>
                            <span>Available (${{ number_format($event->ticket_price, 2) }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-indigo-950 border border-indigo-700"></div>
                            <span>VIP Row</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-rose-600"></div>
                            <span>Selected</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-slate-800 border border-slate-700 opacity-50 flex items-center justify-center text-rose-500 font-bold">&times;</div>
                            <span>Booked</span>
                        </div>
                    </div>
                </div>

                <!-- Reservation Sidebar & Booking Form -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg mb-1">Reservation Summary</h3>
                        <p class="text-xs text-gray-500 mb-6">Review your seat selections and confirm tickets.</p>

                        <!-- Selected Seats Live List -->
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 mb-6 space-y-3">
                            <div class="flex justify-between items-center text-xs font-semibold text-gray-500 uppercase">
                                <span>Selected Seats</span>
                                <span x-text="selectedSeats.length + ' seat(s)'"></span>
                            </div>

                            <template x-if="selectedSeats.length === 0">
                                <p class="text-xs text-gray-400 italic py-2">Click any available seat on the grid to add to your order.</p>
                            </template>

                            <div class="flex flex-wrap gap-1.5" x-show="selectedSeats.length > 0">
                                <template x-for="seat in selectedSeats" :key="seat.id">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                        <span x-text="seat.number"></span>
                                        <button @click="toggleSeat(seat.id, seat.number, seat.price)" type="button" class="text-rose-400 hover:text-rose-700">&times;</button>
                                    </span>
                                </template>
                            </div>

                            <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                                <span class="font-bold text-sm text-gray-900">Total Price:</span>
                                <span class="text-xl font-extrabold text-emerald-600 font-mono" x-text="'$' + totalPrice()"></span>
                            </div>
                        </div>

                        <!-- Booking Submission Form -->
                        <form method="POST" action="{{ route('bookings.store', $event) }}" class="space-y-4">
                            @csrf

                            <!-- Hidden Seat Inputs -->
                            <template x-for="seat in selectedSeats" :key="seat.id">
                                <input type="hidden" name="seats[]" :value="seat.id">
                            </template>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Customer Name *</label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Customer Email *</label>
                                <input type="email" name="customer_email" value="{{ old('customer_email', Auth::user()->email) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Phone Number</label>
                                <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="+1 555-0123" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                            </div>

                            <div class="pt-4">
                                <button 
                                    type="submit" 
                                    :disabled="selectedSeats.length === 0"
                                    class="w-full py-3 bg-rose-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-200 hover:bg-rose-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    Confirm & Reserve Seats
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="text-[11px] text-gray-400 text-center mt-6">
                        Tickets are digitally generated with an instant verification code.
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
