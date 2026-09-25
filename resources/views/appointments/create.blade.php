<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ $patient ? route('patients.show', $patient) : route('appointments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Schedule Patient Follow-up</h1>
                <p class="text-xs text-slate-500 mt-1">Book an upcoming clinic consultation or checkup visit for Barangay Tacunan Health Center.</p>
            </div>

            <form method="POST" action="{{ route('appointments.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Patient -->
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
                    @error('patient_id')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Service Program -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Related Program / Purpose <span class="text-rose-500">*</span></label>
                    <select name="service_type" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="General Consultation">General Consultation / Health Follow-up</option>
                        @foreach ($services as $key => $title)
                            <option value="{{ $key }}" {{ old('service_type') == $key ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_type')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $tomorrow) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('appointment_date')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Time</label>
                        <input type="time" name="appointment_time" value="{{ old('appointment_time', '09:00') }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('appointment_time')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Purpose description -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Purpose / Reason <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="e.g. 2nd dose Pentavalent vaccine, BP follow-up, Prenatal 2nd visit" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('purpose')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Instructions -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instructions / Notes to Patient</label>
                    <textarea name="status_notes" rows="3" placeholder="e.g. Bring PhilHealth card, fasting required for FBS, bring child immunization booklet" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('status_notes') }}</textarea>
                    @error('status_notes')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ $patient ? route('patients.show', $patient) : route('appointments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Save Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
