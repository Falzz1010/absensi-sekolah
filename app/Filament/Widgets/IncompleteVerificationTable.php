<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class IncompleteVerificationTable extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = '⚠️ Siswa dengan Verifikasi Belum Lengkap (Hari Ini)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->with('murid')
                    ->whereDate('tanggal', today())
                    ->where('status', 'Hadir')
                    ->where('is_complete', false)
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('murid.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                
                TextColumn::make('qr_scan_done')
                    ->label('QR Scan')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? '✓ Sudah' : '✗ Belum')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                
                TextColumn::make('qr_scan_time')
                    ->label('Waktu QR')
                    ->time('H:i')
                    ->placeholder('-'),
                
                TextColumn::make('manual_checkin_done')
                    ->label('Manual')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? '✓ Sudah' : '✗ Belum')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                
                TextColumn::make('manual_checkin_time')
                    ->label('Waktu Manual')
                    ->time('H:i')
                    ->placeholder('-'),
                
                TextColumn::make('first_method')
                    ->label('Metode Pertama')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'qr_scan' => 'QR Scan',
                        'manual' => 'Manual',
                        default => '-'
                    })
                    ->color(fn ($state) => match($state) {
                        'qr_scan' => 'info',
                        'manual' => 'warning',
                        default => 'gray'
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Absensi $record): string => route('filament.admin.resources.absensis.edit', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('🎉 Semua Siswa Sudah Lengkap!')
            ->emptyStateDescription('Tidak ada siswa dengan verifikasi belum lengkap hari ini.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->poll('30s');
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }
}
