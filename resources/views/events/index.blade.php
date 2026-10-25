<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Shows & Seating Matrix') }}
            </h2>
            @if(Auth::user()->isAdmin())
                <a href="{{ route('events.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800 transition">
                    + Add New Show
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('events.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search show title, venue..." class="w-full text-sm rounded-lg border-gray-300">
                    </div>
                    <div>
                        <select name="category" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">All Categories</option>
                            <option value="Cinema" {{ request('category') === 'Cinema' ? 'selected' : '' }}>Cinema</option>
                            <option value="Concert" {{ request('category') === 'Concert' ? 'selected' : '' }}>Concert</option>
                            <option value="Theater" {{ request('category') === 'Theater' ? 'selected' : '' }}>Theater</option>
                            <option value="Bus Transit" {{ request('category') === 'Bus Transit' ? 'selected' : '' }}>Bus Transit</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-700">Filter</button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('events.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-semibold hover:bg-gray-200">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase bg-rose-50 text-rose-700">
                                    {{ $event->category }}
                                </span>
                                <span class="text-base font-extrabold text-emerald-600">
                                    ${{ number_format($event->ticket_price, 2) }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mt-3">{{ $event->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $event->venue }}</span>
                            </p>

                            <div class="mt-4 p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs space-y-1">
                                <div class="flex justify-between text-gray-600">
                                    <span>Date & Time:</span>
                                    <span class="font-bold text-gray-900">{{ $event->show_date->format('M d, Y') }} &bull; {{ $event->start_time }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Seating Grid:</span>
                                    <span class="font-medium text-gray-900">{{ $event->total_rows }} rows &times; {{ $event->seats_per_row }} seats</span>
                                </div>
                            </div>

                            @if($event->description)
                                <p class="text-xs text-gray-500 mt-3 line-clamp-2">{{ $event->description }}</p>
                            @endif
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                            @if(Auth::user()->isAdmin())
                                <div class="flex gap-2">
                                    <a href="{{ route('events.edit', $event) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Edit</a>
                                    <form method="POST" action="{{ route('events.destroy', $event) }}" class="inline" onsubmit="return confirm('Delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-500 hover:text-rose-700">Delete</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">{{ $event->seats_count }} seats</span>
                            @endif

                            <a href="{{ route('events.show', $event) }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700 transition">
                                Select Seats &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white p-8 rounded-xl text-center text-gray-500">
                        No events or shows found.
                    </div>
                @endforelse
            </div>

            @if($events->hasPages())
                <div class="bg-white p-4 rounded-xl border border-gray-100">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
