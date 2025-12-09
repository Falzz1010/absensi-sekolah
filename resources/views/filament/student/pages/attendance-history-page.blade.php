<x-filament-panels::page>
    <div class="space-y-4 sm:space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-2 sm:mb-4">Riwayat Kehadiran 30 Hari Terakhir</h2>
            <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mb-3 sm:mb-4">
                Menampilkan data kehadiran Anda dalam 30 hari terakhir. Gunakan filter untuk menyaring data berdasarkan status atau rentang tanggal.
            </p>
            
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
