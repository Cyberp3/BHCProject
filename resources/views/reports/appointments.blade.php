<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumb & Print -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Reports
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
            <div class="text-center border-b border-slate-200 pb-4">
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-widest">Republic of the Philippines &bull; City of Davao</h3>
                <h1 class="text-xl font-extrabold text-slate-900 mt-1">Barangay Tacunan Health Center</h1>
                <h2 class="text-sm font-semibold text-indigo-700 mt-0.5">Follow-up & Appointment Compliance Report</h2>
                <p class="text-xs text-slate-400 mt-1">
                    Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </p>
            </div>

            <!-- Date Filter -->
            <div class="my-6 p-4 bg-slate-50 rounded-xl border border-slate-200 no-print">
                <form method="GET" action="{{ route('reports.appointments') }}" class="flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-slate-700">From:</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs rounded-lg border-slate-300">
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-slate-700">To:</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs rounded-lg border-slate-300">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-900 transition">
                        Generate
                    </button>
                </form>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50/60 rounded-lg text-center border border-blue-100">
                    <span class="text-xs font-semibold text-blue-700 uppercase">Scheduled</span>
                    <div class="text-2xl font-extrabold text-blue-900 mt-1">{{ $statusCounts['scheduled'] ?? 0 }}</div>
                </div>
                <div class="p-4 bg-emerald-50/60 rounded-lg text-center border border-emerald-100">
                    <span class="text-xs font-semibold text-emerald-700 uppercase">Attended</span>
                    <div class="text-2xl font-extrabold text-emerald-900 mt-1">{{ $statusCounts['attended'] ?? 0 }}</div>
                </div>
                <div class="p-4 bg-amber-50/60 rounded-lg text-center border border-amber-100">
                    <span class="text-xs font-semibold text-amber-700 uppercase">Missed / Overdue</span>
                    <div class="text-2xl font-extrabold text-amber-900 mt-1">{{ $statusCounts['missed'] ?? 0 }}</div>
                </div>
                <div class="p-4 bg-rose-50/60 rounded-lg text-center border border-rose-100">
                    <span class="text-xs font-semibold text-rose-700 uppercase">Cancelled</span>
                    <div class="text-2xl font-extrabold text-rose-900 mt-1">{{ $statusCounts['cancelled'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Table -->
            <div class="mt-8">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Appointment Log</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">Date & Time</th>
                                <th class="p-2.5 border">Patient</th>
                                <th class="p-2.5 border">Purok</th>
                                <th class="p-2.5 border">Service</th>
                                <th class="p-2.5 border">Purpose</th>
                                <th class="p-2.5 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($appointments as $appt)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border font-medium">
                                        {{ $appt->appointment_date->format('M d, Y') }} {{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : '' }}
                                    </td>
                                    <td class="p-2.5 border font-semibold">{{ $appt->patient->full_name }}</td>
                                    <td class="p-2.5 border">{{ $appt->patient->purok->name }}</td>
                                    <td class="p-2.5 border">{{ ucwords(str_replace('_', ' ', $appt->service_type)) }}</td>
                                    <td class="p-2.5 border">{{ $appt->purpose }}</td>
                                    <td class="p-2.5 border font-bold uppercase">
                                        {{ $appt->status }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">No appointments recorded for this timeframe.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

