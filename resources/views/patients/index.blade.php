<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Patient Directory</h1>
                <p class="text-sm text-slate-500 mt-1">Manage and access patient health records in Barangay Tacunan.</p>
            </div>
            <div>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Register New Patient
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs">
            <form method="GET" action="{{ route('patients.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <div class="sm:col-span-6 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or patient control #..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <select name="purok_id" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Filter by Purok (All) --</option>
                        @foreach ($puroks as $purok)
                            <option value="{{ $purok->id }}" {{ request('purok_id') == $purok->id ? 'selected' : '' }}>
                                {{ $purok->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'purok_id']))
                        <a href="{{ route('patients.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
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
                                    <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-slate-900 hover:text-emerald-600">
                                        {{ $patient->full_name }}
                                    </a>
                                    @if ($patient->blood_type)
                                        <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                            {{ $patient->blood_type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    {{ $patient->sex }} &bull; {{ $patient->age }} yrs old
                                    <div class="text-[11px] text-slate-400">DOB: {{ $patient->date_of_birth ? $patient->date_of_birth->format('M d, Y') : 'N/A' }}</div>
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-800">
                                    {{ $patient->purok->name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    {{ $patient->contact_number ?? 'None recorded' }}
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-slate-500">
                                    {{ $patient->philhealth_number ?? 'None' }}
                                </td>
                                <td class="px-5 py-4 text-right space-x-2">
                                    <a href="{{ route('patients.show', $patient) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                        View Profile
                                    </a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Edit
                                    </a>
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

