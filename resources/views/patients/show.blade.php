<x-app-layout>
    <div class="space-y-6" x-data="{ activeTab: 'assessments' }">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ route('patients.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Back to Patient Directory
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Patient Header Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-2xl shrink-0 border border-emerald-200">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-extrabold text-slate-900">{{ $patient->full_name }}</h1>
                            <span class="font-mono text-xs px-2.5 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200 font-semibold">
                                {{ $patient->patient_control_number }}
                            </span>
                            @if ($patient->blood_type)
                                <span class="text-xs px-2 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                    {{ $patient->blood_type }}
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-500 mt-2">
                            <span><strong class="text-slate-700">Sex:</strong> {{ $patient->sex }}</span>
                            <span>&bull;</span>
                            <span><strong class="text-slate-700">Age:</strong> {{ $patient->age }} years old</span>
                            <span>&bull;</span>
                            <span><strong class="text-slate-700">DOB:</strong> {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</span>
                            <span>&bull;</span>
                            <span><strong class="text-slate-700">Purok:</strong> {{ $patient->purok->name }}</span>
                            <span>&bull;</span>
                            <span><strong class="text-slate-700">Civil Status:</strong> {{ $patient->civil_status ?? 'Not Specified' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('assessments.create', $patient) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        + Record Vitals (BNS)
                    </a>
                    <a href="{{ route('services.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Health Service
                    </a>
                    <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        + Schedule Follow-up
                    </a>
                    <a href="{{ route('inventory.dispense', ['patient_id' => $patient->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Dispense Item
                    </a>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex space-x-6">
                <button @click="activeTab = 'assessments'" :class="activeTab === 'assessments' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Vitals & BNS Assessments ({{ $patient->healthAssessments->count() }})
                </button>
                <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Health Services History ({{ $patient->healthServiceRecords->count() }})
                </button>
                <button @click="activeTab = 'appointments'" :class="activeTab === 'appointments' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Appointments & Follow-ups ({{ $patient->appointments->count() }})
                </button>
                <button @click="activeTab = 'dispensed'" :class="activeTab === 'dispensed' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Dispensed Items ({{ $patient->inventoryTransactions->count() }})
                </button>
                <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Full Demographics & Contacts
                </button>
            </nav>
        </div>

        <!-- TAB 1: VITALS & BNS ASSESSMENTS -->
        <div x-show="activeTab === 'assessments'" class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-base">Recorded Vital Signs & Anthropometrics</h2>
                <a href="{{ route('assessments.create', $patient) }}" class="text-xs text-emerald-600 hover:underline font-semibold">+ Record New Vitals</a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Weight / Height</th>
                                <th class="px-4 py-3">BMI</th>
                                <th class="px-4 py-3">Blood Pressure</th>
                                <th class="px-4 py-3">Pulse / Temp</th>
                                <th class="px-4 py-3">Nutritional Status</th>
                                <th class="px-4 py-3">Staff</th>
                                <th class="px-4 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($patient->healthAssessments as $vitals)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $vitals->assessment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $vitals->weight_kg ? $vitals->weight_kg . ' kg' : '-' }} /
                                        {{ $vitals->height_cm ? $vitals->height_cm . ' cm' : '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $vitals->bmi ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ $vitals->blood_pressure ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $vitals->pulse_rate ? $vitals->pulse_rate . ' bpm' : '-' }} &bull;
                                        {{ $vitals->temperature_celsius ? $vitals->temperature_celsius . ' °C' : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($vitals->nutritional_status)
                                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $vitals->nutritional_status }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $vitals->user ? $vitals->user->name : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 max-w-xs truncate">
                                        {{ $vitals->notes ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        No health assessments or vital signs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: HEALTH SERVICES RECORDS (8 PROGRAMS) -->
        <div x-show="activeTab === 'services'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-base">Health Center Consultations & Programs</h2>
                <a href="{{ route('services.create', ['patient_id' => $patient->id]) }}" class="text-xs text-blue-600 hover:underline font-semibold">+ Add Service Record</a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Program / Service</th>
                                <th class="px-4 py-3">Complaint / Reason</th>
                                <th class="px-4 py-3">Findings & Clinical Notes</th>
                                <th class="px-4 py-3">Attending Staff</th>
                                <th class="px-4 py-3">Next Follow-up</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($patient->healthServiceRecords as $service)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $service->service_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        <span class="px-2 py-0.5 rounded text-[11px] bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $service->service_title }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-700">
                                        {{ $service->complaint_or_reason ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600 max-w-sm truncate">
                                        {{ $service->findings_and_notes ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $service->user ? $service->user->name : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $service->next_follow_up_date ? $service->next_follow_up_date->format('M d, Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('services.show', $service) }}" class="text-blue-600 hover:underline font-semibold">View Details</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                        No health service records found for this patient.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: APPOINTMENTS & FOLLOW-UPS -->
        <div x-show="activeTab === 'appointments'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-base">Appointments & Follow-up Visits</h2>
                <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="text-xs text-slate-800 hover:underline font-semibold">+ Schedule Follow-up</a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Scheduled Date</th>
                                <th class="px-4 py-3">Time</th>
                                <th class="px-4 py-3">Service / Program</th>
                                <th class="px-4 py-3">Purpose</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Scheduled By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($patient->appointments as $appt)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $appt->appointment_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : 'All Day' }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-slate-800">
                                        {{ ucwords(str_replace('_', ' ', $appt->service_type)) }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        {{ $appt->purpose }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $appt->status === 'attended' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($appt->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $appt->scheduledBy ? $appt->scheduledBy->name : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        No scheduled appointments on file for this patient.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: DISPENSED ITEMS -->
        <div x-show="activeTab === 'dispensed'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-base">Medicines & Vaccines Dispensed</h2>
                <a href="{{ route('inventory.dispense', ['patient_id' => $patient->id]) }}" class="text-xs text-amber-600 hover:underline font-semibold">+ Dispense Item</a>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-200 uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Item Name</th>
                                <th class="px-4 py-3">Batch Number</th>
                                <th class="px-4 py-3">Quantity</th>
                                <th class="px-4 py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($patient->inventoryTransactions as $tx)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-medium text-slate-900">
                                        {{ $tx->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-slate-800">
                                        {{ $tx->item ? $tx->item->name : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 font-mono text-slate-500">
                                        {{ $tx->batch ? $tx->batch->batch_number : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 font-bold text-emerald-700">
                                        {{ $tx->quantity }} {{ $tx->item ? $tx->item->unit_of_measure : '' }}
                                    </td>
                                    <td class="px-4 py-3 text-slate-500">
                                        {{ $tx->remarks ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        No items have been dispensed to this patient yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 5: FULL DEMOGRAPHICS & CONTACTS -->
        <div x-show="activeTab === 'info'" class="bg-white rounded-xl border border-slate-200 shadow-xs p-6" style="display: none;">
            <h2 class="font-bold text-slate-800 text-sm mb-4">Complete Demographic Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Street Address</span>
                    <span class="text-slate-800 font-medium">{{ $patient->street_address ?? 'None provided' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Purok</span>
                    <span class="text-slate-800 font-medium">{{ $patient->purok->name }} (Tacunan)</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Contact Phone Number</span>
                    <span class="text-slate-800 font-medium">{{ $patient->contact_number ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">PhilHealth ID Number</span>
                    <span class="text-slate-800 font-mono font-medium">{{ $patient->philhealth_number ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Emergency Contact Person</span>
                    <span class="text-slate-800 font-medium">{{ $patient->emergency_contact_name ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Emergency Contact Phone</span>
                    <span class="text-slate-800 font-medium">{{ $patient->emergency_contact_number ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Registered By</span>
                    <span class="text-slate-800 font-medium">{{ $patient->creator ? $patient->creator->name : 'System' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Registration Date</span>
                    <span class="text-slate-800 font-medium">{{ $patient->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

