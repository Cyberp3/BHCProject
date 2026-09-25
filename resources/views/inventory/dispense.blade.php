<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Back to Inventory
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-200 bg-slate-50/50">
                <h1 class="text-xl font-bold text-slate-900">Dispense Medicine / Vaccine</h1>
                <p class="text-xs text-slate-500 mt-1">Dispense supplies to a patient or clinic health program using FIFO allocation.</p>
            </div>

            <!-- FIFO System Banner -->
            <div class="p-4 bg-emerald-50/70 border-b border-emerald-100 flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xs text-emerald-900">
                    <strong>FIFO Principle Active:</strong> The system will automatically allocate stock from your earliest-expiring active batches first to prevent expiration wastage.
                </div>
            </div>

            <form method="POST" action="{{ route('inventory.dispense.process') }}" class="p-6 space-y-6">
                @csrf

                <!-- Select Item -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Item to Dispense <span class="text-rose-500">*</span></label>
                    <select name="inventory_item_id" required class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-medium">
                        <option value="">-- Select Medicine / Vaccine --</option>
                        @foreach ($items as $item)
                            @php
                                $stock = $item->total_stock;
                            @endphp
                            <option value="{{ $item->id }}" {{ old('inventory_item_id', $selectedItemId) == $item->id ? 'selected' : '' }} {{ $stock <= 0 ? 'disabled' : '' }}>
                                {{ $item->name }} ({{ $item->item_code }}) - Available: {{ $stock }} {{ $item->unit_of_measure }} {{ $stock <= 0 ? '[OUT OF STOCK]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Patient (Optional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Recipient Patient (Optional)</label>
                    <select name="patient_id" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">-- General Clinic / Program Use (No Specific Patient) --</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatientId) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->last_name }}, {{ $patient->first_name }} ({{ $patient->patient_control_number }}) - {{ $patient->purok->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity to Dispense <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="w-full sm:w-48 text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-bold text-base">
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dispensation Reason / Clinical Notes</label>
                    <input type="text" name="remarks" value="{{ old('remarks') }}" placeholder="e.g. 5-day course prescribed by Nurse, routine infant immunization, oral rehydration therapy" class="w-full text-sm rounded-lg border-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('inventory.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                        Confirm & Deduct Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

