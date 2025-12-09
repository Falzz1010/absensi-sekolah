<?php

namespace App\Filament\Student\Pages;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceHistoryPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static string $view = 'filament.student.pages.attendance-history-page';
    
    protected static ?string $title = 'Riwayat Kehadiran';
    
    protected static ?string $navigationLabel = 'Riwayat Kehadiran';
    
    protected static ?int $navigationSort = 2;

    protected function getVerificationDetails($record): string
    {
        if (!$record) return '';
        
        $details = [];
        if ($record->qr_scan_done) {
            $details[] = '✓ QR Scan';
        } else {
            $details[] = '✗ QR Scan';
        }
        
        if ($record->manual_checkin_done) {
            $details[] = '✓ Manual';
        } else {
            $details[] = '✗ Manual';
        }
        
        return implode(' | ', $details);
    }

    public function table(Table $table): Table
    {
        // Get the authenticated student's murid_id
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();
        
        return $table
            ->query(
                Absensi::query()
                    ->where('murid_id', $murid?->id)
                    ->where('tanggal', '>=', Carbon::now()->subDays(30))
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Sakit' => 'warning',
                        'Izin' => 'info',
                        'Alfa' => 'danger',
                        'Terlambat' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('check_in_time')
                    ->label('Waktu')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('late_duration')
                    ->label('Terlambat')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} mnt" : '-')
                    ->placeholder('-')
                    ->visible(fn ($record) => $record && $record->is_late)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_complete')
                    ->label('Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $state ? '✅ Lengkap' : '⚠️ Belum Lengkap')
                    ->color(fn ($state): string => $state ? 'success' : 'warning')
                    ->description(fn ($record) => $this->getVerificationDetails($record))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->placeholder('-')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                        'Terlambat' => 'Terlambat',
                    ])
                    ->placeholder('Semua Status'),
                Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->default(Carbon::now()->subDays(30)),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->default(Carbon::now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari: ' . Carbon::parse($data['dari'])->format('d/m/Y');
                        }
                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai: ' . Carbon::parse($data['sampai'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Detail')
                    ->modalHeading('Detail Kehadiran')
                    ->modalContent(fn (Absensi $record) => view('filament.student.pages.attendance-detail-modal', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(25);
    }
}
