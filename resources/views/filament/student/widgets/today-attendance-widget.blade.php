<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Kehadiran Hari Ini
        </x-slot>
        
        @php
            $attendance = $this->getTodayAttendance();
            $murid = $this->getMurid();
        @endphp
        
        <div class="space-y-4">
            @if($attendance)
                {{-- Attendance exists --}}
                <div class="rounded-lg border border-gray-200 bg-white p-4 sm:p-6 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 sm:gap-3">
                                @if($attendance->status === 'Hadir' && !$attendance->is_late)
                                    <div class="rounded-full bg-success-100 p-1.5 sm:p-2 dark:bg-success-900 flex-shrink-0">
                                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-success-600 dark:text-success-400">Hadir</h3>
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</p>
                                    </div>
                                @elseif($attendance->is_late)
                                    <div class="rounded-full bg-warning-100 p-1.5 sm:p-2 dark:bg-warning-900 flex-shrink-0">
                                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-warning-600 dark:text-warning-400">Terlambat</h3>
                                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                            {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                            @if($attendance->late_duration)
                                                <span class="ml-1 sm:ml-2 inline-block rounded bg-warning-100 px-1.5 sm:px-2 py-0.5 sm:py-1 text-xs font-medium text-warning-800 dark:bg-warning-900 dark:text-warning-200">
                                                    {{ $attendance->late_duration }} mnt
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                @elseif($attendance->status === 'Sakit')
                                    <div class="rounded-full bg-info-100 p-2 dark:bg-info-900">
                                        <svg class="h-6 w-6 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-info-600 dark:text-info-400">Sakit</h3>
                                        @if($attendance->verification_status)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Status: 
                                                @if($attendance->verification_status === 'pending')
                                                    <span class="text-warning-600">Menunggu Verifikasi</span>
                                                @elseif($attendance->verification_status === 'approved')
                                                    <span class="text-success-600">Disetujui</span>
                                                @else
                                                    <span class="text-danger-600">Ditolak</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                @elseif($attendance->status === 'Izin')
                                    <div class="rounded-full bg-gray-100 p-2 dark:bg-gray-700">
                                        <svg class="h-6 w-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400">Izin</h3>
                                        @if($attendance->verification_status)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Status: 
                                                @if($attendance->verification_status === 'pending')
                                                    <span class="text-warning-600">Menunggu Verifikasi</span>
                                                @elseif($attendance->verification_status === 'approved')
                                                    <span class="text-success-600">Disetujui</span>
                                                @else
                                                    <span class="text-danger-600">Ditolak</span>
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <div class="rounded-full bg-danger-100 p-2 dark:bg-danger-900">
                                        <svg class="h-6 w-6 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-danger-600 dark:text-danger-400">{{ $attendance->status }}</h3>
                                    </div>
                                @endif
                            </div>
                            
                            @if($attendance->keterangan)
                                <div class="mt-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium">Keterangan:</span> {{ $attendance->keterangan }}
                                    </p>
                                </div>
                            @endif
                            
                            @if($attendance->proof_document)
                                <div class="mt-3">
                                    <a href="{{ route('files.attendance-proof', ['path' => base64_encode($attendance->proof_document)]) }}" 
                                       target="_blank"
                                       class="inline-flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        Lihat Dokumen Bukti
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                {{-- No attendance yet --}}
                <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 sm:p-8 text-center dark:border-gray-600 dark:bg-gray-800">
                    <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100">Belum Absen</h3>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Anda belum melakukan absensi hari ini</p>
                    <div class="mt-4 sm:mt-6">
                        <a href="/student/qr-scan-page" 
                           class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 min-h-[44px] text-xs sm:text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 touch-manipulation">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            Scan QR Code Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
