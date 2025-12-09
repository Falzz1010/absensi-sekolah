<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Murid;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load kelas_id from Murid record if exists
        if ($this->record->hasAnyRole(['murid', 'student'])) {
            $murid = Murid::where('user_id', $this->record->id)->first();
            if ($murid) {
                $data['kelas_id'] = $murid->kelas_id;
            }
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Update kelas_id in Murid record if provided
        $kelasId = $this->data['kelas_id'] ?? null;
        
        if ($this->record->hasAnyRole(['murid', 'student'])) {
            $murid = Murid::where('user_id', $this->record->id)->first();
            
            if ($murid) {
                $kelas = \App\Models\Kelas::find($kelasId);
                $murid->update([
                    'kelas_id' => $kelasId,
                    'kelas' => $kelas ? $kelas->nama : null,
                ]);
            }
        }
    }
}
