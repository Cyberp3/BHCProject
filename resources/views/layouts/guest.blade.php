<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tacunan Integrated Health System') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950 min-h-screen flex flex-col justify-center items-center p-4">
        <div class="w-full max-w-md">
            <!-- Health Center Header Branding -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-600 text-white shadow-xl shadow-emerald-950 mb-3">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <h1 class="text-xl font-extrabold text-white tracking-tight">Barangay Tacunan Health Center</h1>
                <p class="text-xs font-semibold text-emerald-400 mt-1 uppercase tracking-wider">Integrated Health & Inventory Management System</p>
                <p class="text-[11px] text-slate-400">Davao City, Philippines</p>
            </div>

            <!-- Card Container -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 border border-slate-100">
                {{ $slot }}
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                Systems Integration and Architecture Project &bull; 2026
            </p>
        </div>
    </body>
</html>
