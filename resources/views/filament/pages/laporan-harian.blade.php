<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan
            </x-slot>
            
            {{ $this->form }}
        </x-filament::section>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Absensi -->
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $laporanData['total'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Total Absensi
                    </div>
                </div>
            </x-filament::card>

            <!-- Hadir -->
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">
                        {{ $laporanData['hadir'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Hadir
                    </div>
                    <div class="text-xs text-success-600 mt-1">
                        {{ $laporanData['persentase_hadir'] ?? 0 }}%
                    </div>
                </div>
            </x-filament::card>

            <!-- Sakit -->
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-info-600">
                        {{ $laporanData['sakit'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Sakit
                    </div>
                </div>
            </x-filament::card>

            <!-- Izin -->
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-warning-600">
                        {{ $laporanData['izin'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Izin
                    </div>
                </div>
            </x-filament::card>

            <!-- Alfa -->
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-danger-600">
                        {{ $laporanData['alfa'] ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Alfa
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Detail Per Kelas -->
        @if(!empty($laporanData['detail']))
        <x-filament::section>
            <x-slot name="heading">
                Detail Per Kelas
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">Kelas</th>
                            <th class="px-6 py-3 text-center">Total</th>
                            <th class="px-6 py-3 text-center">Hadir</th>
                            <th class="px-6 py-3 text-center">Sakit</th>
                            <th class="px-6 py-3 text-center">Izin</th>
                            <th class="px-6 py-3 text-center">Alfa</th>
                            <th class="px-6 py-3 text-center">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporanData['detail'] as $detail)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-6 py-4 font-medium">{{ $detail['kelas'] }}</td>
                            <td class="px-6 py-4 text-center">{{ $detail['total'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-success-600 font-semibold">{{ $detail['hadir'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-info-600">{{ $detail['sakit'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-warning-600">{{ $detail['izin'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-danger-600">{{ $detail['alfa'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $persentase = $detail['total'] > 0 ? round(($detail['hadir'] / $detail['total']) * 100, 1) : 0;
                                @endphp
                                <span class="font-semibold {{ $persentase >= 80 ? 'text-success-600' : ($persentase >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ $persentase }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
        @else
        <x-filament::section>
            <div class="text-center py-12">
                <div class="text-gray-400 text-lg">
                    Tidak ada data absensi untuk tanggal yang dipilih
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
