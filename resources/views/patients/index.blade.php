<x-app-layout>
    <div class="space-y-6" x-data="{ privacyMask: {{ Auth::user()->isAdmin() ? 'true' : 'false' }} }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Patient Directory</h1>
                <p class="text-sm text-slate-500 mt-1">Manage and access patient health records in Barangay Tacunan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    RA 10173 Protected
                </span>

                <!-- Privacy Shield Toggle -->
                <button type="button" @click="privacyMask = !privacyMask" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border transition shadow-2xs" :class="privacyMask ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span x-text="privacyMask ? 'Privacy Shield: Masked' : 'Privacy Shield: Visible'"></span>
                </button>

                <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Register New Patient
                </a>
            </div>
        </div>

        <!-- Search & Multi-Criteria Filter Bar -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-3">
            <form method="GET" action="{{ route('patients.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    <!-- Search Input -->
                    <div class="sm:col-span-2 lg:col-span-3 relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or control #..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="sm:col-span-1 lg:col-span-2">
                        <select name="status" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="active" {{ request('status', 'active') === 'active' ? 'selected' : '' }}>Status: Active</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Status: Archived</option>
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Status: All Records</option>
                        </select>
                    </div>

                    <!-- Purok Filter -->
                    <div class="sm:col-span-1 lg:col-span-2">
                        <select name="purok_id" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Purok: All</option>
                            @foreach ($puroks as $purok)
                                <option value="{{ $purok->id }}" {{ request('purok_id') == $purok->id ? 'selected' : '' }}>
                                    {{ $purok->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sex Filter -->
                    <div class="sm:col-span-1 lg:col-span-1">
                        <select name="sex" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Sex: All</option>
                            <option value="Male" {{ request('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ request('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Age Bracket Filter -->
                    <div class="sm:col-span-1 lg:col-span-2">
                        <select name="age_bracket" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Age: All</option>
                            <option value="infant" {{ request('age_bracket') === 'infant' ? 'selected' : '' }}>Infant (0-1 yrs)</option>
                            <option value="child" {{ request('age_bracket') === 'child' ? 'selected' : '' }}>Child (2-12 yrs)</option>
                            <option value="teen" {{ request('age_bracket') === 'teen' ? 'selected' : '' }}>Teen (13-17 yrs)</option>
                            <option value="adult" {{ request('age_bracket') === 'adult' ? 'selected' : '' }}>Adult (18-59 yrs)</option>
                            <option value="senior" {{ request('age_bracket') === 'senior' ? 'selected' : '' }}>Senior (60+ yrs)</option>
                        </select>
                    </div>

                    <!-- Health Service Filter -->
                    <div class="sm:col-span-1 lg:col-span-2">
                        <select name="service_type" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Service: All</option>
                            @foreach ($services as $srvKey => $srvLabel)
                                <option value="{{ $srvKey }}" {{ request('service_type') === $srvKey ? 'selected' : '' }}>
                                    {{ $srvLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-xs">
                    <span class="text-slate-500">
                        Showing <strong class="text-slate-800">{{ $patients->total() }}</strong> patient record{{ $patients->total() === 1 ? '' : 's' }}
                    </span>
                    <div class="flex items-center gap-2">
                        @if (request()->hasAny(['search', 'purok_id', 'sex', 'age_bracket', 'service_type']) || (request('status') && request('status') !== 'active'))
                            <a href="{{ route('patients.index') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-medium transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Clear Filters
                            </a>
                        @endif
                        <button type="submit" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold transition flex items-center gap-1 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Patients Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Control #</th>
                            <th class="px-5 py-3.5">Full Name</th>
                            <th class="px-5 py-3.5">Sex / Age</th>
                            <th class="px-5 py-3.5">Purok</th>
                            <th class="px-5 py-3.5">Contact #</th>
                            <th class="px-5 py-3.5">PhilHealth</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 font-mono text-xs font-semibold text-slate-800">
                                    {{ $patient->patient_control_number }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-slate-900 hover:text-emerald-600">
                                            {{ $patient->full_name }}
                                        </a>
                                        @if ($patient->trashed())
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold border border-amber-200">
                                                Archived
                                            </span>
                                        @endif
                                        @if ($patient->blood_type)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                                {{ $patient->blood_type }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    {{ $patient->sex }} &bull; {{ $patient->age }} yrs old
                                    <div class="text-[11px] text-slate-400">DOB: {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-800">
                                    {{ $patient->purok->name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    <span x-show="!privacyMask">{{ $patient->contact_number ?? 'None recorded' }}</span>
                                    <span x-show="privacyMask" style="display: none;" class="font-mono text-slate-400 font-medium">{{ $patient->masked_contact_number ?? 'None recorded' }}</span>
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-slate-500">
                                    <span x-show="!privacyMask">{{ $patient->philhealth_number ?? 'None' }}</span>
                                    <span x-show="privacyMask" style="display: none;" class="font-mono text-slate-400 font-medium">{{ $patient->masked_philhealth_number ?? 'None' }}</span>
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                        View Profile
                                    </a>
                                    @if (! Auth::user()->isAdmin())
                                        @if (! $patient->trashed())
                                            <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                                Edit Patient
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                                Archived
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-[11px] font-medium bg-slate-50 text-slate-400 border border-slate-200/60">
                                            Read-Only
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="font-medium text-slate-600">No patients found</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Try adjusting your search criteria or register a new patient.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($patients->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

