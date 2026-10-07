<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumb & Top Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                <a href="{{ route('inventory.index') }}" class="hover:text-slate-800 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Inventory Directory
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('inventory.batches.receive', ['item_id' => $item->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Receive New Delivery Batch
                </a>
                <a href="{{ route('inventory.dispense', ['item_id' => $item->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Dispense Item (FIFO)
                </a>
                <a href="{{ route('inventory.items.edit', $item) }}" class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                    Edit Catalog Details
                </a>
            </div>
        </div>

        <!-- Product Overview Header Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs p-6 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $item->item_code }}
                        </span>
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $item->category === 'vaccine' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            {{ ucfirst($item->category) }}
                        </span>
                        @if ($item->is_cold_chain)
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707" />
                                </svg>
                                Cold Chain ({{ $item->storage_temperature_note ?? '2°C to 8°C' }})
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $item->name }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">
                        {{ $item->generic_name ?? 'N/A' }} &bull; Dosage Form: <strong class="text-slate-700">{{ $item->dosage_form ?: 'Standard' }}</strong> &bull; Unit: <strong class="text-slate-700">{{ $item->unit_of_measure }}</strong>
                    </p>
                </div>

                <!-- Stock Counter Card -->
                <div class="flex flex-wrap items-center gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Dispensable Stock (Unexpired)</div>
                        <div class="text-2xl font-extrabold {{ $item->dispensable_stock <= $item->minimum_stock_alert ? 'text-amber-600' : 'text-slate-900' }}">
                            {{ number_format($item->dispensable_stock) }} <span class="text-xs font-normal text-slate-500">{{ $item->unit_of_measure }}</span>
                        </div>
                    </div>
                    @if ($item->dispensable_stock <= $item->minimum_stock_alert)
                        <div class="px-2.5 py-1 rounded bg-amber-100 text-amber-800 text-xs font-bold border border-amber-200">
                            Low Stock (Reorder &le; {{ $item->minimum_stock_alert }})
                        </div>
                    @endif
                    @if ($item->expired_stock > 0)
                        <div class="px-2.5 py-1 rounded bg-rose-100 text-rose-800 text-xs font-bold border border-rose-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            {{ number_format($item->expired_stock) }} {{ $item->unit_of_measure }} Quarantined (Expired)
                        </div>
                    @endif
                </div>
            </div>

            @if ($item->description)
                <p class="text-xs text-slate-600 pt-2 border-t border-slate-100">{{ $item->description }}</p>
            @endif
        </div>

        <!-- Multi-Batch FIFO / FEFO Operational Principle Card -->
        <div class="bg-blue-50/70 rounded-xl border border-blue-200 p-5 space-y-3">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">
                    i
                </div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-blue-900">
                    First-Expired, First-Out (FEFO) Multi-Batch Allocation &amp; Expiry Safety Guard
                </h2>
            </div>
            <p class="text-xs text-blue-800 leading-relaxed">
                When new shipments arrive, each delivery is tracked as a <strong>distinct batch</strong> with its own batch number and expiration date so new stock never overwrites or merges with older batches. 
                When healthcare staff dispense this item, TacunaCare automatically consumes from the <strong>earliest-expiring valid batch first</strong>. 
                Any batch that reaches its expiration date is <strong>strictly blocked and quarantined</strong> from dispensation to guarantee patient safety.
            </p>

            <!-- Visual Queue of Active Batches -->
            @if ($activeBatches->count() > 0)
                <div class="pt-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-900 block mb-2">Current Dispensing Order (FEFO Queue):</span>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach ($activeBatches as $index => $batch)
                            <div class="flex items-center gap-2">
                                <div class="px-3 py-2 rounded-lg border text-xs font-medium {{ $index === 0 ? 'bg-white border-blue-300 shadow-2xs ring-2 ring-blue-400/40 text-blue-950 font-bold' : 'bg-white/70 border-slate-200 text-slate-700' }}">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                            #{{ $index + 1 }} {{ $index === 0 ? 'Dispensing Now' : 'Queued' }}
                                        </span>
                                        <span class="font-mono">{{ $batch->batch_number }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between gap-4">
                                        <span>Expires: <strong class="{{ $batch->is_expired ? 'text-rose-600' : 'text-slate-800' }}">{{ $batch->expiration_date->format('M d, Y') }}</strong></span>
                                        <span><strong>{{ number_format($batch->current_quantity) }}</strong> left</span>
                                    </div>
                                </div>
                                @if (! $loop->last)
                                    <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Active Batches Breakdown Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-slate-800 text-sm">Valid Active Batches (Earliest Expiry at Top)</h2>
                    <p class="text-xs text-slate-500">Only unexpired active batches are eligible for clinic dispensation.</p>
                </div>
                <a href="{{ route('inventory.batches.receive', ['item_id' => $item->id]) }}" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-semibold border border-blue-200 transition">
                    + Receive Additional Batch
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">FEFO Order</th>
                            <th class="px-5 py-3">Batch / Lot Number</th>
                            <th class="px-5 py-3">Expiration Date</th>
                            <th class="px-5 py-3">Days Until Expiry</th>
                            <th class="px-5 py-3">Current Stock</th>
                            <th class="px-5 py-3">Received Date</th>
                            <th class="px-5 py-3">Supplier / Source</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($activeBatches as $index => $batch)
                            @php
                                $days = $batch->days_until_expiration;
                                $isNear = $days <= 90 && $days >= 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $index === 0 ? 'bg-blue-50/20' : '' }}">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $index === 0 ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                        {{ $index === 0 ? '#1 Priority (FEFO)' : '#'.($index + 1).' Next' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-xs font-bold text-slate-900">
                                    {{ $batch->batch_number }}
                                </td>
                                <td class="px-5 py-3.5 text-xs font-semibold {{ $isNear ? 'text-amber-600 font-bold' : 'text-slate-800' }}">
                                    {{ $batch->expiration_date->format('M d, Y') }}
                                    @if ($index === 0)
                                        <span class="block text-[10px] font-normal text-blue-600">(Earliest Expiration)</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-xs">
                                    @if ($isNear)
                                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold text-[11px]">{{ $days }} days left</span>
                                    @else
                                        <span class="text-slate-600">{{ $days }} days left</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-slate-900 text-sm">{{ number_format($batch->current_quantity) }}</span>
                                        <span class="text-xs text-slate-400">/ {{ number_format($batch->quantity_received) }} {{ $item->unit_of_measure }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500">
                                    {{ $batch->date_received->format('M d, Y') }}
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-600">
                                    {{ $batch->supplier_or_source ?? 'City Health Office (CHO) Davao' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-xs">
                                    No valid unexpired stock batches currently available for this item.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quarantined / Expired Batches Section -->
        @if ($expiredBatches->count() > 0)
            <div class="bg-rose-50/60 rounded-xl border border-rose-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-rose-200 bg-rose-100/60 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                        <h2 class="font-bold text-rose-900 text-xs uppercase tracking-wider">Quarantined / Expired Batches (Blocked from Dispensation)</h2>
                    </div>
                    <span class="text-xs font-bold text-rose-700 bg-white/90 px-2.5 py-0.5 rounded border border-rose-300">
                        {{ $expiredBatches->count() }} {{ Str::plural('Batch', $expiredBatches->count()) }} &bull; {{ number_format($expiredBatches->sum('current_quantity')) }} {{ $item->unit_of_measure }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-rose-950">
                        <thead class="bg-rose-100/40 border-b border-rose-200 text-rose-700 uppercase tracking-wider font-semibold">
                            <tr>
                                <th class="px-5 py-2.5">Batch / Lot Number</th>
                                <th class="px-5 py-2.5">Expiration Date</th>
                                <th class="px-5 py-2.5">Expired Since</th>
                                <th class="px-5 py-2.5">Quarantined Quantity</th>
                                <th class="px-5 py-2.5">Supplier / Source</th>
                                <th class="px-5 py-2.5">Safety Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-100 bg-white/70">
                            @foreach ($expiredBatches as $batch)
                                <tr>
                                    <td class="px-5 py-3 font-mono font-bold text-rose-900">{{ $batch->batch_number }}</td>
                                    <td class="px-5 py-3 font-semibold text-rose-700">{{ $batch->expiration_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-3 text-rose-600">{{ abs($batch->days_until_expiration) }} days ago</td>
                                    <td class="px-5 py-3 font-extrabold text-rose-900">{{ number_format($batch->current_quantity) }} {{ $item->unit_of_measure }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $batch->supplier_or_source ?? 'City Health Office (CHO) Davao' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                            BLOCKED / QUARANTINED
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Depleted Batches Archive -->
        @if ($depletedBatches->count() > 0)
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-200 bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Depleted Batches (Stock Exhausted)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-500">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-400">
                            <tr>
                                <th class="px-5 py-2.5">Batch Number</th>
                                <th class="px-5 py-2.5">Expiration Date</th>
                                <th class="px-5 py-2.5">Received Qty</th>
                                <th class="px-5 py-2.5">Current Qty</th>
                                <th class="px-5 py-2.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($depletedBatches as $batch)
                                <tr>
                                    <td class="px-5 py-2.5 font-mono">{{ $batch->batch_number }}</td>
                                    <td class="px-5 py-2.5">{{ $batch->expiration_date->format('M d, Y') }}</td>
                                    <td class="px-5 py-2.5">{{ number_format($batch->quantity_received) }}</td>
                                    <td class="px-5 py-2.5 font-bold">{{ number_format($batch->current_quantity) }}</td>
                                    <td class="px-5 py-2.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">
                                            {{ ucfirst($batch->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Recent Transaction History for this Item -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
                <h2 class="font-bold text-slate-800 text-sm">Stock Movement History (Intake &amp; Dispensation Trail)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Movement Type</th>
                            <th class="px-5 py-3">Batch / Lot</th>
                            <th class="px-5 py-3">Quantity</th>
                            <th class="px-5 py-3">Patient / Destination</th>
                            <th class="px-5 py-3">Staff</th>
                            <th class="px-5 py-3">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($item->transactions->take(15) as $trx)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3 text-slate-500">{{ $trx->transaction_date->format('M d, Y') }}</td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $trx->transaction_type === 'received' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ $trx->transaction_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 font-mono text-[11px]">{{ $trx->batch?->batch_number ?? 'N/A' }}</td>
                                <td class="px-5 py-3 font-bold {{ $trx->transaction_type === 'received' ? 'text-blue-700' : 'text-slate-800' }}">
                                    {{ $trx->transaction_type === 'received' ? '+' : '-' }}{{ number_format($trx->quantity) }}
                                </td>
                                <td class="px-5 py-3">
                                    @if ($trx->patient)
                                        <a href="{{ route('patients.show', $trx->patient) }}" class="text-blue-600 hover:underline">
                                            {{ $trx->patient->full_name }}
                                        </a>
                                    @else
                                        <span class="text-slate-400">Clinic Stock</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-500">{{ $trx->user?->name ?? 'System' }}</td>
                                <td class="px-5 py-3 text-slate-500">{{ $trx->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-6 text-center text-slate-400">
                                    No stock movements recorded yet for this item.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
