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

        <!-- Official Report Paper Sheet Container -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 print:p-0 print:border-none print:shadow-none">
            <!-- Official Letterhead Header -->
            <x-report-header 
                title="Patient Profiling & Purok Distribution Report" 
                subtitle="Barangay Health Registry & Demographics Assessment" 
            />

            <!-- Aggregate Demographic Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
                <div class="p-4 bg-slate-50 rounded-lg text-center border border-slate-200">
                    <span class="text-xs font-semibold text-slate-500 uppercase">Total Patients</span>
                    <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalPatients) }}</div>
                </div>
                <div class="p-4 bg-blue-50/60 rounded-lg text-center border border-blue-100">
                    <span class="text-xs font-semibold text-blue-700 uppercase">Male Patients</span>
                    <div class="text-2xl font-extrabold text-blue-900 mt-1">{{ number_format($maleCount) }}</div>
                </div>
                <div class="p-4 bg-rose-50/60 rounded-lg text-center border border-rose-100">
                    <span class="text-xs font-semibold text-rose-700 uppercase">Female Patients</span>
                    <div class="text-2xl font-extrabold text-rose-900 mt-1">{{ number_format($femaleCount) }}</div>
                </div>
            </div>

            <!-- Purok Distribution Table -->
            <div class="mt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Breakdown by Purok</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">Purok Name</th>
                                <th class="p-2.5 border text-center">Registered Residents</th>
                                <th class="p-2.5 border text-center">Percentage of Population</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purokStats as $purok)
                                @php
                                    $pct = $totalPatients > 0 ? round(($purok->patients_count / $totalPatients) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border font-semibold">{{ $purok->name }}</td>
                                    <td class="p-2.5 border text-center font-bold">{{ $purok->patients_count }}</td>
                                    <td class="p-2.5 border text-center">{{ $pct }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Patient Listing Filter -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 no-print">
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Patient Registry Roster</h3>
                        <span class="text-xs text-slate-500 font-medium">({{ number_format($patientList->total()) }} Registered)</span>
                    </div>
                    <form method="GET" action="{{ route('reports.patients') }}" class="flex flex-wrap items-center gap-2">
                        <select name="purok_id" class="py-1.5 px-3 text-xs rounded-lg border-slate-300">
                            <option value="">-- All Puroks --</option>
                            @foreach ($purokStats as $purok)
                                <option value="{{ $purok->id }}" {{ $selectedPurokId == $purok->id ? 'selected' : '' }}>
                                    {{ $purok->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="per_page" class="py-1.5 px-3 text-xs rounded-lg border-slate-300">
                            <option value="10" {{ request('per_page', 10) == '10' ? 'selected' : '' }}>10 / page</option>
                            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 / page</option>
                            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 / page</option>
                            <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 / page</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Show All</option>
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold">Filter</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">#</th>
                                <th class="p-2.5 border">Control Number</th>
                                <th class="p-2.5 border">Full Name</th>
                                <th class="p-2.5 border">Sex</th>
                                <th class="p-2.5 border">Age</th>
                                <th class="p-2.5 border">Purok</th>
                                <th class="p-2.5 border">Contact Number</th>
                                <th class="p-2.5 border">PhilHealth ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($patientList as $index => $patient)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border text-slate-400">{{ $patientList->firstItem() ? ($patientList->firstItem() + $index) : ($index + 1) }}</td>
                                    <td class="p-2.5 border font-mono font-bold">{{ $patient->patient_control_number }}</td>
                                    <td class="p-2.5 border font-semibold">{{ $patient->full_name }}</td>
                                    <td class="p-2.5 border">{{ $patient->sex }}</td>
                                    <td class="p-2.5 border">{{ $patient->age }}</td>
                                    <td class="p-2.5 border">{{ $patient->purok->name }}</td>
                                    <td class="p-2.5 border">{{ $patient->contact_number ?? '-' }}</td>
                                    <td class="p-2.5 border font-mono">{{ $patient->philhealth_number ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-6 text-center text-slate-400">No patient records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($patientList->hasPages())
                    <div class="p-4 border-t border-slate-200 no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Showing {{ $patientList->firstItem() }} to {{ $patientList->lastItem() }} of {{ number_format($patientList->total()) }} patients
                        </div>
                        {{ $patientList->links() }}
                    </div>
                @endif
            </div>

            <!-- Official Certification & Signatory Section -->
            <x-report-footer />
        </div>
    </div>
</x-app-layout>

