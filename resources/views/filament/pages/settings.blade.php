<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center gap-4">
            <x-filament::button 
                type="submit" 
                size="lg"
                color="primary"
                icon="heroicon-o-check-circle"
            >
                Simpan Pengaturan
            </x-filament::button>
            
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Perubahan akan langsung diterapkan ke seluruh sistem
            </p>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
