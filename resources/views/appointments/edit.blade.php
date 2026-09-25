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

            <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="p-6 space-y-6">
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
                    <select name="service_type" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="General Consultation" {{ old('service_type', $appointment->service_type) === 'General Consultation' ? 'selected' : '' }}>General Consultation</option>
                        @foreach ($services as $key => $title)
                            <option value="{{ $key }}" {{ old('service_type', $appointment->service_type) == $key ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date & Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date ? $appointment->appointment_date->format('Y-m-d') : '') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Appointment Time</label>
                        <input type="time" name="appointment_time" value="{{ old('appointment_time', $appointment->appointment_time ? substr($appointment->appointment_time, 0, 5) : '') }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
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
                </div>

                <!-- Purpose -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Purpose / Reason <span class="text-rose-500">*</span></label>
                    <input type="text" name="purpose" value="{{ old('purpose', $appointment->purpose) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Notes / Clinical Outcome</label>
                    <textarea name="status_notes" rows="3" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('status_notes', $appointment->status_notes) }}</textarea>
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

