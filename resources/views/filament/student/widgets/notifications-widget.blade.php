<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-bell class="w-5 h-5" />
                    <span>Notifikasi</span>
                    @if($this->getUnreadCount() > 0)
                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $this->getUnreadCount() }}
                        </span>
                    @endif
                </div>
                @if($this->getUnreadCount() > 0)
                    <button 
                        wire:click="markAllAsRead"
                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                    >
                        Tandai Semua Dibaca
                    </button>
                @endif
            </div>
        </x-slot>

        <div class="space-y-3">
            @forelse($this->getNotifications() as $notification)
                <div 
                    class="p-4 rounded-lg border transition-colors {{ $notification->isUnread() ? 'bg-primary-50 border-primary-200 dark:bg-primary-900/20 dark:border-primary-800' : 'bg-gray-50 border-gray-200 dark:bg-gray-800 dark:border-gray-700' }}"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                @if($notification->type === 'late_arrival')
                                    <x-heroicon-o-clock class="w-4 h-4 text-orange-500 flex-shrink-0" />
                                @elseif($notification->type === 'verification_update')
                                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 flex-shrink-0" />
                                @elseif($notification->type === 'reminder')
                                    <x-heroicon-o-bell-alert class="w-4 h-4 text-yellow-500 flex-shrink-0" />
                                @else
                                    <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 flex-shrink-0" />
                                @endif
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $notification->title }}
                                </h4>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $notification->message }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($notification->isUnread())
                            <button 
                                wire:click="markAsRead({{ $notification->id }})"
                                class="flex-shrink-0 text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                                title="Tandai dibaca"
                            >
                                <x-heroicon-o-check class="w-5 h-5" />
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-bell-slash class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-600 mb-3" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Tidak ada notifikasi
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
