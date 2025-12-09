<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Jadwal Hari Ini
        </x-slot>
        
        @php
            $data = $this->getTodaySchedule();
            $currentClass = $this->getCurrentClass();
        @endphp
        
        <div class="space-y-3">
            @if(isset($data['holiday']))
                {{-- Holiday --}}
                <div class="rounded-lg bg-info-50 p-6 text-center dark:bg-info-900/20">
                    <svg class="mx-auto h-12 w-12 text-info-600 dark:text-info-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="mt-3 text-lg font-semibold text-info-900 dark:text-info-100">{{ $data['holiday']->nama }}</h3>
                    <p class="mt-1 text-sm text-info-700 dark:text-info-300">Tidak ada kelas hari ini</p>
                </div>
            @elseif(isset($data['schedules']) && $data['schedules']->count() > 0)
                {{-- Schedule list --}}
                @foreach($data['schedules'] as $schedule)
                    @php
                        $isCurrentClass = $currentClass && $currentClass->id === $schedule->id;
                    @endphp
                    
                    <div class="rounded-lg border p-3 sm:p-4 transition {{ $isCurrentClass ? 'border-primary-500 bg-primary-50 dark:border-primary-400 dark:bg-primary-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm sm:text-base font-semibold {{ $isCurrentClass ? 'text-primary-900 dark:text-primary-100' : 'text-gray-900 dark:text-gray-100' }} break-words">
                                        {{ $schedule->mata_pelajaran }}
                                    </h4>
                                    @if($isCurrentClass)
                                        <span class="rounded-full bg-primary-600 px-2 py-0.5 text-xs font-medium text-white whitespace-nowrap">
                                            Berlangsung
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="mt-2 space-y-1 text-xs sm:text-sm {{ $isCurrentClass ? 'text-primary-700 dark:text-primary-300' : 'text-gray-600 dark:text-gray-400' }}">
                                    @if($schedule->guru)
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span class="break-words">{{ $schedule->guru->name }}</span>
                                        </div>
                                    @endif
                                    
                                    @if($schedule->jam_mulai && $schedule->jam_selesai)
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                    

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- No schedule --}}
                <div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-600 dark:bg-gray-800">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Tidak Ada Jadwal</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada kelas yang dijadwalkan untuk hari ini</p>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
