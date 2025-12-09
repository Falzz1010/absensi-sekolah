<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        @if(count($muridList) > 0)
            <x-filament::section class="mt-6">
                <x-slot name="heading">
                    Daftar Kehadiran ({{ count($muridList) }} Murid)
                </x-slot>

                <div class="space-y-2">
                    @foreach($muridList as $index => $murid)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex-1">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $murid['name'] }}
                                </span>
                            </div>
                            <div class="flex gap-1.5">
                                @foreach(['Hadir' => 'success', 'Sakit' => 'info', 'Izin' => 'warning', 'Alfa' => 'danger'] as $status => $color)
                                    <button
                                        type="button"
                                        wire:click="updateStatus({{ $murid['id'] }}, '{{ $status }}')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors
                                            @if($murid['status'] === $status)
                                                @if($color === 'success') bg-green-600 text-white
                                                @elseif($color === 'info') bg-blue-600 text-white
                                                @elseif($color === 'warning') bg-yellow-600 text-white
                                                @elseif($color === 'danger') bg-red-600 text-white
                                                @endif
                                            @else
                                                bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600
                                            @endif
                                        "
                                    >
                                        {{ $status }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end">
                    <x-filament::button type="submit">
                        <x-slot name="icon">
                            heroicon-o-check-circle
                        </x-slot>
                        Simpan Absensi
                    </x-filament::button>
                </div>
            </x-filament::section>
        @else
            <x-filament::section class="mt-6">
                <div class="text-center py-12 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="mt-4 text-sm">Pilih kelas untuk menampilkan daftar murid</p>
                </div>
            </x-filament::section>
        @endif
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
