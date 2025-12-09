<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl p-6 shadow-xl" style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #7c3aed 100%);">
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
                    
                    <!-- Class Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full mb-3 shadow-lg" style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                        <svg class="w-4 h-4" style="color: white;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                        <span class="text-sm font-bold" style="color: white;">Kelas {{ $this->getUserClass() }}</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info -->
            <div class="mt-4 pt-4" style="border-top: 1px solid rgba(255, 255, 255, 0.25);">
                <p class="text-sm font-medium" style="color: rgba(255, 255, 255, 0.9);">
                    Jangan lupa untuk melakukan absensi hari ini! Semangat belajar! 📚
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
