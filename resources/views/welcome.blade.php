<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'SeatPass') }} - Real-time Seat Reservation</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-slate-900 text-slate-100">
        <div class="min-h-screen flex flex-col justify-between">
            <header class="max-w-7xl w-full mx-auto px-6 py-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-900/50">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">SeatPass</span>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-500 transition shadow">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-sm font-semibold hover:bg-rose-500 transition shadow">Register</a>
                        @endif
                    @endauth
                </div>
            </header>

            <main class="max-w-7xl mx-auto px-6 py-12 flex-1 flex flex-col justify-center">
                <div class="text-center max-w-3xl mx-auto space-y-6">
                    <span class="px-3 py-1 bg-rose-950/80 border border-rose-800 text-rose-300 rounded-full text-xs font-semibold uppercase tracking-wider">Theater & Transit Reservation System</span>
                    <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight">
                        Interactive Visual Seat Selection & Instant Booking
                    </h1>
                    <p class="text-lg text-slate-400 leading-relaxed">
                        Pick your preferred seats directly on a real-time visual grid map. Real-time availability status, center aisle spacing, VIP tier pricing, and instant digital pass generation.
                    </p>
                    <div class="flex flex-wrap justify-center gap-4 pt-4">
                        @auth
                            <a href="{{ route('events.index') }}" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-semibold shadow-lg shadow-rose-950 hover:bg-rose-500 transition">Browse Shows & Reserve Seats</a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-semibold shadow-lg shadow-rose-950 hover:bg-rose-500 transition">Explore Live Demo</a>
                        @endauth
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 text-slate-200">
                    <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-sm">
                        <div class="w-12 h-12 bg-rose-900/50 text-rose-400 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Visual Seat Matrix</h3>
                        <p class="text-sm text-slate-400">Interactive color-coded seat layout with stage/screen orientation and live row/column coordinate labeling.</p>
                    </div>

                    <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-sm">
                        <div class="w-12 h-12 bg-emerald-900/50 text-emerald-400 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Multi-Seat Cart</h3>
                        <p class="text-sm text-slate-400">Select multiple seats concurrently with automatic live subtotal calculations and double-booking conflict prevention.</p>
                    </div>

                    <div class="bg-slate-800/80 p-6 rounded-2xl border border-slate-700 shadow-sm">
                        <div class="w-12 h-12 bg-indigo-900/50 text-indigo-400 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Digital Pass & Receipt</h3>
                        <p class="text-sm text-slate-400">Instant digital ticket generation with verified booking reference codes, venue details, and seat assignment badges.</p>
                    </div>
                </div>
            </main>

            <footer class="max-w-7xl w-full mx-auto px-6 py-6 border-t border-slate-800 text-center text-xs text-slate-500">
                SeatPass &bull; Laravel 13 &bull; SQLite &bull; Breeze &bull; Tailwind CSS
            </footer>
        </div>
    </body>
</html>
