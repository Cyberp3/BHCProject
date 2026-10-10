<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6" x-data="{
        programType: '{{ old('service_type', $selectedType) }}',
        serviceDate: '{{ old('service_date', $today) }}',
        scheduleInfo: {
            'ntp': {
                days: [1, 2],
                dayNames: 'Monday & Tuesday (Morning)',
                hours: '8:00 AM - 12:00 PM',
                badgeColor: 'amber'
            },
            'family_planning': {
                days: [1, 2],
                dayNames: 'Monday & Tuesday (Afternoon)',
                hours: '1:00 PM - 5:00 PM',
                badgeColor: 'purple'
            },
            'prenatal_care': {
                days: [2],
                dayNames: 'Tuesday',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'pink'
            },
            'immunization': {
                days: [3],
                dayNames: 'Wednesday (Bakuna Day)',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'emerald'
            },
            'purok_kalusugan': {
                days: [4],
                dayNames: 'Thursday (Community Outreach)',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'teal'
            },
            'bns_program': {
                days: [4],
                dayNames: 'Thursday (Growth Monitoring)',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'teal'
            },
            'cvd_screening': {
                days: [5],
                dayNames: 'Friday (Hypertension / Diabetes)',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'rose'
            },
            'philpen': {
                days: [5],
                dayNames: 'Friday (PhilPEN Assessment)',
                hours: '8:00 AM - 5:00 PM',
                badgeColor: 'rose'
            }
        },
        getCurrentSchedule() {
            return this.scheduleInfo[this.programType] || null;
        },
        isDateMatchingSchedule() {
            if (!this.serviceDate) return true;
            let schedule = this.getCurrentSchedule();
            if (!schedule) return true;
            let parts = this.serviceDate.split('-');
            if (parts.length !== 3) return true;
            let dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
            return schedule.days.includes(dateObj.getDay());
        },
        getSelectedDayName() {
            if (!this.serviceDate) return '';
            let parts = this.serviceDate.split('-');
            if (parts.length !== 3) return '';
            let dateObj = new Date(parts[0], parts[1] - 1, parts[2]);
            let days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            return days[dateObj.getDay()];
        }
    }">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ $patient ? route('patients.show', $patient) : route('services.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-1">
                    @if (Auth::user()->isBhw())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            BHW Community Outreach
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                            <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Clinical PHI Consultation
                        </span>
                    @endif
                </div>
                <h1 class="text-xl font-bold text-slate-900">
                    {{ Auth::user()->isBhw() ? 'Record Community Outreach Activity' : 'Record Health Service Consultation' }}
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    {{ Auth::user()->isBhw() ? 'Log community visits and health promotion activities under Purok Kalusugan.' : "Log care, intake, and interventions for one of Barangay Tacunan's authorized health programs." }}
                </p>
            </div>

            <form method="POST" action="{{ route('services.store') }}" class="p-6 space-y-6" autocomplete="off">
                @csrf

                <!-- Patient Selection -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Patient & Program</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Patient <span class="text-rose-500">*</span></label>
                            @if ($patient)
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-900 font-semibold flex items-center justify-between">
                                    <span>{{ $patient->full_name }} ({{ $patient->patient_control_number }})</span>
                                    <span class="text-xs font-normal text-emerald-700">{{ $patient->purok->name }}</span>
                                </div>
                            @else
                                <select name="patient_id" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">-- Select Patient --</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->last_name }}, {{ $p->first_name }} ({{ $p->patient_control_number }}) - {{ $p->purok->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Health Center Program <span class="text-rose-500">*</span></label>
                            <select name="service_type" x-model="programType" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                                @foreach ($services as $key => $title)
                                    <option value="{{ $key }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Service / Consultation Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="service_date" x-model="serviceDate" max="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <!-- Helpful Clinic Schedule Reference -->
                    <div class="flex flex-col justify-end">
                        <template x-if="getCurrentSchedule()">
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs space-y-1">
                                <div class="flex items-center gap-1.5 font-semibold text-slate-700">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Regular Program Day: <span class="text-slate-900" x-text="getCurrentSchedule()?.dayNames"></span></span>
                                </div>
                                <div class="text-[11px] text-slate-500" x-text="'Hours: ' + getCurrentSchedule()?.hours"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Advisory notice based on selected consultation date -->
                <template x-if="getCurrentSchedule() && serviceDate">
                    <div>
                        <!-- Matching date (green positive feedback) -->
                        <div x-show="isDateMatchingSchedule()" class="px-3.5 py-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>
                                Consultation date (<strong x-text="getSelectedDayName()"></strong>) aligns with the official clinic day for this health program.
                            </span>
                        </div>

                        <!-- Off-schedule date (gentle amber advisory notice) -->
                        <div x-show="!isDateMatchingSchedule()" class="px-3.5 py-2.5 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900 flex items-start gap-2 shadow-2xs">
                            <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="font-bold">Schedule Advisory:</span>
                                <span>The selected date is a <strong x-text="getSelectedDayName()"></strong>. This program is scheduled on <strong x-text="getCurrentSchedule()?.dayNames"></strong>.</span>
                                <span class="text-amber-700 block text-[11px] mt-0.5">
                                    Walk-ins, catch-up sessions, and back-encoded visits are permitted and will still be saved.
                                </span>
                            </div>
                        </div>
                    </div>
                </template>

                <hr class="border-slate-100">

                <!-- Program-Specific Form Fields -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Program-Specific Parameters</h2>
                        <span class="text-[11px] text-slate-400">Clinical fields dynamically adapt based on selected program</span>
                    </div>

                    <!-- 1. Family Planning -->
                    <fieldset :disabled="programType !== 'family_planning'" x-show="programType === 'family_planning'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Method Provided / Accepted</label>
                                <select name="service_specific_data[method]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Method --</option>
                                    @foreach(['Pills (COC/POP)' => 'Pills (COC / POP)', 'Injectable (DMPA)' => 'Injectable (DMPA)', 'Condoms' => 'Condoms', 'IUD' => 'IUD (Intrauterine Device)', 'Implant' => 'Subdermal Implant', 'NFP-BOM' => 'Natural FP (BOM / STM / LAM)'] as $mVal => $mLbl)
                                        <option value="{{ $mVal }}" {{ old('service_specific_data.method') === $mVal ? 'selected' : '' }}>{{ $mLbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Client Type</label>
                                <select name="service_specific_data[client_type]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Client Type --</option>
                                    @foreach(['New Acceptor', 'Current User', 'Changing Method', 'Dropout Restart'] as $cType)
                                        <option value="{{ $cType }}" {{ old('service_specific_data.client_type') === $cType ? 'selected' : '' }}>{{ $cType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Source of Supplies / Service</label>
                                <select name="service_specific_data[source]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Source --</option>
                                    @foreach(['Barangay Health Center', 'City Health Office (CHO)', 'District Hospital', 'Private Clinic / Hospital', 'NGO / Other Facility'] as $src)
                                        <option value="{{ $src }}" {{ old('service_specific_data.source') === $src ? 'selected' : '' }}>{{ $src }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Drop-out Reason <span class="text-slate-400 font-normal">(If applicable)</span></label>
                                <select name="service_specific_data[drop_out_reason]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">None / Active User</option>
                                    @foreach(['Side effects', 'Desires pregnancy', 'Medical complication', 'Relocated out of catchment', 'Spouse disapproval', 'Underwent permanent sterilization', 'Other reason'] as $reason)
                                        <option value="{{ $reason }}" {{ old('service_specific_data.drop_out_reason') === $reason ? 'selected' : '' }}>{{ $reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <!-- 2. Prenatal Care -->
                    <fieldset :disabled="programType !== 'prenatal_care'" x-show="programType === 'prenatal_care'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Gravida (Total Pregnancies)</label>
                                <input type="number" min="1" max="25" name="service_specific_data[gravida]" value="{{ old('service_specific_data.gravida') }}" placeholder="e.g. 1" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Para (Viable Births)</label>
                                <input type="number" min="0" max="25" name="service_specific_data[para]" value="{{ old('service_specific_data.para') }}" placeholder="e.g. 0" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Fundic Height (cm)</label>
                                <input type="number" step="0.5" min="0" max="50" name="service_specific_data[fundic_height_cm]" value="{{ old('service_specific_data.fundic_height_cm') }}" placeholder="e.g. 23" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Fetal Heart Tone (FHT)</label>
                                <input type="text" name="service_specific_data[fetal_heart_tone]" value="{{ old('service_specific_data.fetal_heart_tone') }}" placeholder="e.g. 142 bpm" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Last Menstrual Period (LMP)</label>
                                <input type="date" name="service_specific_data[lmp]" value="{{ old('service_specific_data.lmp') }}" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Expected Confinement (EDC)</label>
                                <input type="date" name="service_specific_data[edc]" value="{{ old('service_specific_data.edc') }}" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Trimester / Visit</label>
                                <select name="service_specific_data[trimester]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Trimester --</option>
                                    @foreach(['1st Trimester (Visit 1)', '2nd Trimester (Visit 2)', '3rd Trimester (Visit 3)', '3rd Trimester (Visit 4+)'] as $tri)
                                        <option value="{{ $tri }}" {{ old('service_specific_data.trimester') === $tri ? 'selected' : '' }}>{{ $tri }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tetanus Toxoid / Td Immunization Status</label>
                            <select name="service_specific_data[tetanus_toxoid_status]" class="w-full sm:w-1/2 text-sm rounded-lg border-slate-300">
                                <option value="">-- Select Td Status --</option>
                                @foreach(['Td 1 Given', 'Td 2 Given', 'Td 3 Given', 'Td 4 Given', 'Td 5 Given', 'Fully Immunized Mother', 'Not due / Not given this visit'] as $tt)
                                    <option value="{{ $tt }}" {{ old('service_specific_data.tetanus_toxoid_status') === $tt ? 'selected' : '' }}>{{ $tt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>

                    <!-- 3. Immunization -->
                    <fieldset :disabled="programType !== 'immunization'" x-show="programType === 'immunization'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Vaccine Administered</label>
                                <select name="service_specific_data[vaccine_name]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Vaccine --</option>
                                    @foreach(['BCG', 'Hepatitis B', 'Pentavalent (DTP-HepB-Hib)', 'OPV (Oral Polio)', 'IPV (Inactivated Polio)', 'PCV (Pneumococcal)', 'MMR (Measles-Mumps-Rubella)', 'Td (Tetanus Diphtheria)'] as $vax)
                                        <option value="{{ $vax }}" {{ old('service_specific_data.vaccine_name') === $vax ? 'selected' : '' }}>{{ $vax }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Dose Sequence</label>
                                <select name="service_specific_data[dose_sequence]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Dose --</option>
                                    @foreach(['Dose 1', 'Dose 2', 'Dose 3', 'Booster 1', 'Booster 2'] as $dose)
                                        <option value="{{ $dose }}" {{ old('service_specific_data.dose_sequence') === $dose ? 'selected' : '' }}>{{ $dose }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Administration Site / Route</label>
                                <input type="text" name="service_specific_data[admin_site]" value="{{ old('service_specific_data.admin_site') }}" placeholder="e.g. Left Deltoid, Right Thigh, Oral" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Batch / Lot Number</label>
                                <input type="text" name="service_specific_data[batch_lot_number]" value="{{ old('service_specific_data.batch_lot_number') }}" placeholder="e.g. LOT-2026-PENT-001" class="w-full text-sm rounded-lg border-slate-300 font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Adverse Events Following Immunization (AEFI)</label>
                                <input type="text" name="service_specific_data[adverse_events]" value="{{ old('service_specific_data.adverse_events') }}" placeholder="e.g. None observed in 15 min monitoring, mild fever" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 4. CVD Screening -->
                    <fieldset :disabled="programType !== 'cvd_screening'" x-show="programType === 'cvd_screening'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Systolic BP (mmHg)</label>
                                <input type="number" min="50" max="300" name="service_specific_data[systolic_bp]" value="{{ old('service_specific_data.systolic_bp') }}" placeholder="e.g. 120" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Diastolic BP (mmHg)</label>
                                <input type="number" min="30" max="200" name="service_specific_data[diastolic_bp]" value="{{ old('service_specific_data.diastolic_bp') }}" placeholder="e.g. 80" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Known Hypertension</label>
                                <select name="service_specific_data[has_hypertension_history]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="no" {{ old('service_specific_data.has_hypertension_history') === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="yes" {{ old('service_specific_data.has_hypertension_history') === 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Known Diabetes</label>
                                <select name="service_specific_data[has_diabetes_history]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="no" {{ old('service_specific_data.has_diabetes_history') === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="yes" {{ old('service_specific_data.has_diabetes_history') === 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Cardiovascular Risk Category</label>
                                <select name="service_specific_data[cvd_risk]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Risk Category --</option>
                                    @foreach(['Low (<10%)' => 'Low (< 10%)', 'Moderate (10% to <20%)' => 'Moderate (10% to < 20%)', 'High (>=20%)' => 'High (>= 20%)'] as $cVal => $cLbl)
                                        <option value="{{ $cVal }}" {{ old('service_specific_data.cvd_risk') === $cVal ? 'selected' : '' }}>{{ $cLbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Smoking Status</label>
                                <select name="service_specific_data[smoker_status]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Smoking Status --</option>
                                    @foreach(['non_smoker' => 'Non-smoker', 'current_smoker' => 'Current Smoker', 'former_smoker' => 'Former Smoker'] as $sVal => $sLbl)
                                        <option value="{{ $sVal }}" {{ old('service_specific_data.smoker_status') === $sVal ? 'selected' : '' }}>{{ $sLbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Organ Damage Screen</label>
                                <input type="text" name="service_specific_data[organ_damage]" value="{{ old('service_specific_data.organ_damage') }}" placeholder="e.g. None reported, hypertensive retinopathy" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 5. PhilPEN -->
                    <fieldset :disabled="programType !== 'philpen'" x-show="programType === 'philpen'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Tobacco Exposure</label>
                                <select name="service_specific_data[tobacco_use]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Status --</option>
                                    @foreach(['Non-smoker', 'Current smoker', 'Ex-smoker'] as $tobacco)
                                        <option value="{{ $tobacco }}" {{ old('service_specific_data.tobacco_use') === $tobacco ? 'selected' : '' }}>{{ $tobacco }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Alcohol Consumption</label>
                                <select name="service_specific_data[alcohol_use]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Status --</option>
                                    @foreach(['None', 'Occasional drinker', 'Binge / Heavy drinker'] as $alc)
                                        <option value="{{ $alc }}" {{ old('service_specific_data.alcohol_use') === $alc ? 'selected' : '' }}>{{ $alc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">PhilPEN Risk Stratification</label>
                                <select name="service_specific_data[risk_stratification]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Risk Level --</option>
                                    @foreach(['Low (<10%)', 'Moderate (10% to <20%)', 'High (>=20%)'] as $risk)
                                        <option value="{{ $risk }}" {{ old('service_specific_data.risk_stratification') === $risk ? 'selected' : '' }}>{{ $risk }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Fasting Blood Sugar (FBS in mg/dL)</label>
                                <input type="number" step="0.1" name="service_specific_data[fbs]" value="{{ old('service_specific_data.fbs') }}" placeholder="e.g. 95" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Total Cholesterol (mg/dL)</label>
                                <input type="text" name="service_specific_data[total_cholesterol]" value="{{ old('service_specific_data.total_cholesterol') }}" placeholder="e.g. 190 mg/dL" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 6. NTP (National Tuberculosis Program) -->
                    <fieldset :disabled="programType !== 'ntp'" x-show="programType === 'ntp'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Presumptive TB Status</label>
                                <select name="service_specific_data[tb_presumptive]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="No" {{ old('service_specific_data.tb_presumptive') === 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Yes" {{ old('service_specific_data.tb_presumptive') === 'Yes' ? 'selected' : '' }}>Yes (Presumptive TB)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Cough Duration</label>
                                <select name="service_specific_data[cough_duration]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Cough Duration --</option>
                                    @foreach(['No cough', 'Less than 2 weeks', '2 weeks or more (Presumptive TB)'] as $cough)
                                        <option value="{{ $cough }}" {{ old('service_specific_data.cough_duration') === $cough ? 'selected' : '' }}>{{ $cough }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Sputum / GeneXpert Result</label>
                                <select name="service_specific_data[sputum_genexpert_result]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Result --</option>
                                    @foreach(['Not done / Pending', 'MTB Not Detected (Negative)', 'MTB Detected - Rifampicin Sensitive', 'MTB Detected - Rifampicin Resistant'] as $xpert)
                                        <option value="{{ $xpert }}" {{ old('service_specific_data.sputum_genexpert_result') === $xpert ? 'selected' : '' }}>{{ $xpert }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Chest X-Ray Findings</label>
                                <input type="text" name="service_specific_data[chest_xray_findings]" value="{{ old('service_specific_data.chest_xray_findings') }}" placeholder="e.g. Clear lung fields, apical infiltrates" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Treatment Regimen</label>
                                <select name="service_specific_data[treatment_regimen]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">None / Not Applicable</option>
                                    @foreach(['Category I (New TB Case)', 'Category II (Retreatment)', 'Symptomatic / Antibiotics for Bronchitis', 'Preventive Therapy (TPT)'] as $reg)
                                        <option value="{{ $reg }}" {{ old('service_specific_data.treatment_regimen') === $reg ? 'selected' : '' }}>{{ $reg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Diagnostic / Testing Notes</label>
                                <input type="text" name="service_specific_data[tb_testing]" value="{{ old('service_specific_data.tb_testing') }}" placeholder="e.g. Referred to CHO for GeneXpert" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 7. Purok Kalusugan (Community Outreach) -->
                    <fieldset :disabled="programType !== 'purok_kalusugan'" x-show="programType === 'purok_kalusugan'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Activity Type</label>
                                <select name="service_specific_data[activity_type]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Activity --</option>
                                    @foreach(['Household Health Survey & Sanitation Visit', 'Dengue 4S Search and Destroy Inspection', 'Child and Maternal Care Follow-up', 'Communicable Disease Contact Tracing', 'Senior Citizen & PWD Wellness Check', 'Community Health Assembly'] as $act)
                                        <option value="{{ $act }}" {{ old('service_specific_data.activity_type') === $act ? 'selected' : '' }}>{{ $act }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Household Members Screened</label>
                                <input type="number" min="1" max="50" name="service_specific_data[household_members_screened]" value="{{ old('service_specific_data.household_members_screened') }}" placeholder="e.g. 4" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Sanitation Status</label>
                                <select name="service_specific_data[sanitation_status]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Status --</option>
                                    @foreach(['Satisfactory (Clean water & sanitary toilet)', 'Needs Improvement (Uncovered water / breeding site)', 'For Follow-up Re-inspection'] as $san)
                                        <option value="{{ $san }}" {{ old('service_specific_data.sanitation_status') === $san ? 'selected' : '' }}>{{ $san }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Health Education Topic</label>
                                <input type="text" name="service_specific_data[health_education_topic]" value="{{ old('service_specific_data.health_education_topic') }}" placeholder="e.g. Dengue 4S Strategy & Hand Hygiene" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Purok Outreach Observations & Notes</label>
                                <input type="text" name="service_specific_data[outreach_activity]" value="{{ old('service_specific_data.outreach_activity') }}" placeholder="e.g. Purok household check, environmental inspection notes" class="w-full text-sm rounded-lg border-slate-300">
                            </div>
                        </div>
                    </fieldset>

                    <!-- 8. BNS Program (Barangay Nutrition Scholar) -->
                    <fieldset :disabled="programType !== 'bns_program'" x-show="programType === 'bns_program'" class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-4" style="display: none;">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Target Group</label>
                                <select name="service_specific_data[target_group]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Target Group --</option>
                                    @foreach(['Infant (0-11 months)', 'Preschool Child (12-59 months)', 'Pregnant Woman', 'Lactating Mother'] as $grp)
                                        <option value="{{ $grp }}" {{ old('service_specific_data.target_group') === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">BNS Intervention</label>
                                <select name="service_specific_data[bns_intervention]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select Intervention --</option>
                                    @foreach(['Operation Timbang (OPT) Plus Growth Monitoring', 'Supplementary Feeding Intake', 'Micronutrient Supplementation (Vit A / Iron)', 'Deworming Tablet Administered', 'Infant and Young Child Feeding (IYCF) Counseling'] as $bns)
                                        <option value="{{ $bns }}" {{ old('service_specific_data.bns_intervention') === $bns ? 'selected' : '' }}>{{ $bns }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Weight for Age</label>
                                <select name="service_specific_data[weight_for_age]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select --</option>
                                    @foreach(['Normal', 'Underweight', 'Severely Underweight', 'Overweight'] as $wfa)
                                        <option value="{{ $wfa }}" {{ old('service_specific_data.weight_for_age') === $wfa ? 'selected' : '' }}>{{ $wfa }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Height for Age</label>
                                <select name="service_specific_data[height_for_age]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select --</option>
                                    @foreach(['Normal', 'Stunted', 'Severely Stunted', 'Tall'] as $hfa)
                                        <option value="{{ $hfa }}" {{ old('service_specific_data.height_for_age') === $hfa ? 'selected' : '' }}>{{ $hfa }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Vitamin A Given</label>
                                <select name="service_specific_data[vitamin_a_given]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select --</option>
                                    @foreach(['Yes (100,000 IU)', 'Yes (200,000 IU)', 'No', 'Given previously / Not due'] as $vitA)
                                        <option value="{{ $vitA }}" {{ old('service_specific_data.vitamin_a_given') === $vitA ? 'selected' : '' }}>{{ $vitA }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Deworming Given</label>
                                <select name="service_specific_data[deworming_given]" class="w-full text-sm rounded-lg border-slate-300">
                                    <option value="">-- Select --</option>
                                    @foreach(['Yes (Albendazole 400mg)', 'No', 'Underage / Not eligible'] as $deworm)
                                        <option value="{{ $deworm }}" {{ old('service_specific_data.deworming_given') === $deworm ? 'selected' : '' }}>{{ $deworm }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nutritional Classification Notes / Target Category</label>
                            <input type="text" name="service_specific_data[bns_category]" value="{{ old('service_specific_data.bns_category') }}" placeholder="e.g. Target for 120-day supplementary feeding, healthy weight" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </fieldset>
                </div>

                <hr class="border-slate-100">

                <!-- Notes and Complaints -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Chief Complaint / Reason for Visit</label>
                        <input type="text" name="complaint_or_reason" value="{{ old('complaint_or_reason') }}" placeholder="e.g. Routine checkup, scheduled immunization, persistent cough" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Findings, Care Delivered & Clinical Notes</label>
                        <textarea name="findings_and_notes" rows="4" placeholder="Detail the assessment findings, health education provided, and actions taken..." class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('findings_and_notes') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Recommended Next Follow-up Date <span class="text-slate-400 font-normal">(Optional clinical advisory note)</span></label>
                        <input type="date" name="next_follow_up_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('next_follow_up_date') }}" class="w-full sm:w-64 text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <template x-if="getCurrentSchedule()">
                            <span class="text-[11px] text-slate-500 block mt-1">
                                Tip: For regular program follow-ups, consider scheduling on a <strong class="text-slate-700" x-text="getCurrentSchedule()?.dayNames"></strong>.
                            </span>
                        </template>
                        <span class="text-[11px] text-slate-400 block mt-1">Stores an advisory return date on the patient's consultation history for clinical continuity.</span>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ $patient ? route('patients.show', $patient) : route('services.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Save Service Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

