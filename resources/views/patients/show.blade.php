<x-app-layout>
    <div class="space-y-6" x-data="{
        activeTab: '{{ request('tab', 'assessments') }}',
        privacyMask: {{ Auth::user()->isAdmin() ? 'true' : 'false' }},
        viewModalOpen: false,
        editModalOpen: false,
        selectedVitals: null,
        editForm: {
            id: null,
            assessment_date: '',
            weight_kg: '',
            height_cm: '',
            systolic_bp: '',
            diastolic_bp: '',
            pulse_rate: '',
            respiratory_rate: '',
            temperature_celsius: '',
            nutritional_status: '',
            notes: '',
            updateUrl: ''
        },
        openViewModal(item) {
            this.selectedVitals = item;
            this.viewModalOpen = true;
        },
        openEditModal(item) {
            this.selectedVitals = item;
            this.editForm = {
                id: item.id,
                assessment_date: item.assessment_date_raw || '',
                weight_kg: item.weight_kg ?? '',
                height_cm: item.height_cm ?? '',
                systolic_bp: item.systolic_bp ?? '',
                diastolic_bp: item.diastolic_bp ?? '',
                pulse_rate: item.pulse_rate ?? '',
                respiratory_rate: item.respiratory_rate ?? '',
                temperature_celsius: item.temperature_celsius ?? '',
                nutritional_status: item.nutritional_status ?? '',
                notes: item.notes ?? '',
                updateUrl: item.update_url
            };
            this.viewModalOpen = false;
            this.editModalOpen = true;
        }
    }">
        <!-- Breadcrumb & Privacy Controls -->
        <div class="flex items-center justify-between">
            <a href="{{ route('patients.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
                &larr; Back to Patient Directory
            </a>
            <div class="flex items-center gap-2">
                <!-- Privacy Shield Toggle -->
                <button type="button" @click="privacyMask = !privacyMask" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition shadow-2xs" :class="privacyMask ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span x-text="privacyMask ? 'Privacy Shield: Masked' : 'Privacy Shield: Visible'"></span>
                </button>

                @if (! Auth::user()->isAdmin() && ! $patient->trashed())
                    <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition shadow-2xs">
                        Edit Profile
                    </a>
                @endif
                @if (Auth::user()->isAdmin())
                    @if ($patient->trashed())
                        <form method="POST" action="{{ route('patients.restore', $patient) }}" onsubmit="return confirm('Restore this archived patient record back to the active directory?');" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100 transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Restore Patient
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('patients.destroy', $patient) }}" onsubmit="return confirm('Are you sure you want to archive this patient record? Historical consultations and assessments will remain safely preserved.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-rose-600 border border-rose-200 hover:bg-rose-50 transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Archive Patient
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>

        <!-- Archived Patient Notice -->
        @if ($patient->trashed())
            <div class="p-4 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 text-xs shadow-2xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="p-2 bg-amber-200 text-amber-900 rounded-lg shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                    <div>
                        <div class="font-bold text-amber-950 text-sm">Archived Patient Record (Soft Deleted)</div>
                        <p class="text-amber-800 mt-0.5">This patient record is currently archived. Historical vitals, consultations, and transactions are preserved in read-only mode.</p>
                    </div>
                </div>
                @if (Auth::user()->isAdmin())
                    <form method="POST" action="{{ route('patients.restore', $patient) }}" onsubmit="return confirm('Restore this archived patient record back to the active directory?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white transition shadow-2xs shrink-0">
                            Restore Record
                        </button>
                    </form>
                @endif
            </div>
        @endif

        <!-- Administrative Privacy & Medical Confidentiality Notice -->
        @if (Auth::user()->isAdmin())
            <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-xl text-amber-900 text-xs shadow-2xs flex items-start gap-3">
                <div class="p-2 bg-amber-100 text-amber-800 rounded-lg shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div class="space-y-1 flex-1">
                    <div class="font-bold text-amber-950 flex flex-wrap items-center gap-2">
                        <span>Administrative Read-Only View &bull; Medical Confidentiality Enforced</span>
                        <span class="px-2 py-0.5 rounded bg-amber-200/80 text-amber-900 text-[10px] font-bold uppercase tracking-wider">RA 10173 Compliance</span>
                    </div>
                    <p class="text-amber-800 leading-relaxed">
                        In accordance with Republic Act 10173 (Philippine Data Privacy Act of 2012) and health center policy, patient personal demographics, vital sign assessments, and consultation entries are restricted from administrative alteration. Clinical actions are reserved exclusively for licensed and frontline health personnel (Nurses, Midwives, BHWs, and BNS).
                    </p>
                </div>
            </div>
        @else
            <div class="px-4 py-2 bg-emerald-50/80 border border-emerald-200 rounded-lg text-emerald-900 text-xs flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="font-semibold">Authorized Healthcare Personnel Access &bull; Confidential Medical Record</span>
                </div>
            </div>
        @endif

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
                    @if (! $patient->trashed())
                        @if (! Auth::user()->isAdmin())
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
                        @endif
                        <a href="{{ route('inventory.dispense', ['patient_id' => $patient->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Dispense Item
                        </a>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-bold shadow-2xs">
                            <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Archived Record (New entries suspended)
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-slate-200">
            <nav class="flex space-x-6">
                <button @click="activeTab = 'assessments'" :class="activeTab === 'assessments' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Vitals & BNS Assessments ({{ $healthAssessments->total() }})
                </button>
                <button @click="activeTab = 'services'" :class="activeTab === 'services' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Health Services History ({{ $healthServiceRecords->total() }})
                </button>
                <button @click="activeTab = 'dispensed'" :class="activeTab === 'dispensed' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Dispensed Items ({{ $inventoryTransactions->total() }})
                </button>
                <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'" class="py-3 px-1 border-b-2 text-sm transition">
                    Full Demographics & Contacts
                </button>
            </nav>
        </div>

        <!-- TAB 1: VITALS & BNS ASSESSMENTS -->
        <div x-show="activeTab === 'assessments'" class="space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-slate-800 text-base">Recorded Vital Signs & Anthropometrics</h2>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        PHI Confidential
                    </span>
                </div>
                @if (! Auth::user()->isAdmin())
                    <a href="{{ route('assessments.create', $patient) }}" class="text-xs text-emerald-600 hover:underline font-semibold">+ Record New Vitals</a>
                @else
                    <span class="text-xs text-slate-400 italic">Clinical entry restricted to healthcare personnel</span>
                @endif
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
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($healthAssessments as $vitals)
                                @php
                                    $vitalsData = [
                                        'id' => $vitals->id,
                                        'assessment_date' => $vitals->assessment_date->format('M d, Y'),
                                        'assessment_date_raw' => $vitals->assessment_date->format('Y-m-d'),
                                        'weight_kg' => $vitals->weight_kg,
                                        'height_cm' => $vitals->height_cm,
                                        'bmi' => $vitals->bmi,
                                        'blood_pressure' => $vitals->blood_pressure,
                                        'systolic_bp' => $vitals->systolic_bp,
                                        'diastolic_bp' => $vitals->diastolic_bp,
                                        'pulse_rate' => $vitals->pulse_rate,
                                        'respiratory_rate' => $vitals->respiratory_rate,
                                        'temperature_celsius' => $vitals->temperature_celsius,
                                        'nutritional_status' => $vitals->nutritional_status,
                                        'notes' => $vitals->notes,
                                        'staff_name' => $vitals->user ? $vitals->user->name : 'N/A',
                                        'staff_role' => $vitals->user ? ucfirst($vitals->user->role) : '',
                                        'created_at' => $vitals->created_at ? $vitals->created_at->format('M d, Y h:i A') : '',
                                        'updated_at' => $vitals->updated_at && $vitals->updated_at != $vitals->created_at ? $vitals->updated_at->format('M d, Y h:i A') : null,
                                        'update_url' => route('assessments.update', $vitals),
                                    ];
                                @endphp
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
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" @click="openViewModal({{ json_encode($vitalsData) }})" class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition inline-flex items-center gap-1 shadow-2xs">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </button>
                                            @if (! Auth::user()->isAdmin() && ! $patient->trashed())
                                                <button type="button" @click="openEditModal({{ json_encode($vitalsData) }})" class="px-2.5 py-1 text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded-lg transition inline-flex items-center gap-1 shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                        No health assessments or vital signs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($healthAssessments->hasPages())
                    <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Showing {{ $healthAssessments->firstItem() }} to {{ $healthAssessments->lastItem() }} of {{ number_format($healthAssessments->total()) }} assessments
                        </div>
                        {{ $healthAssessments->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 2: HEALTH SERVICES RECORDS (8 PROGRAMS) -->
        <div x-show="activeTab === 'services'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-slate-800 text-base">Health Center Consultations & Programs</h2>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        PHI Confidential
                    </span>
                </div>
                @if (Auth::user()->isClinicalStaff())
                    <a href="{{ route('services.create', ['patient_id' => $patient->id]) }}" class="text-xs text-blue-600 hover:underline font-semibold">+ Add Service Record</a>
                @elseif (Auth::user()->isBhw())
                    <a href="{{ route('services.create', ['patient_id' => $patient->id, 'type' => 'purok_kalusugan']) }}" class="text-xs text-blue-600 hover:underline font-semibold">+ Log Outreach (Purok Kalusugan)</a>
                @else
                    <span class="text-xs text-slate-400 italic">Consultations restricted to healthcare personnel</span>
                @endif
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
                            @forelse ($healthServiceRecords as $service)
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

                @if ($healthServiceRecords->hasPages())
                    <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Showing {{ $healthServiceRecords->firstItem() }} to {{ $healthServiceRecords->lastItem() }} of {{ number_format($healthServiceRecords->total()) }} consultations
                        </div>
                        {{ $healthServiceRecords->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 3: DISPENSED ITEMS -->
        <div x-show="activeTab === 'dispensed'" class="space-y-4" style="display: none;">
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-base">Medicines & Vaccines Dispensed</h2>
                @if (Auth::user()->canDispenseMedicine())
                    <a href="{{ route('inventory.dispense', ['patient_id' => $patient->id]) }}" class="text-xs text-amber-600 hover:underline font-semibold">+ Dispense Item</a>
                @endif
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
                            @forelse ($inventoryTransactions as $tx)
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

                @if ($inventoryTransactions->hasPages())
                    <div class="p-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Showing {{ $inventoryTransactions->firstItem() }} to {{ $inventoryTransactions->lastItem() }} of {{ number_format($inventoryTransactions->total()) }} dispensed records
                        </div>
                        {{ $inventoryTransactions->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- TAB 5: FULL DEMOGRAPHICS & CONTACTS -->
        <div x-show="activeTab === 'info'" class="bg-white rounded-xl border border-slate-200 shadow-xs p-6" style="display: none;">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-slate-800 text-sm">Complete Demographic Details</h2>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                        PII Protected
                    </span>
                </div>
                <button type="button" @click="privacyMask = !privacyMask" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span x-text="privacyMask ? 'Reveal PII' : 'Mask PII'"></span>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Street Address</span>
                    <span class="text-slate-800 font-medium" x-show="!privacyMask">{{ $patient->street_address ?? 'None provided' }}</span>
                    <span class="text-slate-400 font-medium font-mono" x-show="privacyMask" style="display: none;">••••••••••••••••</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Purok</span>
                    <span class="text-slate-800 font-medium">{{ $patient->purok->name }} (Tacunan)</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Contact Phone Number</span>
                    <span class="text-slate-800 font-medium" x-show="!privacyMask">{{ $patient->contact_number ?? 'None' }}</span>
                    <span class="text-slate-500 font-medium font-mono" x-show="privacyMask" style="display: none;">{{ $patient->masked_contact_number ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">PhilHealth ID Number</span>
                    <span class="text-slate-800 font-mono font-medium" x-show="!privacyMask">{{ $patient->philhealth_number ?? 'None' }}</span>
                    <span class="text-slate-500 font-mono font-medium" x-show="privacyMask" style="display: none;">{{ $patient->masked_philhealth_number ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Emergency Contact Person</span>
                    <span class="text-slate-800 font-medium">{{ $patient->emergency_contact_name ?? 'None' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Emergency Contact Phone</span>
                    <span class="text-slate-800 font-medium" x-show="!privacyMask">{{ $patient->emergency_contact_number ?? 'None' }}</span>
                    <span class="text-slate-500 font-medium font-mono" x-show="privacyMask" style="display: none;">{{ $patient->masked_emergency_contact_number ?? 'None' }}</span>
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

        <!-- MODAL 1: VIEW FULL VITALS & ASSESSMENT DETAILS -->
        <div x-show="viewModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;"
             @keydown.escape.window="viewModalOpen = false">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="viewModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all text-slate-800"
                     @click.away="viewModalOpen = false">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Vital Signs & BNS Assessment Details</h3>
                                <p class="text-xs text-slate-500">
                                    Patient: <span class="font-semibold text-slate-700">{{ $patient->full_name }}</span> ({{ $patient->patient_control_number }})
                                </p>
                            </div>
                        </div>
                        <button type="button" @click="viewModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto text-sm">
                        <!-- Date & Staff Info Banner -->
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Assessment Date</span>
                                <span class="font-semibold text-slate-800 text-sm" x-text="selectedVitals?.assessment_date || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Recorded By</span>
                                <span class="font-semibold text-slate-800 text-sm" x-text="selectedVitals?.staff_name || 'Healthcare Staff'"></span>
                                <template x-if="selectedVitals?.staff_role">
                                    <span class="text-[11px] text-slate-500 block" x-text="'(' + selectedVitals.staff_role + ')'"></span>
                                </template>
                            </div>
                            <div>
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Timestamp</span>
                                <span class="text-xs text-slate-600" x-text="selectedVitals?.created_at || 'N/A'"></span>
                                <template x-if="selectedVitals?.updated_at">
                                    <span class="text-[10px] text-amber-700 block mt-0.5" x-text="'Edited: ' + selectedVitals.updated_at"></span>
                                </template>
                            </div>
                        </div>

                        <!-- Anthropometric Panel (BNS Growth Monitoring) -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Anthropometric Measurements (Growth Monitoring)
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Weight</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.weight_kg ? selectedVitals.weight_kg + ' kg' : 'Not recorded'"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Height</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.height_cm ? selectedVitals.height_cm + ' cm' : 'Not recorded'"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">BMI</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.bmi ? selectedVitals.bmi + ' kg/m²' : 'N/A'"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Nutritional Status</div>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block" 
                                              x-text="selectedVitals?.nutritional_status || 'Unspecified'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Clinical Vital Signs Panel -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Clinical Vital Signs
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Blood Pressure</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.blood_pressure || (selectedVitals?.systolic_bp && selectedVitals?.diastolic_bp ? selectedVitals.systolic_bp + '/' + selectedVitals.diastolic_bp + ' mmHg' : 'Not recorded')"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Pulse / Heart Rate</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.pulse_rate ? selectedVitals.pulse_rate + ' bpm' : 'Not recorded'"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Respiratory Rate</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.respiratory_rate ? selectedVitals.respiratory_rate + ' breaths/min' : 'Not recorded'"></div>
                                </div>
                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                    <div class="text-[11px] text-slate-500 font-medium">Body Temperature</div>
                                    <div class="text-base font-bold text-slate-900 mt-0.5" x-text="selectedVitals?.temperature_celsius ? selectedVitals.temperature_celsius + ' °C' : 'Not recorded'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Observation Notes & Remarks Panel -->
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Observations & Clinical Notes</h4>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                                <p class="text-xs text-slate-700 whitespace-pre-wrap leading-relaxed" x-text="selectedVitals?.notes || 'No observation notes or remarks entered for this assessment.'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <button type="button" @click="viewModalOpen = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold transition">
                            Close
                        </button>
                        @if (! Auth::user()->isAdmin() && ! $patient->trashed())
                            <button type="button" @click="openEditModal(selectedVitals)" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Record
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL 2: EDIT VITALS & ASSESSMENT -->
        @if (! Auth::user()->isAdmin() && ! $patient->trashed())
            <div x-show="editModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;"
                 @keydown.escape.window="editModalOpen = false">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all text-slate-800"
                         @click.away="editModalOpen = false">
                        
                        <form method="POST" :action="editForm.updateUrl" autocomplete="off" class="space-y-0">
                            @csrf
                            @method('PUT')

                            <!-- Modal Header -->
                            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-slate-900">Edit Vital Signs & Assessment</h3>
                                        <p class="text-xs text-slate-500">
                                            Updating record for <span class="font-semibold text-slate-700">{{ $patient->full_name }}</span> ({{ $patient->patient_control_number }})
                                        </p>
                                    </div>
                                </div>
                                <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1">&times;</button>
                            </div>

                            <!-- Modal Form Fields -->
                            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto text-xs">
                                <!-- Assessment Date -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Assessment Date <span class="text-rose-500">*</span></label>
                                    <input type="date" name="assessment_date" x-model="editForm.assessment_date" max="{{ date('Y-m-d') }}" required autocomplete="off" class="w-full sm:w-64 text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                </div>

                                <hr class="border-slate-100">

                                <!-- Anthropometrics (Growth Monitoring) -->
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Anthropometric Measurements (BNS)</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Weight (kg)</label>
                                            <input type="number" step="0.1" min="0.5" max="300" name="weight_kg" x-model="editForm.weight_kg" placeholder="e.g. 55.4" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Height (cm)</label>
                                            <input type="number" step="0.1" min="30" max="250" name="height_cm" x-model="editForm.height_cm" placeholder="e.g. 162.5" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-slate-100">

                                <!-- Clinical Vitals -->
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Vital Signs</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Pressure - Systolic (mmHg)</label>
                                            <input type="number" min="50" max="260" name="systolic_bp" x-model="editForm.systolic_bp" placeholder="e.g. 120" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Pressure - Diastolic (mmHg)</label>
                                            <input type="number" min="30" max="160" name="diastolic_bp" x-model="editForm.diastolic_bp" placeholder="e.g. 80" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pulse / Heart Rate (bpm)</label>
                                            <input type="number" min="30" max="220" name="pulse_rate" x-model="editForm.pulse_rate" placeholder="e.g. 75" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Respiratory Rate (breaths/min)</label>
                                            <input type="number" min="10" max="80" name="respiratory_rate" x-model="editForm.respiratory_rate" placeholder="e.g. 18" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Body Temperature (°C)</label>
                                            <input type="number" step="0.1" min="30" max="45" name="temperature_celsius" x-model="editForm.temperature_celsius" placeholder="e.g. 36.6" autocomplete="off" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nutritional Status Assessment</label>
                                            <select name="nutritional_status" x-model="editForm.nutritional_status" class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                <option value="">Auto-assign based on BMI</option>
                                                <option value="Normal">Normal</option>
                                                <option value="Underweight">Underweight</option>
                                                <option value="Overweight">Overweight</option>
                                                <option value="Obese">Obese</option>
                                                <option value="Wasted">Wasted (Acute Malnutrition)</option>
                                                <option value="Stunted">Stunted (Chronic Malnutrition)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-slate-100">

                                <!-- Remarks -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Observations / Remarks</label>
                                    <textarea name="notes" x-model="editForm.notes" rows="3" placeholder="Enter clinical observations or nutritional notes..." class="w-full text-xs rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-3">
                                <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg text-xs font-semibold transition">
                                    Cancel
                                </button>
                                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

