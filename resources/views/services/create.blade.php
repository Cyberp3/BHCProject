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
                    <div x-show="programType === 'family_planning'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Method Provided</label>
                            <select name="service_specific_data[method]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="">-- Select Method --</option>
                                <option value="Pills (COC/POP)">Pills (COC / POP)</option>
                                <option value="Injectable (DMPA)">Injectable (DMPA)</option>
                                <option value="Condoms">Condoms</option>
                                <option value="IUD">IUD (Intrauterine Device)</option>
                                <option value="Implant">Subdermal Implant</option>
                                <option value="NFP-BOM">Natural FP (BOM / STM / LAM)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Client Type</label>
                            <select name="service_specific_data[client_type]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="New Acceptor">New Acceptor</option>
                                <option value="Current User">Current User (Resupply)</option>
                                <option value="Changing Method">Changing Method</option>
                                <option value="Dropout Restart">Dropout Restart</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. Prenatal Care -->
                    <div x-show="programType === 'prenatal_care'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Last Menstrual Period (LMP)</label>
                            <input type="date" name="service_specific_data[lmp]" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Expected Date of Confinement (EDC)</label>
                            <input type="date" name="service_specific_data[edc]" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Trimester / Visit #</label>
                            <select name="service_specific_data[trimester]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="1st Trimester (Visit 1)">1st Trimester (Visit 1)</option>
                                <option value="2nd Trimester (Visit 2)">2nd Trimester (Visit 2)</option>
                                <option value="3rd Trimester (Visit 3)">3rd Trimester (Visit 3)</option>
                                <option value="3rd Trimester (Visit 4+)">3rd Trimester (Visit 4+)</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. Immunization -->
                    <div x-show="programType === 'immunization'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Vaccine Administered</label>
                            <select name="service_specific_data[vaccine_name]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="BCG">BCG</option>
                                <option value="Hepatitis B">Hepatitis B (Birth dose)</option>
                                <option value="Pentavalent (DTP-HepB-Hib)">Pentavalent (DTP-HepB-Hib)</option>
                                <option value="OPV (Oral Polio)">OPV (Oral Polio)</option>
                                <option value="IPV (Inactivated Polio)">IPV (Inactivated Polio)</option>
                                <option value="PCV (Pneumococcal)">PCV (Pneumococcal)</option>
                                <option value="MMR (Measles-Mumps-Rubella)">MMR (Measles-Mumps-Rubella)</option>
                                <option value="Td (Tetanus Diphtheria)">Td (Tetanus Diphtheria)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dose Sequence</label>
                            <select name="service_specific_data[dose_sequence]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="Dose 1">Dose 1</option>
                                <option value="Dose 2">Dose 2</option>
                                <option value="Dose 3">Dose 3</option>
                                <option value="Booster">Booster</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Administration Site</label>
                            <input type="text" name="service_specific_data[admin_site]" placeholder="e.g. Left Deltoid, Right Thigh" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <!-- 4. CVD Screening -->
                    <div x-show="programType === 'cvd_screening'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cardiovascular Risk Category</label>
                            <select name="service_specific_data[cvd_risk]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="Low (<10%)">Low (&lt; 10%)</option>
                                <option value="Moderate (10% to <20%)">Moderate (10% to &lt; 20%)</option>
                                <option value="High (>=20%)">High (&ge; 20%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Target Organ Damage Screen</label>
                            <input type="text" name="service_specific_data[organ_damage]" placeholder="e.g. None reported, hypertensive retinopathy" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <!-- 5. PhilPEN -->
                    <div x-show="programType === 'philpen'" class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tobacco Exposure</label>
                            <select name="service_specific_data[tobacco_use]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="Non-smoker">Non-smoker</option>
                                <option value="Current smoker">Current smoker</option>
                                <option value="Ex-smoker">Ex-smoker</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Alcohol Consumption</label>
                            <select name="service_specific_data[alcohol_use]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="None">None</option>
                                <option value="Occasional drinker">Occasional drinker</option>
                                <option value="Binge / Heavy drinker">Binge / Heavy drinker</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Fasting Blood Sugar (mg/dL)</label>
                            <input type="number" step="0.1" name="service_specific_data[fbs]" placeholder="e.g. 95" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <!-- 6. NTP -->
                    <div x-show="programType === 'ntp'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Cough Duration</label>
                            <select name="service_specific_data[cough_duration]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="No cough">No cough</option>
                                <option value="Less than 2 weeks">Less than 2 weeks</option>
                                <option value="2 weeks or more (Presumptive TB)">2 weeks or more (Presumptive TB)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sputum / GeneXpert Status</label>
                            <input type="text" name="service_specific_data[tb_testing]" placeholder="e.g. Specimen collected, referred for X-ray" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>

                    <!-- 7. Purok Kalusugan -->
                    <div x-show="programType === 'purok_kalusugan'" class="p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Purok Activity / Household Outreach Notes</label>
                        <input type="text" name="service_specific_data[outreach_activity]" placeholder="e.g. Purok household check, environmental sanitation visit" class="w-full text-sm rounded-lg border-slate-300">
                    </div>

                    <!-- 8. BNS Program -->
                    <div x-show="programType === 'bns_program'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200" style="display: none;">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">BNS Intervention</label>
                            <select name="service_specific_data[bns_intervention]" class="w-full text-sm rounded-lg border-slate-300">
                                <option value="Supplementary Feeding Intake">Supplementary Feeding Intake</option>
                                <option value="Micronutrient Supplementation (Vit A / Iron)">Micronutrient Supplementation (Vit A / Iron)</option>
                                <option value="Deworming Tablet Administered">Deworming Tablet Administered</option>
                                <option value="Infant and Young Child Feeding Counseling">Infant and Young Child Feeding Counseling</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nutritional Category</label>
                            <input type="text" name="service_specific_data[bns_category]" placeholder="e.g. Target for 120-day feeding" class="w-full text-sm rounded-lg border-slate-300">
                        </div>
                    </div>
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
                </div>

                <hr class="border-slate-100">

                <!-- Follow-up Scheduling Section -->
                <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-900">Follow-up Scheduling</h3>
                            <p class="text-[11px] text-blue-700">Schedule the next visit directly without manual calendar cross-referencing.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Next Follow-up Date</label>
                            <input type="date" name="next_follow_up_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('next_follow_up_date') }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="schedule_follow_up" value="1" checked class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span class="text-xs font-semibold text-slate-700">Auto-create entry in Appointments Calendar</span>
                            </label>
                        </div>
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

