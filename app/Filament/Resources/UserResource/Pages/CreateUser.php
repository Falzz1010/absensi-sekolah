<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Murid;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // After user is created, the Observer will auto-create Murid record
        // Now we need to update the kelas_id if it was provided
        $kelasId = $this->data['kelas_id'] ?? null;
        
        if ($kelasId && $this->record->hasAnyRole(['murid', 'student'])) {
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
