<x-filament-panels::page>
    <div class="space-y-4 sm:space-y-6">
        {{-- Info Card --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3 sm:p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xs sm:text-sm font-medium text-blue-800 dark:text-blue-200">Informasi Penting</h3>
                    <p class="mt-1 text-xs sm:text-sm text-blue-700 dark:text-blue-300">
                        Gunakan form ini untuk mengajukan izin atau melaporkan sakit. Pastikan Anda melampirkan bukti pendukung yang valid (surat dokter untuk sakit, atau surat izin dari orang tua).
                    </p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <form wire:submit="submit">
                {{ $this->form }}

                <div class="mt-4 sm:mt-6 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                    <x-filament::button
                        type="button"
                        color="gray"
                        wire:click="$set('data', [])"
                        class="w-full sm:w-auto min-h-[44px]"
                    >
                        Reset
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        class="w-full sm:w-auto min-h-[44px]"
                    >
                        Kirim Pengajuan
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Guidelines Card --}}
        <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4">
            <h3 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white mb-2 sm:mb-3">Panduan Pengajuan</h3>
            <ul class="space-y-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                <li class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="break-words"><strong>Sakit:</strong> Lampirkan foto surat keterangan dokter yang jelas dan terbaca</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="break-words"><strong>Izin:</strong> Lampirkan foto surat izin dari orang tua/wali yang sudah ditandatangani</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="break-words">Format file yang diterima: JPEG, PNG, atau PDF (maksimal 5MB)</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-500 mr-1.5 sm:mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="break-words">Pengajuan akan diverifikasi oleh admin/wali kelas dalam 1x24 jam</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-500 mr-1.5 sm:mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span class="break-words">Pastikan bukti yang dilampirkan asli dan tidak dimanipulasi</span>
                </li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
