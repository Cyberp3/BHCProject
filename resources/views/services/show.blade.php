<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            @if ($serviceRecord->patient)
                <a href="{{ route('patients.show', $serviceRecord->patient) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Back to {{ $serviceRecord->patient->full_name }} Profile
                </a>
            @else
                <a href="{{ route('services.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                    &larr; Back to Consultation Logs
                </a>
            @endif
            <a href="{{ route('services.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                All Services Log &rarr;
            </a>
        </div>

        <!-- PHI Confidentiality Notice -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 py-2.5 bg-slate-100/90 border border-slate-200 rounded-lg text-xs text-slate-600 gap-2">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="font-semibold text-slate-700">Protected Health Information (PHI)</span>
                <span>&bull;</span>
                <span class="text-slate-500">Official Clinical Care Record (RA 10173 Compliant)</span>
            </div>
            @if (Auth::user()->isAdmin())
                <span class="text-[11px] font-semibold px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded border border-amber-200 self-start sm:self-auto">
                    Administrative Oversight (Read-Only)
                </span>
            @else
                <span class="text-[11px] font-semibold px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded border border-emerald-200 self-start sm:self-auto">
                    Attending Healthcare Personnel View
                </span>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                        {{ $serviceRecord->service_title }}
                    </span>
                    <h1 class="text-xl font-bold text-slate-900 mt-2">Consultation Record</h1>
                    <p class="text-xs text-slate-500">Conducted on {{ $serviceRecord->service_date->format('F d, Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 block">Attending Staff</span>
                    <span class="text-sm font-semibold text-slate-800">{{ $serviceRecord->user ? $serviceRecord->user->name : 'System' }}</span>
                </div>
            </div>

            <!-- Patient Info Banner -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between text-xs">
                <div>
                    <span class="text-slate-500">Patient:</span>
                    @if ($serviceRecord->patient)
                        <a href="{{ route('patients.show', $serviceRecord->patient) }}" class="font-bold text-slate-800 hover:text-emerald-600 ml-1">
                            {{ $serviceRecord->patient->full_name }}
                        </a>
                        @if ($serviceRecord->patient->trashed())
                            <span class="inline-block px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded text-[10px] font-semibold ml-1">Archived</span>
                        @endif
                        <span class="text-slate-400 font-mono ml-1">({{ $serviceRecord->patient->patient_control_number }})</span>
                    @else
                        <span class="font-bold text-slate-400 italic ml-1">Archived / Unavailable</span>
                    @endif
                </div>
                <div>
                    <span class="text-slate-500">Purok:</span>
                    <span class="font-semibold text-slate-800 ml-1">{{ $serviceRecord->patient?->purok?->name ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="p-6 space-y-6 text-sm">
                <!-- Complaint -->
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Chief Complaint / Reason for Visit</span>
                    <p class="text-slate-800 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        {{ $serviceRecord->complaint_or_reason ?? 'Routine follow-up / intake' }}
                    </p>
                </div>

                <!-- Program Specific Data -->
                @if (!empty($serviceRecord->service_specific_data))
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">Program-Specific Parameters</span>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            @foreach ($serviceRecord->service_specific_data as $key => $val)
                                <div>
                                    <span class="text-slate-500 font-medium uppercase tracking-wider block">{{ str_replace('_', ' ', $key) }}</span>
                                    <span class="text-slate-900 font-semibold">{{ filled($val) ? (is_array($val) ? implode(', ', $val) : $val) : 'N/A' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Findings and Notes -->
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Clinical Findings & Care Delivered</span>
                    <div class="text-slate-800 bg-slate-50 p-4 rounded-lg border border-slate-200 whitespace-pre-line leading-relaxed">
                        {{ $serviceRecord->findings_and_notes ?? 'No additional clinical notes recorded.' }}
                    </div>
                </div>

                <!-- Next Follow-up -->
                @if ($serviceRecord->next_follow_up_date)
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <div class="text-xs font-bold text-blue-900">Next Follow-up Due</div>
                                <div class="text-sm font-semibold text-blue-700">{{ $serviceRecord->next_follow_up_date->format('l, F d, Y') }}</div>
                            </div>
                        </div>
                        <a href="{{ route('appointments.index') }}" class="text-xs text-blue-800 hover:underline font-semibold">
                            View in Appointments &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <div class="px-6 py-3 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between text-[11px] text-slate-400 gap-1">
                <span>🔒 Confidentiality Notice: This medical record is protected under RA 10173 and DOH Clinical Practice Guidelines.</span>
                <span class="font-medium text-slate-500">Barangay Tacunan Health Center</span>
            </div>
        </div>
    </div>
</x-app-layout>

