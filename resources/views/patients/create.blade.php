<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Breadcrumb / Back button -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('patients.index') }}" class="hover:text-slate-800">&larr; Back to Patient Directory</a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Register New Patient</h1>
                <p class="text-xs text-slate-500 mt-1">Encode patient demographic and personal information for centralized health-center records.</p>
            </div>

            <form method="POST" action="{{ route('patients.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Identification -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Identification & Assignment</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Patient Control Number <span class="text-rose-500">*</span></label>
                            <input type="text" name="patient_control_number" value="{{ old('patient_control_number', $suggestedControlNumber) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                            <span class="text-[11px] text-slate-400">Standard auto-generated ID for Tacunan Health Center.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Purok Assignment <span class="text-rose-500">*</span></label>
                            <select name="purok_id" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Select Barangay Tacunan Purok --</option>
                                @foreach ($puroks as $purok)
                                    <option value="{{ $purok->id }}" {{ old('purok_id') == $purok->id ? 'selected' : '' }}>
                                        {{ $purok->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Personal Name -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Personal Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">First Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Middle Name</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Last Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div class="sm:col-span-1">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Suffix (e.g. Jr, Sr, III)</label>
                            <input type="text" name="suffix" value="{{ old('suffix') }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sex <span class="text-rose-500">*</span></label>
                            <select name="sex" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Select Sex --</option>
                                <option value="Male" {{ old('sex') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Date of Birth <span class="text-rose-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Civil Status</label>
                            <select name="civil_status" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">-- Select Status --</option>
                                @foreach (['Single', 'Married', 'Widowed', 'Separated', 'Live-in'] as $status)
                                    <option value="{{ $status }}" {{ old('civil_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Contact and Health Identification -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Address & Contact Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Street Address / House No.</label>
                            <input type="text" name="street_address" value="{{ old('street_address') }}" placeholder="e.g. Block 4 Lot 12, Tacunan Village" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Number</label>
                            <input type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="09xxxxxxxxx" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">PhilHealth ID Number</label>
                            <input type="text" name="philhealth_number" value="{{ old('philhealth_number') }}" placeholder="xx-xxxxxxxxx-x" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Blood Type</label>
                            <select name="blood_type" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">Unknown / Not Tested</option>
                                @foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                                    <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Emergency Contact -->
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Emergency Contact (Guardian / Relative)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="Full Name" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" placeholder="09xxxxxxxxx" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('patients.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Save Patient Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

