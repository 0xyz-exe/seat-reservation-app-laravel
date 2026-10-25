<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Event / Showtime') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <form method="POST" action="{{ route('events.store') }}" class="space-y-6">
                    @csrf

                    @if ($errors->any())
                        <div class="p-4 bg-rose-50 text-rose-800 rounded-lg text-sm">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Event / Show Title *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Dune: Part Two (IMAX)" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Category *</label>
                            <select name="category" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                <option value="Cinema">Cinema</option>
                                <option value="Concert">Concert</option>
                                <option value="Theater">Theater</option>
                                <option value="Bus Transit">Bus Transit</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Venue / Hall *</label>
                            <input type="text" name="venue" value="{{ old('venue') }}" required placeholder="e.g. Dolby Cinema Hall 2" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Date *</label>
                            <input type="date" name="show_date" value="{{ old('show_date', date('Y-m-d')) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Start Time *</label>
                            <input type="text" name="start_time" value="{{ old('start_time', '19:30') }}" required placeholder="19:30" class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Base Ticket Price ($) *</label>
                            <input type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', 14.50) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Grid Rows (A-Z) *</label>
                            <input type="number" name="total_rows" value="{{ old('total_rows', 5) }}" min="2" max="10" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                            <p class="text-[11px] text-gray-400 mt-0.5">Rows A through E</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Seats per Row *</label>
                            <input type="number" name="seats_per_row" value="{{ old('seats_per_row', 8) }}" min="4" max="16" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                            <p class="text-[11px] text-gray-400 mt-0.5">Columns 1 to 8 with center aisle</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Description</label>
                        <textarea name="description" rows="3" placeholder="Additional show notes, age rating, duration..." class="mt-1 w-full text-sm rounded-lg border-gray-300">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800">Generate Seating & Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
