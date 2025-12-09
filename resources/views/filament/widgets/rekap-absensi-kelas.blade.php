<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Rekap Kehadiran Hari Ini per Kelas
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3">Kelas</th>
                        <th scope="col" class="px-6 py-3 text-center">Hadir</th>
                        <th scope="col" class="px-6 py-3 text-center">Sakit</th>
                        <th scope="col" class="px-6 py-3 text-center">Izin</th>
                        <th scope="col" class="px-6 py-3 text-center">Alfa</th>
                        <th scope="col" class="px-6 py-3 text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->getRekapData() as $kelas => $data)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium">{{ $kelas }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $data['Hadir'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $data['Sakit'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ $data['Izin'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $data['Alfa'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $data['Total'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data absensi hari ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
