<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Health Center Reports</h1>
            <p class="text-sm text-slate-500 mt-1">Operational summaries, demographics, service counts, and inventory monitoring for Barangay Tacunan.</p>
        </div>

        <!-- 4 Reports Hub Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. Patient Demographics Report -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:border-emerald-300 transition">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Patient Profiling & Purok Report</h2>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Total patient count, distribution per Purok, sex ratio, and printable patient registry for Barangay Tacunan.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('reports.patients') }}" class="inline-flex items-center text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Generate Patient Report &rarr;
                    </a>
                </div>
            </div>

            <!-- 2. Health Services Summary -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:border-emerald-300 transition">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Health Services Monthly Summary</h2>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Consultation tallies across the 8 authorized programs (Family Planning, Prenatal, Immunization, CVD, PhilPEN, NTP, Purok Kalusugan, BNS).
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('reports.services') }}" class="inline-flex items-center text-xs font-bold text-blue-700 hover:text-blue-800">
                        Generate Services Report &rarr;
                    </a>
                </div>
            </div>

            <!-- 3. Appointments & Follow-ups -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:border-emerald-300 transition">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Appointments & Follow-up Summary</h2>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Follow-up compliance metrics, tally of scheduled vs attended vs missed checkups across specific date ranges.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('reports.appointments') }}" class="inline-flex items-center text-xs font-bold text-indigo-700 hover:text-indigo-800">
                        Generate Follow-up Report &rarr;
                    </a>
                </div>
            </div>

            <!-- 4. Medicine & Vaccine Inventory Summary -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col justify-between hover:border-emerald-300 transition">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900">Inventory, Low Stock & Near-Expiry Report</h2>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Comprehensive stock balance, batches expiring within 90 days, items requiring reorder, and stock movement logs.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('reports.inventory') }}" class="inline-flex items-center text-xs font-bold text-amber-700 hover:text-amber-800">
                        Generate Inventory Report &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

