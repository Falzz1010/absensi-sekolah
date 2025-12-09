<x-filament-panels::page>
    @php
        $profileData = $this->getProfileData();
    @endphp
    
    <div class="space-y-4 sm:space-y-6">
        {{-- Profile Information Display --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-900 dark:text-white">Informasi Profil</h2>
            
            <div class="flex flex-col md:flex-row gap-4 sm:gap-6">
                {{-- Profile Photo --}}
                <div class="flex-shrink-0 mx-auto md:mx-0">
                    @if($profileData['photo'])
                        <img 
                            src="{{ route('files.profile-photo', ['path' => base64_encode($profileData['photo'])]) }}" 
                            alt="Foto Profil" 
                            class="w-24 h-24 sm:w-32 sm:h-32 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700"
                        >
                    @else
                        <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-gray-200 dark:border-gray-700">
                            <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Profile Details --}}
                <div class="flex-1 space-y-2 sm:space-y-3">
                    <div>
                        <label class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Nama</label>
                        <p class="text-sm sm:text-base text-gray-900 dark:text-white break-words">{{ $profileData['name'] }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">NIS</label>
                        <p class="text-sm sm:text-base text-gray-900 dark:text-white">{{ $profileData['nis'] }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="text-sm sm:text-base text-gray-900 dark:text-white break-all">{{ $profileData['email'] }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Kelas</label>
                        <p class="text-sm sm:text-base text-gray-900 dark:text-white">{{ $profileData['kelas'] }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Wali Kelas</label>
                        <p class="text-sm sm:text-base text-gray-900 dark:text-white break-words">{{ $profileData['wali_kelas'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Photo Upload Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <form wire:submit="submit">
                {{ $this->form }}
                
                <div class="mt-4">
                    <x-filament::button type="submit" class="w-full md:w-auto min-h-[44px]">
                        Simpan Foto Profil
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        {{-- Today's Schedule Section --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-900 dark:text-white">Jadwal Hari Ini</h2>
            
            @php
                $scheduleWidget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
                $scheduleData = $scheduleWidget->getTodaySchedule();
            @endphp
            
            @if(isset($scheduleData['holiday']))
                <div class="text-center py-6 sm:py-8">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-yellow-500 mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white break-words px-4">{{ $scheduleData['holiday']->nama }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Hari Libur</p>
                </div>
            @elseif(isset($scheduleData['schedules']) && $scheduleData['schedules']->count() > 0)
                <div class="space-y-2 sm:space-y-3">
                    @foreach($scheduleData['schedules'] as $schedule)
                        @php
                            $currentClass = $scheduleWidget->getCurrentClass();
                            $isCurrentClass = $currentClass && $currentClass->id === $schedule->id;
                        @endphp
                        
                        <div class="border rounded-lg p-3 sm:p-4 {{ $isCurrentClass ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-gray-900 dark:text-white break-words">
                                        {{ $schedule->mata_pelajaran }}
                                        @if($isCurrentClass)
                                            <span class="ml-1 sm:ml-2 inline-block text-xs bg-primary-500 text-white px-1.5 sm:px-2 py-0.5 sm:py-1 rounded whitespace-nowrap">Berlangsung</span>
                                        @endif
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1 break-words">
                                        {{ $schedule->guru->name ?? '-' }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-500 mt-1">
                                        Ruang: {{ $schedule->ruangan ?? '-' }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                        {{ substr($schedule->jam_mulai, 0, 5) }} - {{ substr($schedule->jam_selesai, 0, 5) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 sm:py-8">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Tidak ada jadwal untuk hari ini</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
