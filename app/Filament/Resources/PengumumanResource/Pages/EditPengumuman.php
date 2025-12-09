<?php

namespace App\Filament\Resources\PengumumanResource\Pages;

use App\Filament\Resources\PengumumanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengumuman extends EditRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert comma-separated string to array for kelas_target
        if (isset($data['kelas_target']) && is_string($data['kelas_target'])) {
            $data['kelas_target'] = explode(',', $data['kelas_target']);
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Convert array back to comma-separated string
        if (isset($data['kelas_target']) && is_array($data['kelas_target'])) {
            $data['kelas_target'] = implode(',', $data['kelas_target']);
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
