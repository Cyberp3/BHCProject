<div class="mt-8 pt-6 border-t border-slate-300 print:border-black print-avoid-break space-y-6">
    <!-- Official Certification Statement -->
    <div class="text-[11px] text-slate-600 print:text-black italic leading-relaxed text-center">
        "I hereby certify that the information, patient records, and inventory metrics stated above are true, complete, and compiled from the official registries and clinical logbooks of the Barangay Tacunan Health Center."
    </div>

    <!-- Signatory Blocks -->
    <div class="grid grid-cols-2 gap-8 pt-4">
        <!-- Attending Healthcare Worker -->
        <div class="text-left space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider print:text-black">
                Prepared &amp; Certified Correct By:
            </span>
            <div class="pt-8">
                <div class="border-b border-slate-900 w-64 print:w-56"></div>
                <div class="font-bold text-xs text-slate-900 uppercase mt-1 print:text-black">
                    {{ Auth::user()->name ?? 'Healthcare Worker' }}
                </div>
                <div class="text-[11px] text-slate-600 print:text-black">
                    Attending {{ ucfirst(Auth::user()->role ?? 'Staff') }} / BHC In-Charge
                </div>
                <div class="text-[10px] text-slate-400 print:text-slate-600">
                    License / Employee ID: _____________________
                </div>
            </div>
        </div>

        <!-- Medical Officer / Supervising Physician -->
        <div class="text-right space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider print:text-black">
                Noted &amp; Approved By:
            </span>
            <div class="pt-8 flex flex-col items-end">
                <div class="border-b border-slate-900 w-64 print:w-56"></div>
                <div class="font-bold text-xs text-slate-900 uppercase mt-1 print:text-black">
                    Medical Officer / Physician In-Charge
                </div>
                <div class="text-[11px] text-slate-600 print:text-black">
                    City Health Office &bull; Tugbok District
                </div>
                <div class="text-[10px] text-slate-400 print:text-slate-600">
                    Date Signed: ________________________
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom System Notice -->
    <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 print:text-slate-700 font-mono">
        <span>TacunaCare &bull; Barangay Tacunan Health Center Management System</span>
        <span>CONFIDENTIAL HEALTH INFORMATION &bull; FOR OFFICIAL GOVERNMENT USE ONLY</span>
    </div>
</div>

