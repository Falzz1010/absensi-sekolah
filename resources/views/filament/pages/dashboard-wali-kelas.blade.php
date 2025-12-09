<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Periode
            </x-slot>
            
            {{ $this->form }}
        </x-filament::section>

        <!-- Informasi Kelas -->
        @if(!empty($kelasData))
        <x-filament::section>
            <x-slot name="heading">
                Informasi Kelas
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-primary-600">{{ $kelasData['nama'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Nama Kelas</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-info-600">{{ $kelasData['tingkat'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Tingkat</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-warning-600">{{ $kelasData['jurusan'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Jurusan</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-success-600">{{ $kelasData['jumlah_murid'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Jumlah Murid</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600">{{ $kelasData['kapasitas'] }}</div>
                    <div class="text-sm text-gray-500 mt-1">Kapasitas</div>
                </div>
            </div>
        </x-filament::section>
        @endif

        <!-- Statistik Bulanan -->
        @if(!empty($statistik))
        <x-filament::section>
            <x-slot name="heading">
                Statistik Bulan Ini
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $statistik['total_hari_kerja'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Hari Kerja
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success-600">
                            {{ $statistik['total_hadir'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Hadir
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-info-600">
                            {{ $statistik['total_sakit'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Sakit
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-warning-600">
                            {{ $statistik['total_izin'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Izin
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-danger-600">
                            {{ $statistik['total_alfa'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Alfa
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">
                            {{ $statistik['rata_rata_kehadiran'] }}%
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Rata-rata Kehadiran
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $statistik['total_murid'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Murid
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $statistik['total_absensi'] }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total Absensi
                        </div>
                    </div>
                </x-filament::card>
            </div>
        </x-filament::section>
        @endif

        <!-- Rekap Per Murid -->
        @if(!empty($rekapBulanan))
        <x-filament::section>
            <x-slot name="heading">
                Rekap Kehadiran Per Murid
            </x-slot>

            <x-slot name="headerEnd">
                <div class="flex gap-2">
                    <x-filament::button wire:click="exportExcel" color="success" size="sm">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1" />
                        Export Excel
                    </x-filament::button>
                    <x-filament::button wire:click="exportPdf" color="danger" size="sm">
                        <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-1" />
                        Export PDF
                    </x-filament::button>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">Nama Murid</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 text-center">Hadir</th>
                            <th class="px-6 py-3 text-center">Sakit</th>
                            <th class="px-6 py-3 text-center">Izin</th>
                            <th class="px-6 py-3 text-center">Alfa</th>
                            <th class="px-6 py-3 text-center">Total</th>
                            <th class="px-6 py-3 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapBulanan as $index => $rekap)
                        <tr class="border-b dark:border-gray-700 {{ $index % 2 == 0 ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }}">
                            <td class="px-6 py-4">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium">{{ $rekap['nama'] }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $rekap['email'] }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-success-600 font-semibold">{{ $rekap['hadir'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-info-600">{{ $rekap['sakit'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-warning-600">{{ $rekap['izin'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-danger-600">{{ $rekap['alfa'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $rekap['total'] }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-lg {{ $rekap['persentase'] >= 80 ? 'text-success-600' : ($rekap['persentase'] >= 60 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ $rekap['persentase'] }}%
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
                    @if(empty($kelasData))
                        Anda belum ditugaskan sebagai wali kelas
                    @else
                        Tidak ada data absensi untuk periode yang dipilih
                    @endif
                </div>
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
