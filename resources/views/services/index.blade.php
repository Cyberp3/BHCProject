<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Health Center Services</h1>
                <p class="text-sm text-slate-500 mt-1">Official health programs and service delivery records for Barangay Tacunan.</p>
            </div>
            <div>
                <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Consultation Record
                </a>
            </div>
        </div>

        <!-- The 8 Health Center Programs Grid -->
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Supported Health Programs</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $programIcons = [
                        'family_planning' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'prenatal_care' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'immunization' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                        'cvd_screening' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'philpen' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'ntp' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
                        'purok_kalusugan' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'bns_program' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                    ];
                @endphp

                @foreach ($services as $key => $title)
                    <div class="bg-white rounded-xl border border-slate-200 shadow-2xs p-4 flex flex-col justify-between hover:border-emerald-300 transition">
                        <div>
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $programIcons[$key] ?? 'M9 12h6m-6 4h6' }}" />
                                </svg>
                            </div>
                            <h3 class="font-bold text-sm text-slate-800">{{ $title }}</h3>
                            <p class="text-xs text-slate-400 mt-1">Barangay Tacunan health program</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('services.index', ['service_type' => $key]) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                                Filter records &rarr;
                            </a>
                            <a href="{{ route('services.create', ['type' => $key]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                                + Log visit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Filter & Search Logs -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('services.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by patient name or control number..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <select name="service_type" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- All Health Programs --</option>
                        @foreach ($services as $key => $title)
                            <option value="{{ $key }}" {{ request('service_type') == $key ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'service_type']))
                        <a href="{{ route('services.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Consultation Logs Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Date</th>
                            <th class="px-5 py-3.5">Patient</th>
                            <th class="px-5 py-3.5">Purok</th>
                            <th class="px-5 py-3.5">Program</th>
                            <th class="px-5 py-3.5">Findings / Complaint</th>
                            <th class="px-5 py-3.5">Attending Staff</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($records as $record)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 font-medium text-slate-900 text-xs">
                                    {{ $record->service_date->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('patients.show', $record->patient) }}" class="font-semibold text-slate-900 hover:text-emerald-600">
                                        {{ $record->patient->full_name }}
                                    </a>
                                    <div class="text-[11px] font-mono text-slate-400">{{ $record->patient->patient_control_number }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs font-medium text-slate-700">
                                    {{ $record->patient->purok->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ $record->service_title }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-600 max-w-xs truncate">
                                    {{ $record->findings_and_notes ?? $record->complaint_or_reason ?? 'Consultation logged' }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    {{ $record->user ? $record->user->name : 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('services.show', $record) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    No consultation records found matching your filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($records->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

