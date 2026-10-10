<x-app-layout>
    <div class="space-y-6" x-data="{
        section: '{{ in_array(request('section'), ['critical', 'near_expiry', 'overall']) ? request('section') : 'all' }}',
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
                this.section = ['critical', 'near_expiry', 'overall'].includes(s) ? s : 'all';
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
                    <span x-show="section === 'overall'" x-cloak class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-900 border border-blue-300 print:border-black print:text-black print:bg-transparent">
                        Overall Inventory Summary Only (Complete Stock Balance)
                    </span>
                </div>
                <div class="text-[11px] text-slate-500 font-medium print:text-black">
                    <span x-show="section === 'all'">Showing 3 Sections</span>
                    <span x-show="section === 'critical'" x-cloak>{{ $lowStockItems->count() }} Alert Record(s)</span>
                    <span x-show="section === 'near_expiry'" x-cloak>{{ $nearExpiryBatches->count() }} Near-Expiry Batch(es)</span>
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
                        <a href="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold underline">
                            Switch back to 25 items / page
                        </a>
                    </div>
                @endif
            </div>

            <!-- Official Certification & Signatory Section -->
            <x-report-footer />
        </div>
    </div>
</x-app-layout>

