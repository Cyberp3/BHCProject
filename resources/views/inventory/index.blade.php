<x-app-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Medicine & Vaccine Inventory</h1>
                <p class="text-sm text-slate-500 mt-1">Manage health center supplies, lot tracking, FIFO dispensation, and cold-chain storage.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('inventory.items.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 rounded-lg text-xs font-semibold shadow-2xs transition">
                    + New Item
                </a>
                <a href="{{ route('inventory.batches.receive') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    + Receive Stock
                </a>
                <a href="{{ route('inventory.dispense') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    Dispense Item (FIFO)
                </a>
            </div>
        </div>

        <!-- Metric Badges Alert Banner -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Low stock alert box -->
            <div class="p-4 bg-white rounded-xl border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200' }} shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Low Stock Alert</div>
                        <div class="text-lg font-bold text-slate-900">
                            {{ $lowStockCount }} {{ Str::plural('item', $lowStockCount) }} at or below minimum reorder point
                        </div>
                    </div>
                </div>
            </div>

            <!-- Near expiry alert box -->
            <div class="p-4 bg-white rounded-xl border {{ $nearExpiryBatches->count() > 0 ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200' }} shadow-xs flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Near-Expiry Warning</div>
                        <div class="text-lg font-bold text-slate-900">
                            {{ $nearExpiryBatches->count() }} active {{ Str::plural('batch', $nearExpiryBatches->count()) }} expiring within 90 days
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex space-x-2">
                    <a href="{{ route('inventory.index', ['category' => 'all']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $category === 'all' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All Catalog
                    </a>
                    <a href="{{ route('inventory.index', ['category' => 'medicine']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $category === 'medicine' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Medicines
                    </a>
                    <a href="{{ route('inventory.index', ['category' => 'vaccine']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $category === 'vaccine' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Vaccines (Cold-Chain)
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                <input type="hidden" name="category" value="{{ $category }}">

                <div class="sm:col-span-10 relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items by brand, generic name, or item code..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div class="sm:col-span-2 flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-semibold transition">
                        Search
                    </button>
                    @if (request()->has('search'))
                        <a href="{{ route('inventory.index', ['category' => $category]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-medium transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Inventory Stock Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Item Code</th>
                            <th class="px-5 py-3.5">Item & Generic Name</th>
                            <th class="px-5 py-3.5">Category</th>
                            <th class="px-5 py-3.5">Active Stock</th>
                            <th class="px-5 py-3.5">Earliest Batch Expiry</th>
                            <th class="px-5 py-3.5">Cold Chain</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $item)
                            @php
                                $stock = $item->total_stock;
                                $isLow = $stock <= $item->minimum_stock_alert;
                                $earliestBatch = $item->batches->sortBy('expiration_date')->first();
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-4 font-mono text-xs font-bold text-slate-700">
                                    {{ $item->item_code }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900">{{ $item->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->generic_name ?? 'N/A' }} &bull; {{ $item->dosage_form }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->category === 'vaccine' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ ucfirst($item->category) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base font-extrabold {{ $isLow ? 'text-amber-600' : 'text-slate-900' }}">
                                            {{ number_format($stock) }}
                                        </span>
                                        <span class="text-xs text-slate-500">{{ $item->unit_of_measure }}</span>
                                    </div>
                                    @if ($isLow)
                                        <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.2 rounded mt-0.5">
                                            Low Stock (&le; {{ $item->minimum_stock_alert }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if ($earliestBatch)
                                        @php
                                            $daysLeft = $earliestBatch->days_until_expiration;
                                            $isNear = $daysLeft <= 90 && $daysLeft >= 0;
                                            $isExp = $daysLeft < 0;
                                        @endphp
                                        <div class="font-medium {{ $isExp ? 'text-rose-600 font-bold' : ($isNear ? 'text-amber-600 font-semibold' : 'text-slate-700') }}">
                                            {{ $earliestBatch->expiration_date->format('M d, Y') }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">
                                            Batch: {{ $earliestBatch->batch_number }} ({{ $earliestBatch->current_quantity }} left)
                                        </div>
                                    @else
                                        <span class="text-slate-400">No active batches</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if ($item->is_cold_chain)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707" />
                                            </svg>
                                            Cold Chain
                                        </span>
                                        <div class="text-[10px] text-slate-400 mt-0.5">{{ $item->storage_temperature_note ?? '2°C to 8°C' }}</div>
                                    @else
                                        <span class="text-slate-400">Standard Room Temp</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right space-x-1">
                                    <a href="{{ route('inventory.batches.receive', ['item_id' => $item->id]) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                        + Receive
                                    </a>
                                    <a href="{{ route('inventory.dispense', ['item_id' => $item->id]) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                        Dispense
                                    </a>
                                    <a href="{{ route('inventory.items.edit', $item) }}" class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                    No inventory items found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Stock Transactions Trail -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <h2 class="font-bold text-slate-800 text-sm">Recent Stock Movements (Audit Trail)</h2>
                <a href="{{ route('reports.inventory') }}" class="text-xs text-slate-500 hover:underline font-medium">Full Inventory Report</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-semibold text-slate-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Batch #</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Dispensed To / Remarks</th>
                            <th class="px-4 py-3">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentTransactions as $tx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $tx->transaction_date->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $tx->transaction_type === 'received' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ strtoupper($tx->transaction_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-800">
                                    {{ $tx->item ? $tx->item->name : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-500">
                                    {{ $tx->batch ? $tx->batch->batch_number : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 font-bold {{ $tx->transaction_type === 'received' ? 'text-blue-600' : 'text-emerald-700' }}">
                                    {{ $tx->quantity }} {{ $tx->item ? $tx->item->unit_of_measure : '' }}
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if ($tx->patient)
                                        <a href="{{ route('patients.show', $tx->patient) }}" class="text-emerald-700 hover:underline font-medium">
                                            {{ $tx->patient->full_name }}
                                        </a>
                                    @else
                                        {{ $tx->remarks ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-500">
                                    {{ $tx->user ? $tx->user->name : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-slate-400">
                                    No stock movements recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

