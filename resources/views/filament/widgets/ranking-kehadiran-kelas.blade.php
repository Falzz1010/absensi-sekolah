<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🏆 Ranking Kehadiran Kelas Bulan Ini
        </x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-semibold">Rank</th>
                        <th class="px-4 py-3 text-left font-semibold">Kelas</th>
                        <th class="px-4 py-3 text-center font-semibold">Hadir</th>
                        <th class="px-4 py-3 text-center font-semibold">Total</th>
                        <th class="px-4 py-3 text-center font-semibold">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $rankings = $this->getRankingData();
                    @endphp
                    
                    @forelse($rankings as $index => $data)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                @if($index === 0)
                                    <span class="text-xl">🥇</span> #1
                                @elseif($index === 1)
                                    <span class="text-xl">🥈</span> #2
                                @elseif($index === 2)
                                    <span class="text-xl">🥉</span> #3
                                @else
                                    #{{ $index + 1 }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $data['kelas'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-success-50 text-success-700 dark:bg-success-400/10 dark:text-success-400">
                                    {{ $data['hadir'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $data['total'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold {{ $data['persentase'] >= 90 ? 'text-success-600' : ($data['persentase'] >= 75 ? 'text-warning-600' : 'text-danger-600') }}">
                                    {{ number_format($data['persentase'], 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data absensi bulan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
