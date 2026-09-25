<x-app-layout>
    <div class="space-y-6">
        <!-- Page Title Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Health Center Dashboard</h1>
                <p class="text-sm text-slate-500 mt-1">Operational summary for Barangay Tacunan Health Center, Davao City.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Patient
                </a>
                <a href="{{ route('inventory.dispense') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-lg text-sm font-semibold shadow-xs transition">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Dispense Item
                </a>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Patients -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Total Patients</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalPatients) }}</div>
                    <a href="{{ route('patients.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1 mt-2">
                        View directory &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Today's Appointments -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Today's Appointments</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($todayAppointmentsCount) }}</div>
                    <a href="{{ route('appointments.index', ['filter' => 'today']) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 mt-2">
                        View schedule &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Low Stock Items -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Low Stock Items</div>
                    <div class="text-3xl font-extrabold {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1">
                        {{ number_format($lowStockCount) }}
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium inline-flex items-center gap-1 mt-2">
                        Check inventory &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Near Expiry Items -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Near Expiry (&le; 90 Days)</div>
                    <div class="text-3xl font-extrabold {{ $nearExpiryCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1">
                        {{ number_format($nearExpiryCount) }}
                    </div>
                    <a href="{{ route('reports.inventory') }}" class="text-xs text-rose-600 hover:text-rose-700 font-medium inline-flex items-center gap-1 mt-2">
                        View batches &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- 2 Column Section: Today's Appointments & Recent Patients -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Today's Scheduled Appointments -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Today's Scheduled Appointments
                    </h2>
                    <a href="{{ route('appointments.index', ['filter' => 'today']) }}" class="text-xs text-blue-600 hover:underline font-medium">View All</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($todayAppointments as $appt)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                            <div>
                                <a href="{{ route('patients.show', $appt->patient) }}" class="font-semibold text-sm text-slate-900 hover:text-emerald-600">
                                    {{ $appt->patient->full_name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $appt->purpose }} &bull; <span class="font-medium text-slate-700">{{ $appt->appointment_time ? \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') : 'All Day' }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            No appointments scheduled for today.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recently Registered Patients -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Recently Registered Patients
                    </h2>
                    <a href="{{ route('patients.index') }}" class="text-xs text-emerald-600 hover:underline font-medium">View All</a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentPatients as $patient)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition">
                            <div>
                                <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-sm text-slate-900 hover:text-emerald-600">
                                    {{ $patient->full_name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $patient->patient_control_number }} &bull; {{ $patient->purok->name }} &bull; {{ $patient->sex }}, {{ $patient->age }} yrs old
                                </div>
                            </div>
                            <a href="{{ route('patients.show', $patient) }}" class="text-xs text-slate-400 hover:text-slate-600">
                                View Profile &rarr;
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            No registered patients yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 2 Column Section: Purok Distribution & Recent Activity Audit -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Purok Distribution Summary -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-800 text-sm">Patient Distribution by Purok</h2>
                    <a href="{{ route('reports.patients') }}" class="text-xs text-emerald-600 hover:underline font-medium">Full Purok Report</a>
                </div>

                <div class="space-y-3">
                    @foreach ($purokBreakdown as $purok)
                        @php
                            $percentage = $totalPatients > 0 ? round(($purok->patients_count / $totalPatients) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                                <span>{{ $purok->name }}</span>
                                <span class="text-slate-500">{{ $purok->patients_count }} patients ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full" @style(['width' => $percentage . '%'])></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent System Activities -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm">Recent System Activities</h2>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('audit-logs.index') }}" class="text-xs text-slate-500 hover:underline font-medium">View All Logs</a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($recentActivities as $log)
                        <div class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition text-xs">
                            <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-slate-100 text-slate-700 border border-slate-200 shrink-0 mt-0.5">
                                {{ $log->module }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-800 font-medium truncate">{{ $log->description }}</p>
                                <span class="text-slate-400 text-[11px]">
                                    by {{ $log->user ? $log->user->name : 'System' }} &bull; {{ $log->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-sm">
                            No recent system activity recorded.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

