@props(['title', 'subtitle' => null])

<div class="border-b-2 border-slate-900 pb-4 mb-6">
    <!-- Official Government Header Block -->
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <!-- Official Health Center Seal -->
            <div class="shrink-0">
                <img src="{{ asset('images/logo.png') }}" 
                     alt="Barangay Tacunan Health Center Seal" 
                     class="w-16 h-16 object-contain"
                     onerror="this.onerror=null; this.src='{{ asset('images/logo.jpg') }}';">
            </div>

            <!-- Government Letterhead Text -->
            <div class="text-left space-y-0.5">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-600 print:text-black">
                    Republic of the Philippines &bull; City Government of Davao
                </div>
                <div class="text-xs font-bold text-slate-800 uppercase tracking-wide print:text-black">
                    City Health Office &bull; District Health Services
                </div>
                <h1 class="text-sm font-extrabold text-slate-950 uppercase tracking-wide print:text-black">
                    Barangay Tacunan Health Center
                </h1>
                <div class="text-[10px] text-slate-500 font-medium print:text-slate-700">
                    Sitio Tacunan Proper, Barangay Tacunan, Tugbok District, Davao City 8000
                </div>
            </div>
        </div>

        <!-- Document Control Metadata -->
        <div class="text-right text-[10px] text-slate-500 font-mono space-y-0.5 print:text-black shrink-0">
            <div><span class="font-bold text-slate-700 print:text-black">Doc Code:</span> BHC-TAC-RPT</div>
            <div><span class="font-bold text-slate-700 print:text-black">Generated:</span> {{ now()->format('M d, Y g:i A') }}</div>
            <div><span class="font-bold text-slate-700 print:text-black">Prepared By:</span> {{ Auth::user()->name ?? 'Health Staff' }}</div>
            <div class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 font-sans font-bold text-[9px] uppercase tracking-wider text-slate-700 border border-slate-300 print:border-black print:bg-transparent">
                Official Clinical Record
            </div>
        </div>
    </div>

    <!-- Report Title Banner -->
    <div class="mt-4 pt-3 border-t border-slate-200 text-center">
        <h2 class="text-base font-extrabold uppercase tracking-wide text-slate-900 print:text-black">
            {{ $title }}
        </h2>
        @if ($subtitle)
            <p class="text-xs font-semibold text-slate-600 mt-0.5 print:text-slate-800">
                {{ $subtitle }}
            </p>
        @endif
    </div>
</div>

