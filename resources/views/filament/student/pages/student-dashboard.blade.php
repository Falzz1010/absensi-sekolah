<x-filament-panels::page>
    {{-- Quick Action Buttons --}}
    <div class="mb-4 sm:mb-6 grid grid-cols-1 gap-3 sm:gap-4 sm:grid-cols-2">
        {{-- QR Scan Button --}}
        <a href="/student/qr-scan-page" 
           class="flex items-center justify-center gap-2 sm:gap-3 rounded-lg bg-primary-600 px-4 sm:px-6 py-4 min-h-[56px] text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 touch-manipulation">
            <svg class="h-5 w-5 sm:h-6 sm:w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
            <span class="text-base sm:text-lg font-semibold">Scan QR Code</span>
        </a>
        
        {{-- Submit Absence Button --}}
        <a href="/student/absence-submission-page" 
           class="flex items-center justify-center gap-2 sm:gap-3 rounded-lg px-4 sm:px-6 py-4 min-h-[56px] shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 touch-manipulation"
           style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;"
           onmouseover="this.style.background='linear-gradient(135deg, #059669 0%, #047857 100%)'"
           onmouseout="this.style.background='linear-gradient(135deg, #10b981 0%, #059669 100%)'">
            <svg class="h-5 w-5 sm:h-6 sm:w-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-base sm:text-lg font-semibold">Ajukan Izin/Sakit</span>
        </a>
    </div>
    
    {{-- Widgets --}}
    <div class="space-y-6">
        @foreach ($this->getWidgets() as $widget)
            @livewire($widget)
        @endforeach
    </div>
</x-filament-panels::page>
