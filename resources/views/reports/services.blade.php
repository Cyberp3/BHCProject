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
                <h2 class="text-sm font-semibold text-blue-700 mt-0.5">Health Services & Programs Utilization Report</h2>
                <p class="text-xs text-slate-400 mt-1">
                    Reporting Period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                </p>
            </div>

            <!-- Date Range Filter -->
            <div class="my-6 p-4 bg-slate-50 rounded-xl border border-slate-200 no-print">
                <form method="GET" action="{{ route('reports.services') }}" class="flex flex-wrap items-center gap-4">
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

            <!-- Program Summary Table -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Service Utilization by Program</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">Health Program Name</th>
                                <th class="p-2.5 border text-center">Consultations / Visits</th>
                                <th class="p-2.5 border text-center">Share of Total Services</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalConsultations = array_sum($countsByService);
                            @endphp
                            @foreach ($allServices as $key => $title)
                                @php
                                    $count = $countsByService[$key] ?? 0;
                                    $percentage = $totalConsultations > 0 ? round(($count / $totalConsultations) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border font-semibold">{{ $title }}</td>
                                    <td class="p-2.5 border text-center font-bold">{{ $count }}</td>
                                    <td class="p-2.5 border text-center">{{ $percentage }}%</td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50 font-bold">
                                <td class="p-2.5 border uppercase">Total Consultations</td>
                                <td class="p-2.5 border text-center text-emerald-700 font-extrabold text-sm">{{ $totalConsultations }}</td>
                                <td class="p-2.5 border text-center">100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Consultation Log -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Consultation Records Log ({{ $records->count() }} Total)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">Date</th>
                                <th class="p-2.5 border">Patient</th>
                                <th class="p-2.5 border">Purok</th>
                                <th class="p-2.5 border">Program</th>
                                <th class="p-2.5 border">Findings / Complaint</th>
                                <th class="p-2.5 border">Attending Staff</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $record)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border font-medium">{{ $record->service_date->format('M d, Y') }}</td>
                                    <td class="p-2.5 border font-semibold">{{ $record->patient->full_name }}</td>
                                    <td class="p-2.5 border">{{ $record->patient->purok->name }}</td>
                                    <td class="p-2.5 border">{{ $record->service_title }}</td>
                                    <td class="p-2.5 border max-w-xs truncate">{{ $record->findings_and_notes ?? $record->complaint_or_reason ?? '-' }}</td>
                                    <td class="p-2.5 border">{{ $record->user ? $record->user->name : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">No consultations logged within this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

