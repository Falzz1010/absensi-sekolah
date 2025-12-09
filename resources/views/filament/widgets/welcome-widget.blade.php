<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl p-6 shadow-xl" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #ef4444 100%);">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mt-4 -mr-4 h-32 w-32 rounded-full" style="background: rgba(255, 255, 255, 0.1);"></div>
        <div class="absolute bottom-0 left-0 -mb-8 -ml-8 h-40 w-40 rounded-full" style="background: rgba(255, 255, 255, 0.05);"></div>
        
        <div class="relative" style="z-index: 10;">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <!-- Greeting -->
                    <h2 class="text-3xl font-bold mb-2" style="color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        {{ $this->getGreeting() }}, {{ $this->getUserName() }}! 👋
                    </h2>
                    
                    <!-- Role Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-3 shadow-lg" style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                        <svg class="w-4 h-4" style="color: white;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-bold" style="color: white;">{{ $this->getUserRole() }}</span>
                    </div>
                    
                    <!-- Date -->
                    <p class="text-sm flex items-center gap-2 font-medium" style="color: rgba(255, 255, 255, 0.95);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $this->getCurrentDate() }}
                    </p>
                </div>
                
                <!-- Icon -->
                <div class="hidden sm:block">
                    <div class="h-20 w-20 rounded-full flex items-center justify-center shadow-xl" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.2);">
                        <svg class="w-10 h-10" style="color: white; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.25);">
                <p class="text-sm font-medium" style="color: rgba(255, 255, 255, 0.9);">
                    Selamat datang di Sistem Absensi Sekolah. Semoga hari Anda menyenangkan! 🎓
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
