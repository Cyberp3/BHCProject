<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Inventory
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Edit Catalog Item</h1>
                <p class="text-xs text-slate-500 mt-1">Update specifications for {{ $item->name }} ({{ $item->item_code }}).</p>
            </div>

            <form method="POST" action="{{ route('inventory.items.update', $item) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Item Code <span class="text-rose-500">*</span></label>
                        <input type="text" name="item_code" value="{{ old('item_code', $item->item_code) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="medicine" {{ old('category', $item->category) == 'medicine' ? 'selected' : '' }}>Medicine</option>
                            <option value="vaccine" {{ old('category', $item->category) == 'vaccine' ? 'selected' : '' }}>Vaccine</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Brand / Item Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $item->name) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Generic Name</label>
                        <input type="text" name="generic_name" value="{{ old('generic_name', $item->generic_name) }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dosage Form</label>
                        <input type="text" name="dosage_form" value="{{ old('dosage_form', $item->dosage_form) }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Unit of Measure <span class="text-rose-500">*</span></label>
                        <input type="text" name="unit_of_measure" value="{{ old('unit_of_measure', $item->unit_of_measure) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Minimum Alert Threshold <span class="text-rose-500">*</span></label>
                        <input type="number" min="1" name="minimum_stock_alert" value="{{ old('minimum_stock_alert', $item->minimum_stock_alert) }}" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <hr class="border-slate-100">

                <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_cold_chain" value="1" {{ old('is_cold_chain', $item->is_cold_chain) ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-bold text-blue-900">Requires Cold Chain Storage (Vaccines / Biologics)</span>
                    </label>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Storage Temperature / Handling Note</label>
                        <input type="text" name="storage_temperature_note" value="{{ old('storage_temperature_note', $item->storage_temperature_note) }}" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Item Description / Indications</label>
                    <textarea name="description" rows="3" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $item->description) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

