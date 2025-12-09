<?php

namespace App\Filament\Resources\PengajuanIzinResource\Pages;

use App\Filament\Resources\PengajuanIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPengajuanIzins extends ListRecords
{
    protected static string $resource = PengajuanIzinResource::class;

    // Auto-refresh every 30 seconds
    protected static ?string $pollingInterval = '30s';

    protected function getHeaderActions(): array
    {
        return [
            // No create action - submissions come from students only
        ];
    }

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(fn () => $this->getModel()::whereNotNull('verification_status')->count()),

            'menunggu' => Tab::make('Menunggu Verifikasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('verification_status', 'pending'))
                ->badge(fn () => $this->getModel()::where('verification_status', 'pending')->count())
                ->badgeColor('warning'),

            'disetujui' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('verification_status', 'approved'))
                ->badge(fn () => $this->getModel()::where('verification_status', 'approved')->count())
                ->badgeColor('success'),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('verification_status', 'rejected'))
                ->badge(fn () => $this->getModel()::where('verification_status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }
}
