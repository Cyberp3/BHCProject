<x-app-layout>
    <div class="space-y-6">
        <!-- Page Title Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Health Center Dashboard</h1>
            <p class="text-sm text-slate-500 mt-1">Operational summary, patient admissions, and inventory status.</p>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Patients -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between min-w-0">
                <div class="min-w-0 flex-1 mr-3">
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider truncate">Total Patients</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1 truncate">{{ number_format($totalPatients) }}</div>
                    <a href="{{ route('patients.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1 mt-2 truncate">
                        View directory &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Total Consultations -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between min-w-0">
                <div class="min-w-0 flex-1 mr-3">
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider truncate">Total Consultations</div>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1 truncate">{{ number_format($totalServicesCount) }}</div>
                    <a href="{{ route('services.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1 mt-2 truncate">
                        View services &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Low Stock Items -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between min-w-0">
                <div class="min-w-0 flex-1 mr-3">
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider truncate">Low Stock Items</div>
                    <div class="text-3xl font-extrabold {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-slate-900' }} mt-1 truncate">
                        {{ number_format($lowStockCount) }}
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs text-amber-600 hover:text-amber-700 font-medium inline-flex items-center gap-1 mt-2 truncate">
                        Check inventory &rarr;
                    </a>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Near Expiry Items -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex items-center justify-between min-w-0">
                <div class="min-w-0 flex-1 mr-3">
                    <div class="text-xs font-semibold uppercase text-slate-400 tracking-wider truncate">Near Expiry (&le; 90 Days)</div>
                    <div class="text-3xl font-extrabold {{ $nearExpiryCount > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-1 truncate">
                        {{ number_format($nearExpiryCount) }}
                    </div>
                    @if(Auth::user()->canAccessReports())
                        <a href="{{ route('reports.inventory') }}" class="text-xs text-rose-600 hover:text-rose-700 font-medium inline-flex items-center gap-1 mt-2 truncate">
                            View batches &rarr;
                        </a>
                    @else
                        <span class="text-xs text-slate-400 font-medium inline-flex items-center gap-1 mt-2 truncate">
                            Stock monitored
                        </span>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- 2 Column Section: Recent Health Services & Recent Patients -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Consultations & Health Services -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50 shrink-0 flex-shrink-0">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2 min-w-0 truncate">
                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0 flex-shrink-0"></span>
                        <span class="truncate">Recent Consultations & Services</span>
                    </h2>
                    <a href="{{ route('services.index') }}" class="text-xs text-blue-600 hover:underline font-medium shrink-0 flex-shrink-0 ml-2">View All</a>
                </div>

                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($recentServices as $srv)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition min-w-0">
                            <div class="min-w-0 flex-1 mr-3">
                                <a href="{{ route('services.show', $srv) }}" class="font-semibold text-sm text-slate-900 hover:text-emerald-600 truncate block">
                                    {{ $srv->patient->full_name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5 truncate">
                                    {{ $srv->service_title }} &bull; <span class="font-medium text-slate-700">{{ $srv->service_date ? $srv->service_date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 shrink-0 flex-shrink-0">
                                {{ $srv->user ? $srv->user->name : 'Staff' }}
                            </span>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-sm">
                            No consultations recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recently Registered Patients -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50 shrink-0 flex-shrink-0">
                    <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2 min-w-0 truncate">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 flex-shrink-0"></span>
                        <span class="truncate">Recently Registered Patients</span>
                    </h2>
                    <a href="{{ route('patients.index') }}" class="text-xs text-emerald-600 hover:underline font-medium shrink-0 flex-shrink-0 ml-2">View All</a>
                </div>

                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($recentPatients as $patient)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50/80 transition min-w-0">
                            <div class="min-w-0 flex-1 mr-3">
                                <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-sm text-slate-900 hover:text-emerald-600 truncate block">
                                    {{ $patient->full_name }}
                                </a>
                                <div class="text-xs text-slate-500 mt-0.5 truncate">
                                    {{ $patient->patient_control_number }} &bull; {{ $patient->purok->name }} &bull; {{ $patient->sex }}, {{ $patient->age }} yrs old
                                </div>
                            </div>
                            <a href="{{ route('patients.show', $patient) }}" class="text-xs text-slate-400 hover:text-slate-600 shrink-0 flex-shrink-0">
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
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-5 flex flex-col">
                <div class="flex items-center justify-between mb-4 shrink-0 flex-shrink-0">
                    <h2 class="font-bold text-slate-800 text-sm truncate mr-2">Patient Distribution by Purok</h2>
                    @if(Auth::user()->canAccessReports())
                        <a href="{{ route('reports.patients') }}" class="text-xs text-emerald-600 hover:underline font-medium shrink-0 flex-shrink-0">Full Purok Report</a>
                    @endif
                </div>

                <div class="space-y-3 flex-1">
                    @foreach ($purokBreakdown as $purok)
                        @php
                            $percentage = $totalPatients > 0 ? round(($purok->patients_count / $totalPatients) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-medium text-slate-700 mb-1">
                                <span class="truncate mr-2">{{ $purok->name }}</span>
                                <span class="text-slate-500 shrink-0 flex-shrink-0">{{ $purok->patients_count }} patients ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full" @style(['width' => $percentage . '%'])></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent System Activities -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50 shrink-0 flex-shrink-0">
                    <h2 class="font-bold text-slate-800 text-sm truncate mr-2">Recent System Activities</h2>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('audit-logs.index') }}" class="text-xs text-slate-500 hover:underline font-medium shrink-0 flex-shrink-0">View All Logs</a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 flex-1">
                    @forelse ($recentActivities as $log)
                        <div class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition text-xs min-w-0">
                            <span class="px-2 py-0.5 rounded font-bold text-[10px] bg-slate-100 text-slate-700 border border-slate-200 shrink-0 flex-shrink-0 mt-0.5">
                                {{ $log->module }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-slate-800 font-medium truncate">{{ $log->description }}</p>
                                <span class="text-slate-400 text-[11px] truncate block">
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

