<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6" x-data="{ programType: '{{ old('service_type', $selectedType) }}' }">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ $patient ? route('patients.show', $patient) : route('services.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                        <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Clinical PHI Consultation
                    </span>
                    <span class="text-[11px] text-slate-400">RA 10173 Protected</span>
                </div>
                <h1 class="text-xl font-bold text-slate-900">Record Health Service Consultation</h1>
                <p class="text-xs text-slate-500 mt-1">Log care, intake, and interventions for one of Barangay Tacunan's 8 authorized health programs.</p>
            </div>

            <form method="POST" action="{{ route('services.store') }}" class="p-6 space-y-6">
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
                        <input type="date" name="service_date" value="{{ old('service_date', $today) }}" max="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Program-Specific Form Fields -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Program-Specific Details</h2>

                    <!-- 1. Family Planning -->
                    <fieldset :disabled="programType !== 'family_planning'" x-show="programType === 'family_planning'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Method Provided</label>
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
                    </fieldset>

                    <!-- 2. Prenatal Care -->
                    <fieldset :disabled="programType !== 'prenatal_care'" x-show="programType === 'prenatal_care'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Last Menstrual Period (LMP)</label>
                            <input type="date" name="service_specific_data[lmp]" value="{{ old('service_specific_data.lmp') }}" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Expected Date of Confinement (EDC)</label>
                            <input type="date" name="service_specific_data[edc]" value="{{ old('service_specific_data.edc') }}" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Trimester / Visit #</label>
                            <select name="service_specific_data[trimester]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="">-- Select Trimester --</option>
                                @foreach(['1st Trimester (Visit 1)', '2nd Trimester (Visit 2)', '3rd Trimester (Visit 3)', '3rd Trimester (Visit 4+)'] as $tri)
                                    <option value="{{ $tri }}" {{ old('service_specific_data.trimester') === $tri ? 'selected' : '' }}>{{ $tri }}</option>
                                @endforeach
                            </select>
                        </div>
                    </fieldset>

                    <!-- 3. Immunization -->
                    <fieldset :disabled="programType !== 'immunization'" x-show="programType === 'immunization'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
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
                                @foreach(['Dose 1', 'Dose 2', 'Dose 3', 'Booster'] as $dose)
                                    <option value="{{ $dose }}" {{ old('service_specific_data.dose_sequence') === $dose ? 'selected' : '' }}>{{ $dose }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Administration Site</label>
                            <input type="text" name="service_specific_data[admin_site]" value="{{ old('service_specific_data.admin_site') }}" placeholder="e.g. Left Deltoid, Right Thigh" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </fieldset>

                    <!-- 4. CVD Screening -->
                    <fieldset :disabled="programType !== 'cvd_screening'" x-show="programType === 'cvd_screening'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Target Organ Damage Screen</label>
                            <input type="text" name="service_specific_data[organ_damage]" value="{{ old('service_specific_data.organ_damage') }}" placeholder="e.g. None reported, hypertensive retinopathy" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </fieldset>

                    <!-- 5. PhilPEN -->
                    <fieldset :disabled="programType !== 'philpen'" x-show="programType === 'philpen'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Fasting Blood Sugar (mg/dL)</label>
                            <input type="number" step="0.1" name="service_specific_data[fbs]" value="{{ old('service_specific_data.fbs') }}" placeholder="e.g. 95" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </fieldset>

                    <!-- 6. NTP -->
                    <fieldset :disabled="programType !== 'ntp'" x-show="programType === 'ntp'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
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
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sputum / GeneXpert Status</label>
                            <input type="text" name="service_specific_data[tb_testing]" value="{{ old('service_specific_data.tb_testing') }}" placeholder="e.g. Specimen collected, referred for X-ray" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </fieldset>

                    <!-- 7. Purok Kalusugan -->
                    <fieldset :disabled="programType !== 'purok_kalusugan'" x-show="programType === 'purok_kalusugan'" class="p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Purok Activity / Household Outreach Notes</label>
                        <input type="text" name="service_specific_data[outreach_activity]" value="{{ old('service_specific_data.outreach_activity') }}" placeholder="e.g. Purok household check, environmental sanitation visit" class="w-full text-sm rounded-lg border-slate-300">
                    </fieldset>

                    <!-- 8. BNS Program -->
                    <fieldset :disabled="programType !== 'bns_program'" x-show="programType === 'bns_program'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">BNS Intervention</label>
                            <select name="service_specific_data[bns_intervention]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="">-- Select Intervention --</option>
                                @foreach(['Supplementary Feeding Intake', 'Micronutrient Supplementation (Vit A / Iron)', 'Deworming Tablet Administered', 'Infant and Young Child Feeding Counseling'] as $bns)
                                    <option value="{{ $bns }}" {{ old('service_specific_data.bns_intervention') === $bns ? 'selected' : '' }}>{{ $bns }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nutritional Category</label>
                            <input type="text" name="service_specific_data[bns_category]" value="{{ old('service_specific_data.bns_category') }}" placeholder="e.g. Target for 120-day feeding" class="w-full text-sm rounded-lg border-slate-300">
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
                        <span class="text-[11px] text-slate-400 block mt-1">Stores an advisory return note on the patient's record. Official clinic appointments are managed in the <a href="{{ route('appointments.index') }}" class="text-emerald-600 underline font-medium">Appointments</a> module.</span>
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

