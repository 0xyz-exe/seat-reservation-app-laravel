<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Show: ') }} {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <form method="POST" action="{{ route('events.update', $event) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Event / Show Title *</label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Category *</label>
                            <select name="category" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                @foreach(['Cinema', 'Concert', 'Theater', 'Bus Transit'] as $cat)
                                    <option value="{{ $cat }}" {{ old('category', $event->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Venue / Hall *</label>
                            <input type="text" name="venue" value="{{ old('venue', $event->venue) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Date *</label>
                            <input type="date" name="show_date" value="{{ old('show_date', $event->show_date->format('Y-m-d')) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Start Time *</label>
                            <input type="text" name="start_time" value="{{ old('start_time', $event->start_time) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Base Ticket Price ($) *</label>
                            <input type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', $event->ticket_price) }}" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Status *</label>
                            <select name="status" required class="mt-1 w-full text-sm rounded-lg border-gray-300">
                                <option value="active" {{ old('status', $event->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase">Description</label>
                        <textarea name="description" rows="3" class="mt-1 w-full text-sm rounded-lg border-gray-300">{{ old('description', $event->description) }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('events.show', $event) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800">Update Show</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
