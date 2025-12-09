<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
            <p class="text-base font-semibold">{{ $record->tanggal->format('d/m/Y') }}</p>
        </div>
        
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
            <p class="text-base font-semibold">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($record->status === 'Hadir') bg-green-100 text-green-800
                    @elseif($record->status === 'Sakit') bg-yellow-100 text-yellow-800
                    @elseif($record->status === 'Izin') bg-blue-100 text-blue-800
                    @elseif($record->status === 'Alfa') bg-red-100 text-red-800
                    @elseif($record->status === 'Terlambat') bg-orange-100 text-orange-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $record->status }}
                </span>
            </p>
        </div>
    </div>
    
    @if($record->check_in_time)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Waktu Check-in</p>
        <p class="text-base font-semibold">{{ \Carbon\Carbon::parse($record->check_in_time)->format('H:i') }}</p>
    </div>
    @endif
    
    @if($record->is_late)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Keterlambatan</p>
        <p class="text-base font-semibold text-orange-600">{{ $record->late_duration }} menit</p>
    </div>
    @endif
    
    @if($record->keterangan)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Keterangan</p>
        <p class="text-base">{{ $record->keterangan }}</p>
    </div>
    @endif
    
    @if($record->hasProof())
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Dokumen Bukti</p>
        <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <a href="{{ route('files.attendance-proof', ['path' => base64_encode($record->proof_document)]) }}" 
               target="_blank" 
               class="text-blue-600 hover:text-blue-800 underline">
                Lihat Dokumen
            </a>
        </div>
    </div>
    @endif
    
    @if($record->verification_status)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Verifikasi</p>
        <p class="text-base font-semibold">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                @if($record->verification_status === 'approved') bg-green-100 text-green-800
                @elseif($record->verification_status === 'rejected') bg-red-100 text-red-800
                @elseif($record->verification_status === 'pending') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800
                @endif">
                @if($record->verification_status === 'approved') Disetujui
                @elseif($record->verification_status === 'rejected') Ditolak
                @elseif($record->verification_status === 'pending') Menunggu Verifikasi
                @else {{ $record->verification_status }}
                @endif
            </span>
        </p>
    </div>
    @endif
    
    @if($record->verified_at)
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Diverifikasi Pada</p>
        <p class="text-base">{{ $record->verified_at->format('d/m/Y H:i') }}</p>
    </div>
    @endif
</div>
