<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Inventory
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Receive Stock Delivery (Batch / Lot Intake)</h1>
                <p class="text-xs text-slate-500 mt-1">Record a newly delivered supply shipment into inventory with expiration and lot tracking.</p>
            </div>

            <form method="POST" action="{{ route('inventory.batches.store') }}" class="p-6 space-y-6">
                @csrf

                <!-- Select Item -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Medicine or Vaccine <span class="text-rose-500">*</span></label>
                    <select name="inventory_item_id" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- Choose Item from Catalog --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ old('inventory_item_id', $selectedItemId) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->item_code }}) - Unit: {{ $item->unit_of_measure }} {{ $item->is_cold_chain ? '[Cold-Chain]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Batch Number & Quantity -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Batch / Lot Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="e.g. LOT-2026-X99" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity Received <span class="text-rose-500">*</span></label>
                        <input type="number" min="1" name="quantity_received" value="{{ old('quantity_received') }}" placeholder="e.g. 100" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold">
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Date Received <span class="text-rose-500">*</span></label>
                        <input type="date" name="date_received" value="{{ old('date_received', $today) }}" max="{{ date('Y-m-d') }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Expiration Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="expiration_date" value="{{ old('expiration_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <span class="text-[11px] text-slate-400">Used for automatic FIFO dispensation.</span>
                    </div>
                </div>

                <!-- Supplier Source -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Supplier / Allocation Source</label>
                    <input type="text" name="supplier_or_source" value="{{ old('supplier_or_source', 'City Health Office (CHO) Davao') }}" placeholder="e.g. CHO Davao, DOH Central, Barangay Purchase" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Intake Remarks / Storage Location</label>
                    <textarea name="remarks" rows="2" placeholder="e.g. Stored in Refrigerator 1, Shelf B..." class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('remarks') }}</textarea>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Record Received Batch
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

