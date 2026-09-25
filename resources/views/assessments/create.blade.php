<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center justify-between">
            <a href="{{ route('patients.show', $patient) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to {{ $patient->full_name }} Profile
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Record Health Assessment & Vitals</h1>
                        <p class="text-xs text-slate-500 mt-1">Record physical anthropometric measurements and clinical vital signs for BNS / general intake.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-mono text-xs font-bold border border-emerald-200">
                        {{ $patient->patient_control_number }}
                    </span>
                </div>
            </div>

            <!-- Patient Quick Info Banner -->
            <div class="px-6 py-3 bg-emerald-50/60 border-b border-emerald-100 flex items-center justify-between text-xs text-emerald-900">
                <div>
                    <strong>Patient:</strong> {{ $patient->full_name }} &bull; {{ $patient->sex }}, {{ $patient->age }} years old
                </div>
                <div>
                    <strong>Purok:</strong> {{ $patient->purok->name }}
                </div>
            </div>

            <form method="POST" action="{{ route('assessments.store', $patient) }}" class="p-6 space-y-6">
                @csrf

                <!-- Date -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Assessment Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="assessment_date" value="{{ old('assessment_date', $today) }}" max="{{ date('Y-m-d') }}" required class="w-full sm:w-64 text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Anthropometrics (BNS) -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Anthropometric Measurements (BNS Growth Monitoring)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Weight (kg)</label>
                            <input type="number" step="0.1" min="1" max="300" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="e.g. 55.4" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Height (cm)</label>
                            <input type="number" step="0.1" min="30" max="250" name="height_cm" value="{{ old('height_cm') }}" placeholder="e.g. 162.5" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <span class="text-[11px] text-slate-400">BMI will be calculated automatically upon saving ($kg / m^2$).</span>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Clinical Vitals -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Vital Signs</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Pressure - Systolic (mmHg)</label>
                            <input type="number" min="50" max="260" name="systolic_bp" value="{{ old('systolic_bp') }}" placeholder="e.g. 120" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Pressure - Diastolic (mmHg)</label>
                            <input type="number" min="30" max="160" name="diastolic_bp" value="{{ old('diastolic_bp') }}" placeholder="e.g. 80" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pulse / Heart Rate (bpm)</label>
                            <input type="number" min="30" max="220" name="pulse_rate" value="{{ old('pulse_rate') }}" placeholder="e.g. 75" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Respiratory Rate (breaths/min)</label>
                            <input type="number" min="10" max="80" name="respiratory_rate" value="{{ old('respiratory_rate') }}" placeholder="e.g. 18" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Body Temperature (&deg;C)</label>
                            <input type="number" step="0.1" min="30" max="45" name="temperature_celsius" value="{{ old('temperature_celsius') }}" placeholder="e.g. 36.6" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nutritional Status Assessment</label>
                            <select name="nutritional_status" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Auto-assign based on BMI</option>
                                <option value="Normal" {{ old('nutritional_status') == 'Normal' ? 'selected' : '' }}>Normal</option>
                                <option value="Underweight" {{ old('nutritional_status') == 'Underweight' ? 'selected' : '' }}>Underweight</option>
                                <option value="Overweight" {{ old('nutritional_status') == 'Overweight' ? 'selected' : '' }}>Overweight</option>
                                <option value="Obese" {{ old('nutritional_status') == 'Obese' ? 'selected' : '' }}>Obese</option>
                                <option value="Wasted" {{ old('nutritional_status') == 'Wasted' ? 'selected' : '' }}>Wasted (Acute Malnutrition)</option>
                                <option value="Stunted" {{ old('nutritional_status') == 'Stunted' ? 'selected' : '' }}>Stunted (Chronic Malnutrition)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Remarks -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Observation Notes / Remarks</label>
                    <textarea name="notes" rows="3" placeholder="Enter any observable symptoms, patient complaints, or nutritional notes..." class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('notes') }}</textarea>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Save Vitals Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

