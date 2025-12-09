<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">
                        Informasi Penting
                    </h3>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        Absensi manual digunakan sebagai verifikasi tambahan selain QR Scan. 
                        Anda harus melakukan <strong>kedua metode</strong> (QR Scan + Absensi Manual) 
                        untuk melengkapi absensi Anda.
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Time Display -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-center space-y-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Waktu Saat Ini</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white tabular-nums" id="current-time">
                        {{ now()->format('H:i:s') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        {{ now()->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Klik tombol di bawah untuk konfirmasi kehadiran Anda.<br>
                        Waktu akan otomatis tercatat saat Anda klik tombol.
                    </p>
                    
                    <button 
                        wire:click="confirmAttendance"
                        type="button"
                        class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Konfirmasi Kehadiran Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Check -->
        @php
            $user = Auth::user();
            $murid = \App\Models\Murid::where('user_id', $user->id)->first();
            $todayAbsensi = null;
            if ($murid) {
                $todayAbsensi = \App\Models\Absensi::where('murid_id', $murid->id)
                    ->whereDate('tanggal', now()->toDateString())
                    ->first();
            }
        @endphp

        @if($todayAbsensi)
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">
                Status Absensi Hari Ini
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="flex items-center space-x-2">
                    @if($todayAbsensi->qr_scan_done)
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-green-700 dark:text-green-400 font-medium">QR Scan: Sudah</span>
                    @else
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">QR Scan: Belum</span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    @if($todayAbsensi->manual_checkin_done)
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-green-700 dark:text-green-400 font-medium">Manual: Sudah</span>
                    @else
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Manual: Belum</span>
                    @endif
                </div>
                <div class="flex items-center space-x-2">
                    @if($todayAbsensi->is_complete)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✓ Lengkap
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                            ⚠ Belum Lengkap
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Tips -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                💡 Tips
            </h4>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-disc list-inside">
                <li>Cukup klik tombol "Konfirmasi Kehadiran Sekarang" untuk absen manual</li>
                <li>Waktu akan otomatis tercatat sesuai waktu Anda klik tombol</li>
                <li>Jika terlambat, sistem akan otomatis mendeteksi dan mencatat durasi keterlambatan</li>
                <li>Anda hanya dapat melakukan konfirmasi manual sekali per hari</li>
                <li>Jangan lupa untuk melakukan QR Scan juga agar absensi Anda lengkap</li>
            </ul>
        </div>
    </div>

    <script>
        // Update clock every second
        setInterval(function() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                timeElement.textContent = `${hours}:${minutes}:${seconds}`;
            }
        }, 1000);
    </script>
</x-filament-panels::page>
