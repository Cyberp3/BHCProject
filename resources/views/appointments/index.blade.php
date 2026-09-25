<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Appointments & Follow-ups</h1>
                <p class="text-sm text-slate-500 mt-1">Schedule and monitor patient checkups, follow-ups, and service schedules.</p>
            </div>
            <div>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Schedule New Appointment
                </a>
            </div>
        </div>

        <!-- Filter Bar & Date Tabs -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex space-x-2">
                    <a href="{{ route('appointments.index', ['filter' => 'upcoming']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'upcoming' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('appointments.index', ['filter' => 'today']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'today' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Today's Schedule
                    </a>
                    <a href="{{ route('appointments.index', ['filter' => 'past']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $filter === 'past' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Past / All Records
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <input type="hidden" name="filter" value="{{ $filter }}">

                <div class="sm:col-span-6 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by patient name..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="sm:col-span-4">
                    <select name="status" class="w-full py-2 px-3 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- All Statuses --</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Attended</option>
                        <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Missed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'status']))
                        <a href="{{ route('appointments.index', ['filter' => $filter]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Date & Time</th>
                            <th class="px-5 py-3.5">Patient</th>
                            <th class="px-5 py-3.5">Purok</th>
                            <th class="px-5 py-3.5">Purpose / Service</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Scheduled By</th>
                            <th class="px-5 py-3.5 text-right">Update Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($appointments as $appt)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900 text-xs">
                                        {{ $appt->appointment_date->format('M d, Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : 'All Day' }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('patients.show', $appt->patient) }}" class="font-semibold text-slate-900 hover:text-emerald-600">
                                        {{ $appt->patient->full_name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $appt->patient->patient_control_number }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs font-medium text-slate-700">
                                    {{ $appt->patient->purok->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-xs font-semibold text-slate-800">{{ $appt->purpose }}</div>
                                    <div class="text-[11px] text-emerald-700 font-medium">
                                        {{ $services[$appt->service_type] ?? ucwords(str_replace('_', ' ', $appt->service_type)) }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $appt->status === 'attended' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($appt->status === 'cancelled' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($appt->status === 'missed' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200')) }}">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">
                                    {{ $appt->scheduledBy ? $appt->scheduledBy->name : 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-right space-x-1">
                                    @if ($appt->status === 'scheduled')
                                        <form method="POST" action="{{ route('appointments.updateStatus', $appt) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="attended">
                                            <button type="submit" title="Mark as Attended" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded text-xs font-bold border border-emerald-200 transition">
                                                &#10003; Attended
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('appointments.updateStatus', $appt) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="missed">
                                            <button type="submit" title="Mark as Missed" class="px-2 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded text-xs font-bold border border-amber-200 transition">
                                                Missed
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('appointments.edit', $appt) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    No appointments found for the selected view.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($appointments->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

