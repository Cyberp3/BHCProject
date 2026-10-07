<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Breadcrumb -->
        <div class="flex items-center justify-between">
            <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Appointments
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Update Appointment</h1>
                <p class="text-xs text-slate-500 mt-1">Modify follow-up details or change appointment status for {{ $appointment->patient->full_name }}.</p>
            </div>

            <form method="POST" action="{{ route('appointments.update', $appointment) }}" 
                x-data="{
                    serviceType: '{{ old('service_type', $appointment->service_type) }}',
                    appointmentDate: '{{ old('appointment_date', $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : '') }}',
                    appointmentTime: '{{ old('appointment_time', $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '09:00') }}',
                    scheduleMap: {
                        'ntp': {
                            name: 'National Tuberculosis Control Program (NTP / TB-DOTS)',
                            days: [1, 2],
                            label: 'Monday & Tuesday (Morning Only)',
                            session: 'morning',
                            hours: '8:00 AM - 12:00 PM',
                            badge: 'bg-amber-100 text-amber-800 border-amber-200'
                        },
                        'family_planning': {
                            name: 'Family Planning (FP)',
                            days: [1, 2],
                            label: 'Monday & Tuesday (Afternoon Only)',
                            session: 'afternoon',
                            hours: '1:00 PM - 5:00 PM',
                            badge: 'bg-purple-100 text-purple-800 border-purple-200'
                        },
                        'prenatal_care': {
                            name: 'Prenatal Care',
                            days: [2],
                            label: 'Tuesday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-pink-100 text-pink-800 border-pink-200'
                        },
                        'immunization': {
                            name: 'Child Immunization (EPI)',
                            days: [3],
                            label: 'Wednesday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-emerald-100 text-emerald-800 border-emerald-200'
                        },
                        'purok_kalusugan': {
                            name: 'Purok Kalusugan Outreach',
                            days: [4],
                            label: 'Thursday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-teal-100 text-teal-800 border-teal-200'
                        },
                        'bns_program': {
                            name: 'Barangay Nutrition Scholar (BNS) Program',
                            days: [4],
                            label: 'Thursday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-cyan-100 text-cyan-800 border-cyan-200'
                        },
                        'cvd_screening': {
                            name: 'Cardiovascular Disease (CVD) Screening',
                            days: [5],
                            label: 'Friday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-rose-100 text-rose-800 border-rose-200'
                        },
                        'philpen': {
                            name: 'PhilPEN Non-Communicable Disease Assessment',
                            days: [5],
                            label: 'Friday (All Day)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-blue-100 text-blue-800 border-blue-200'
                        },
                        'General Consultation': {
                            name: 'General Consultation / Regular Follow-up',
                            days: [1, 2, 3, 4, 5],
                            label: 'Monday through Friday (Regular Clinic Hours)',
                            session: 'all_day',
                            hours: '8:00 AM - 5:00 PM',
                            badge: 'bg-slate-100 text-slate-800 border-slate-200'
                        }
                    },
                    get currentRule() {
                        return this.scheduleMap[this.serviceType] || null;
                    },
                    get dayWarning() {
                        if (!this.appointmentDate) return null;
                        const parts = this.appointmentDate.split('-');
                        if (parts.length !== 3) return null;
                        const d = new Date(parts[0], parts[1] - 1, parts[2]);
                        const dayOfWeek = d.getDay();
                        const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        if (dayOfWeek === 0 || dayOfWeek === 6) {
                            return 'Clinic Closed on Weekends: Tacunan Health Center operates Monday through Friday.';
                        }
                        const rule = this.currentRule;
                        if (rule && rule.days && !rule.days.includes(dayOfWeek)) {
                            return `Schedule Mismatch: Selected date is ${dayNames[dayOfWeek]}, but ${rule.name} is strictly held on ${rule.label}.`;
                        }
                        if (rule && rule.session && this.appointmentTime) {
                            const hour = parseInt(this.appointmentTime.split(':')[0], 10);
                            if (rule.session === 'morning' && hour >= 12) {
                                return `Session Alert: ${rule.name} is conducted exclusively in the morning session (8:00 AM - 12:00 PM).`;
                            }
                            if (rule.session === 'afternoon' && hour < 12) {
                                return `Session Alert: ${rule.name} is conducted exclusively in the afternoon session (1:00 PM - 5:00 PM).`;
                            }
                        }
                        return null;
                    }
                }"
                class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Patient (Read only) -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700">
                    <span class="font-semibold text-slate-900">{{ $appointment->patient->full_name }}</span>
                    &bull; {{ $appointment->patient->patient_control_number }} &bull; {{ $appointment->patient->purok->name }}
                </div>

                <!-- Program -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Related Service / Program <span class="text-rose-500">*</span></label>
                    <select name="service_type" x-model="serviceType" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="General Consultation" {{ old('service_type', $appointment->service_type) === 'General Consultation' ? 'selected' : '' }}>General Consultation</option>
                        @foreach ($services as $key => $title)
                            <option value="{{ $key }}" {{ old('service_type', $appointment->service_type) == $key ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Interactive Schedule Callout -->
                    <div x-show="currentRule" x-cloak class="mt-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600">Authorized Schedule:</span>
                            <span class="font-semibold text-slate-900" x-text="currentRule?.label"></span>
                        </div>
                        <span class="text-[11px] font-mono text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200" x-text="currentRule?.hours"></span>
                    </div>

                    @error('service_type')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date & Time -->
                <div class="space-y-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Date <span class="text-rose-500">*</span></label>
                            <input type="date" name="appointment_date" x-model="appointmentDate" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Time</label>
                            <input type="time" name="appointment_time" x-model="appointmentTime" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <!-- Client-Side Warning Alert -->
                    <div x-show="dayWarning" x-cloak class="p-3 bg-amber-50 border border-amber-300 rounded-lg text-xs font-semibold text-amber-800 flex items-start gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span x-text="dayWarning"></span>
                    </div>

                    <!-- Server-Side Schedule Error -->
                    @error('appointment_date')
                        <div class="p-3 bg-rose-50 border border-rose-300 rounded-lg text-xs font-semibold text-rose-800 flex items-start gap-2 shadow-2xs">
                            <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    @error('appointment_time')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="scheduled" {{ old('status', $appointment->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="attended" {{ old('status', $appointment->status) === 'attended' ? 'selected' : '' }}>Attended</option>
                        <option value="missed" {{ old('status', $appointment->status) === 'missed' ? 'selected' : '' }}>Missed</option>
                        <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Purpose / Reason <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose', $appointment->purpose) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('purpose')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Notes / Clinical Outcome</label>
                    <textarea name="status_notes" rows="3" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('status_notes', $appointment->status_notes) }}</textarea>
                    @error('status_notes')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Update Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

