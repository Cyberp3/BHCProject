<x-app-layout>
    <div class="space-y-6">
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

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-8">
            <div class="text-center border-b border-slate-200 pb-4">
                <h3 class="text-xs uppercase font-bold text-slate-400 tracking-widest">Republic of the Philippines &bull; City of Davao</h3>
                <h1 class="text-xl font-extrabold text-slate-900 mt-1">Barangay Tacunan Health Center</h1>
                <h2 class="text-sm font-semibold text-amber-700 mt-0.5">Medicine & Vaccine Inventory Status Report</h2>
                <p class="text-xs text-slate-400 mt-1">Generated on {{ now()->format('F d, Y h:i A') }}</p>
            </div>

            <!-- SECTION 1: LOW STOCK ITEMS (Reorder Alert) -->
            <div>
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
            <div>
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
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Overall Inventory Summary</h3>
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
            </div>
        </div>
    </div>
</x-app-layout>

