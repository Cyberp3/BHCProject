<x-app-layout>
    <div class="space-y-6" x-data="{
        section: '{{ in_array(request('section'), ['critical', 'near_expiry', 'overall', 'movements']) ? request('section') : 'all' }}',
        setSection(val) {
            this.section = val;
            const url = new URL(window.location);
            if (val === 'all') {
                url.searchParams.delete('section');
            } else {
                url.searchParams.set('section', val);
            }
            window.history.pushState({}, '', url);
        },
        init() {
            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                const s = params.get('section');
                this.section = ['critical', 'near_expiry', 'overall', 'movements'].includes(s) ? s : 'all';
            });
        }
    }">
        <!-- Breadcrumb & Print -->
        <div class="flex items-center justify-between no-print">
            <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Reports
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Report
            </button>
        </div>

        <!-- Report Specification Toolbar (Filter Buttons) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-3.5 no-print">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Report Specification:
                    </span>
                    <span class="text-[11px] text-slate-500 hidden md:inline">Specify section to print or examine:</span>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- All Sections Button -->
                    <a href="{{ request()->fullUrlWithQuery(['section' => 'all']) }}" 
                       @click.prevent="setSection('all')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border cursor-pointer"
                       :class="section === 'all' 
                           ? 'bg-slate-900 text-white border-slate-900 shadow-xs ring-2 ring-slate-900/10' 
                           : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50 hover:text-slate-900'">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>All Sections</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                              :class="section === 'all' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600'">
                            Full
                        </span>
                    </a>

                    <!-- Critical / Low Stock Button -->
                    <a href="{{ request()->fullUrlWithQuery(['section' => 'critical']) }}" 
                       @click.prevent="setSection('critical')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border cursor-pointer"
                       :class="section === 'critical' 
                           ? 'bg-amber-600 text-white border-amber-600 shadow-xs ring-2 ring-amber-600/20' 
                           : 'bg-white text-amber-900 border-slate-300 hover:bg-amber-50/50 hover:border-amber-300'">
                        <span class="w-2 h-2 rounded-full" :class="section === 'critical' ? 'bg-white ring-2 ring-amber-300' : 'bg-amber-500'"></span>
                        <span>Critical Stock</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                              :class="section === 'critical' ? 'bg-amber-700 text-white' : 'bg-amber-100 text-amber-800'">
                            {{ $lowStockItems->count() }}
                        </span>
                    </a>

                    <!-- Near-Expiry Batches Button -->
                    <a href="{{ request()->fullUrlWithQuery(['section' => 'near_expiry']) }}" 
                       @click.prevent="setSection('near_expiry')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border cursor-pointer"
                       :class="section === 'near_expiry' 
                           ? 'bg-rose-600 text-white border-rose-600 shadow-xs ring-2 ring-rose-600/20' 
                           : 'bg-white text-rose-900 border-slate-300 hover:bg-rose-50/50 hover:border-rose-300'">
                        <span class="w-2 h-2 rounded-full" :class="section === 'near_expiry' ? 'bg-white ring-2 ring-rose-300' : 'bg-rose-500'"></span>
                        <span>Near-Expiry</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                              :class="section === 'near_expiry' ? 'bg-rose-700 text-white' : 'bg-rose-100 text-rose-800'">
                            {{ $nearExpiryBatches->count() }}
                        </span>
                    </a>

                    <!-- Stock Movement Tracker Button (Whiteboard Ledger) -->
                    <a href="{{ request()->fullUrlWithQuery(['section' => 'movements']) }}" 
                       @click.prevent="setSection('movements')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border cursor-pointer"
                       :class="section === 'movements' 
                           ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs ring-2 ring-indigo-600/20' 
                           : 'bg-white text-indigo-900 border-slate-300 hover:bg-indigo-50/50 hover:border-indigo-300'">
                        <span class="w-2 h-2 rounded-full" :class="section === 'movements' ? 'bg-white ring-2 ring-indigo-300' : 'bg-indigo-500'"></span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                        <span>Stock Movement Tracker</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                              :class="section === 'movements' ? 'bg-indigo-700 text-white' : 'bg-indigo-100 text-indigo-800'">
                            Ledger
                        </span>
                    </a>

                    <!-- Overall Inventory Button -->
                    <a href="{{ request()->fullUrlWithQuery(['section' => 'overall']) }}" 
                       @click.prevent="setSection('overall')"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition border cursor-pointer"
                       :class="section === 'overall' 
                           ? 'bg-blue-600 text-white border-blue-600 shadow-xs ring-2 ring-blue-600/20' 
                           : 'bg-white text-blue-900 border-slate-300 hover:bg-blue-50/50 hover:border-blue-300'">
                        <span class="w-2 h-2 rounded-full" :class="section === 'overall' ? 'bg-white ring-2 ring-blue-300' : 'bg-blue-500'"></span>
                        <span>Overall Inventory</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold"
                              :class="section === 'overall' ? 'bg-blue-700 text-white' : 'bg-blue-100 text-blue-800'">
                            {{ number_format($items->total()) }}
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Official Report Paper Sheet Container -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 flex flex-col gap-8 print:p-0 print:border-none print:shadow-none print:gap-6">
            <!-- Official Letterhead Header -->
            <x-report-header 
                title="Medicine & Vaccine Inventory Status Report" 
                subtitle="Stock Balance, Safety Thresholds, and Near-Expiry Tracking" 
            />

            <!-- Specified Scope Banner (Prints on Paper & Updates Dynamically) -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 text-xs">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] print:text-black">Specified Report Scope:</span>
                    <span x-show="section === 'all'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-300 print:border-black print:text-black print:bg-transparent">
                        Comprehensive Report (All Sections Included)
                    </span>
                    <span x-show="section === 'critical'" x-cloak class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-900 border border-amber-300 print:border-black print:text-black print:bg-transparent">
                        Critical / Low Stock Items Only (Reorder Alert)
                    </span>
                    <span x-show="section === 'near_expiry'" x-cloak class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-900 border border-rose-300 print:border-black print:text-black print:bg-transparent">
                        Near-Expiry Batches Only (&le; 90 Days Monitoring)
                    </span>
                    <span x-show="section === 'movements'" x-cloak class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-900 border border-indigo-300 print:border-black print:text-black print:bg-transparent">
                        Stock Movement &amp; Balance Tracker (Beginning + Stock In &minus; Stock Out = Remaining Balance)
                    </span>
                    <span x-show="section === 'overall'" x-cloak class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-900 border border-blue-300 print:border-black print:text-black print:bg-transparent">
                        Overall Inventory Summary Only (Complete Stock Balance)
                    </span>
                </div>
                <div class="text-[11px] text-slate-500 font-medium print:text-black">
                    <span x-show="section === 'all'">Showing 4 Sections</span>
                    <span x-show="section === 'critical'" x-cloak>{{ $lowStockItems->count() }} Alert Record(s)</span>
                    <span x-show="section === 'near_expiry'" x-cloak>{{ $nearExpiryBatches->count() }} Near-Expiry Batch(es)</span>
                    <span x-show="section === 'movements'" x-cloak>{{ $stockMovements->count() }} Stock Movement Items ({{ \Carbon\Carbon::parse($startDate)->format('M d') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</span>
                    <span x-show="section === 'overall'" x-cloak>{{ number_format($items->total()) }} Catalog Item(s)</span>
                </div>
            </div>

            <!-- SECTION 1: LOW STOCK ITEMS (Reorder Alert) -->
            <div x-show="section === 'all' || section === 'critical'" x-transition.opacity>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Critical / Low Stock Items ({{ $lowStockItems->count() }} Alert{{ $lowStockItems->count() == 1 ? '' : 's' }})
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-amber-50 font-bold uppercase text-amber-900">
                            <tr>
                                <th class="p-2.5 border">Item Code</th>
                                <th class="p-2.5 border">Medicine / Vaccine Name</th>
                                <th class="p-2.5 border">Category</th>
                                <th class="p-2.5 border text-center">Current Total Stock</th>
                                <th class="p-2.5 border text-center">Minimum Threshold</th>
                                <th class="p-2.5 border text-center">Shortfall</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lowStockItems as $item)
                                <tr class="hover:bg-amber-50/40">
                                     <td class="p-2.5 border font-mono font-bold">{{ $item->item_code }}</td>
                                    <td class="p-2.5 border font-semibold">{{ $item->name }} ({{ $item->dosage_form }})</td>
                                    <td class="p-2.5 border uppercase text-[11px]">{{ $item->category }}</td>
                                    <td class="p-2.5 border text-center font-extrabold text-amber-700 text-sm">
                                        {{ $item->total_stock }} {{ $item->unit_of_measure }}
                                    </td>
                                    <td class="p-2.5 border text-center font-semibold">{{ $item->minimum_stock_alert }} {{ $item->unit_of_measure }}</td>
                                    <td class="p-2.5 border text-center text-rose-600 font-bold">
                                        -{{ max(0, $item->minimum_stock_alert - $item->total_stock) }} {{ $item->unit_of_measure }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-slate-400">All supplies are currently above minimum stock thresholds.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION 2: NEAR EXPIRY BATCHES (<= 90 Days) -->
            <div x-show="section === 'all' || section === 'near_expiry'" x-transition.opacity>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Near-Expiry Batches (&le; 90 Days Monitoring - {{ $nearExpiryBatches->count() }} Batches)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-rose-50 font-bold uppercase text-rose-900">
                            <tr>
                                <th class="p-2.5 border">Item Code</th>
                                <th class="p-2.5 border">Item Name</th>
                                <th class="p-2.5 border">Batch / Lot #</th>
                                <th class="p-2.5 border">Expiration Date</th>
                                <th class="p-2.5 border text-center">Days Remaining</th>
                                <th class="p-2.5 border text-center">Remaining Quantity</th>
                                <th class="p-2.5 border">Cold Chain Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($nearExpiryBatches as $batch)
                                <tr class="hover:bg-rose-50/40">
                                    <td class="p-2.5 border font-mono font-bold">{{ $batch->item->item_code }}</td>
                                    <td class="p-2.5 border font-semibold">{{ $batch->item->name }}</td>
                                    <td class="p-2.5 border font-mono">{{ $batch->batch_number }}</td>
                                    <td class="p-2.5 border font-bold text-rose-700">{{ $batch->expiration_date->format('M d, Y') }}</td>
                                    <td class="p-2.5 border text-center font-bold text-rose-600">
                                        {{ $batch->days_until_expiration }} days
                                    </td>
                                    <td class="p-2.5 border text-center font-extrabold">
                                        {{ $batch->current_quantity }} {{ $batch->item->unit_of_measure }}
                                    </td>
                                    <td class="p-2.5 border text-slate-500 text-[11px]">
                                        {{ $batch->item->is_cold_chain ? ($batch->item->storage_temperature_note ?? '2°C to 8°C') : 'Standard Room Temp' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-4 text-center text-slate-400">No active batches are near expiration.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SECTION: STOCK MOVEMENT & BALANCE TRACKER (Beginning + Stock In - Stock Out = Remaining Balance) -->
            <div x-show="section === 'all' || section === 'movements'" x-transition.opacity>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                            Stock Movement &amp; Balance Tracker ({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} &ndash; {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})
                        </h3>
                    </div>
                    <span class="text-[11px] text-slate-500 font-mono print:text-black">
                        Formula: Beginning + Stock In &minus; Stock Out = Remaining Balance
                    </span>
                </div>

                <!-- Date Range Filter Form (no-print) -->
                <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-200 no-print flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Tracking Period:
                        </span>
                        <span class="text-[11px] text-slate-500 hidden sm:inline">Specify start and end dates to track inventory flow:</span>
                    </div>

                    <form method="GET" action="{{ route('reports.inventory') }}" class="flex flex-wrap items-center gap-2.5">
                        <input type="hidden" name="section" :value="section">
                        <div class="flex items-center gap-1 text-xs text-slate-600">
                            <label class="font-semibold">From:</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs rounded-lg border-slate-300 py-1 px-2.5">
                        </div>
                        <div class="flex items-center gap-1 text-xs text-slate-600">
                            <label class="font-semibold">To:</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs rounded-lg border-slate-300 py-1 px-2.5">
                        </div>
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition">
                            Filter Ledger
                        </button>
                    </form>
                </div>

                <!-- Stock Movement KPI Summary Cards (Print and Screen) -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Beginning Balance</span>
                        <div class="text-lg font-black text-slate-800 mt-0.5">{{ number_format($movementTotals['beginning']) }}</div>
                        <span class="text-[10px] text-slate-400">As of {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }}</span>
                    </div>
                    <div class="p-3 bg-emerald-50/70 rounded-lg border border-emerald-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider">Total Stock In (+)</span>
                        <div class="text-lg font-black text-emerald-800 mt-0.5">+{{ number_format($movementTotals['stock_in']) }}</div>
                        <span class="text-[10px] text-emerald-600">Received Batches</span>
                    </div>
                    <div class="p-3 bg-rose-50/70 rounded-lg border border-rose-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-rose-700 tracking-wider">Total Stock Out (&minus;)</span>
                        <div class="text-lg font-black text-rose-800 mt-0.5">&minus;{{ number_format($movementTotals['stock_out']) }}</div>
                        <span class="text-[10px] text-rose-600">Dispensed Prescriptions</span>
                    </div>
                    <div class="p-3 bg-indigo-50/70 rounded-lg border border-indigo-200 text-center">
                        <span class="text-[10px] uppercase font-bold text-indigo-700 tracking-wider">Remaining Balance (=)</span>
                        <div class="text-lg font-black text-indigo-900 mt-0.5">{{ number_format($movementTotals['remaining']) }}</div>
                        <span class="text-[10px] text-indigo-600">As of {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                    </div>
                </div>

                <!-- Stock Movement Ledger Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-indigo-50 font-bold uppercase text-indigo-950">
                            <tr>
                                <th class="p-2.5 border">Item Code</th>
                                <th class="p-2.5 border">Medicine / Vaccine Name</th>
                                <th class="p-2.5 border">Category</th>
                                <th class="p-2.5 border text-center">Unit</th>
                                <th class="p-2.5 border text-center bg-slate-100/90 text-slate-900 font-bold">Beginning Balance</th>
                                <th class="p-2.5 border text-center bg-emerald-100/70 text-emerald-950 font-bold">Stock In (+)</th>
                                <th class="p-2.5 border text-center bg-rose-100/70 text-rose-950 font-bold">Stock Out (&minus;)</th>
                                <th class="p-2.5 border text-center bg-indigo-100/90 text-indigo-950 font-black">Remaining Balance (=)</th>
                                <th class="p-2.5 border text-center">Net Period Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stockMovements as $row)
                                <tr class="hover:bg-indigo-50/30">
                                    <td class="p-2.5 border font-mono font-bold">{{ $row->item->item_code }}</td>
                                    <td class="p-2.5 border font-semibold">
                                        {{ $row->item->name }}
                                        @if ($row->item->dosage_form)
                                            <span class="text-[11px] text-slate-400 font-normal">({{ $row->item->dosage_form }})</span>
                                        @endif
                                    </td>
                                    <td class="p-2.5 border uppercase text-[11px]">{{ $row->item->category }}</td>
                                    <td class="p-2.5 border text-center text-slate-500">{{ $row->item->unit_of_measure }}</td>
                                    <td class="p-2.5 border text-center font-bold text-slate-800 bg-slate-50/50">
                                        {{ number_format($row->beginning_balance) }}
                                    </td>
                                    <td class="p-2.5 border text-center font-bold {{ $row->stock_in > 0 ? 'text-emerald-700 bg-emerald-50/40' : 'text-slate-400' }}">
                                        {{ $row->stock_in > 0 ? '+' . number_format($row->stock_in) : '0' }}
                                    </td>
                                    <td class="p-2.5 border text-center font-bold {{ $row->stock_out > 0 ? 'text-rose-700 bg-rose-50/40' : 'text-slate-400' }}">
                                        {{ $row->stock_out > 0 ? '-' . number_format($row->stock_out) : '0' }}
                                    </td>
                                    <td class="p-2.5 border text-center font-black text-sm text-indigo-900 bg-indigo-50/50">
                                        {{ number_format($row->remaining_balance) }}
                                    </td>
                                    <td class="p-2.5 border text-center font-bold">
                                        @if ($row->net_change > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-emerald-100 text-emerald-800">
                                                +{{ number_format($row->net_change) }}
                                            </span>
                                        @elseif ($row->net_change < 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-rose-100 text-rose-800">
                                                {{ number_format($row->net_change) }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 font-normal text-[11px]">No Change</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-4 text-center text-slate-400">No inventory items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-100 font-extrabold text-slate-900 border-t-2 border-slate-300">
                            <tr>
                                <td colspan="4" class="p-2.5 border text-right uppercase tracking-wider text-[11px]">
                                    Total Aggregate Stock Units:
                                </td>
                                <td class="p-2.5 border text-center">
                                    {{ number_format($movementTotals['beginning']) }}
                                </td>
                                <td class="p-2.5 border text-center text-emerald-700">
                                    +{{ number_format($movementTotals['stock_in']) }}
                                </td>
                                <td class="p-2.5 border text-center text-rose-700">
                                    &minus;{{ number_format($movementTotals['stock_out']) }}
                                </td>
                                <td class="p-2.5 border text-center text-indigo-950 text-sm">
                                    {{ number_format($movementTotals['remaining']) }}
                                </td>
                                <td class="p-2.5 border text-center">
                                    @php $totalNet = $movementTotals['stock_in'] - $movementTotals['stock_out']; @endphp
                                    <span class="{{ $totalNet > 0 ? 'text-emerald-700' : ($totalNet < 0 ? 'text-rose-700' : 'text-slate-500') }}">
                                        {{ $totalNet > 0 ? '+' : '' }}{{ number_format($totalNet) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- SECTION 3: COMPLETE CURRENT CATALOG -->
            <div x-show="section === 'all' || section === 'overall'" x-transition.opacity>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Overall Inventory Summary ({{ number_format($items->total()) }} Items)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-700 border border-slate-200">
                        <thead class="bg-slate-100 font-bold uppercase text-slate-600">
                            <tr>
                                <th class="p-2.5 border">Item Code</th>
                                <th class="p-2.5 border">Item Name</th>
                                <th class="p-2.5 border">Category</th>
                                <th class="p-2.5 border text-center">Active Batches</th>
                                <th class="p-2.5 border text-center">Total In Stock</th>
                                <th class="p-2.5 border">Unit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-2.5 border font-mono font-bold">{{ $item->item_code }}</td>
                                    <td class="p-2.5 border font-semibold">{{ $item->name }}</td>
                                    <td class="p-2.5 border uppercase text-[11px]">{{ $item->category }}</td>
                                    <td class="p-2.5 border text-center font-medium">{{ $item->batches->count() }}</td>
                                    <td class="p-2.5 border text-center font-bold {{ $item->total_stock <= $item->minimum_stock_alert ? 'text-amber-600' : 'text-slate-900' }}">
                                        {{ $item->total_stock }}
                                    </td>
                                    <td class="p-2.5 border text-slate-500">{{ $item->unit_of_measure }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($items->hasPages())
                    <div class="p-4 border-t border-slate-200 no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ number_format($items->total()) }} catalog items
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ request()->fullUrlWithQuery(['per_page' => 'all']) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold underline">
                                Show all items (for complete printout)
                            </a>
                            {{ $items->links() }}
                        </div>
                    </div>
                @elseif (request('per_page') === 'all')
                    <div class="p-4 border-t border-slate-200 no-print flex items-center justify-between text-xs text-slate-500">
                        <span>Showing all {{ number_format($items->total()) }} catalog items (unpaginated complete view)</span>
                        <a href="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold underline">
                            Switch back to 10 items / page
                        </a>
                    </div>
                @endif
            </div>

            <!-- Official Certification & Signatory Section -->
            <x-report-footer />
        </div>
    </div>
</x-app-layout>

