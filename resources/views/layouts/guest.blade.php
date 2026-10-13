<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TacunaCare') }} - Personnel Login</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 min-h-screen flex flex-col justify-center items-center p-4">
        <div class="w-full max-w-md">
            <!-- Health Center Header Branding -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl overflow-hidden bg-white shadow-2xl shadow-emerald-950/60 mb-3 border-2 border-emerald-400/40 p-1.5 transition transform hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="TacunaCare Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="text-3xl font-black text-white tracking-tight flex items-center justify-center gap-0.5">
                    Tacuna<span class="text-emerald-400">Care</span>
                </h1>
                <p class="text-xs font-semibold text-emerald-300 mt-1 uppercase tracking-wider">Integrated Health & Inventory Management System</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Barangay Tacunan Health Center &bull; Davao City</p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100/80">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                Systems Integration and Architecture Project &bull; 2026
            </p>
        </div>

        <!-- Back-Button & BfCache Security Guard -->
        <script>
            // Invalidate browser Back-Forward cache to enforce live session check
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || (window.performance && window.performance.getEntriesByType && window.performance.getEntriesByType('navigation')[0] && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
                    window.location.reload();
                }
            });
        </script>
    </body>
</html>
