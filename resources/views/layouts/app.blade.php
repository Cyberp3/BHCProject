<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Tacunan Integrated Health System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full font-sans antialiased text-slate-800 bg-slate-50">
        <div class="min-h-screen flex">
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Page Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Top Header -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Barangay Tacunan Health Center</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Davao City</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-xs text-slate-500 font-medium hidden sm:block">
                            {{ now()->format('l, F d, Y') }}
                        </div>

                        <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-slate-700">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                {{ Auth::user()->role }}
                            </span>
                        </div>
                    </div>
                </header>

                <!-- Optional Header Slot -->
                @isset($header)
                    <div class="bg-white border-b border-slate-200 py-4 px-6">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Flash Notifications -->
                <div class="px-6 pt-4">
                    @if (session('success'))
                        <div class="p-4 mb-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 text-sm shadow-xs">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="font-medium">{{ session('success') }}</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="p-4 mb-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 text-sm shadow-xs">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="font-medium">{{ session('error') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-4 mb-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900 text-sm shadow-xs">
                            <div class="font-semibold mb-1 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Please resolve the following errors:
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-xs text-amber-800 ml-6">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Page Content Body -->
                <main class="flex-1 overflow-y-auto p-6 bg-slate-50">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
