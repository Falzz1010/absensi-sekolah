<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Absensi;
use App\Exports\AbsensiExport;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\LaporanKehadiranResource\Pages;
use Filament\Tables\Enums\RecordCheckboxPosition;
use App\Filament\Resources\LaporanKehadiranResource\Pages\ListLaporanKehadirans;

class LaporanKehadiranResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Kehadiran';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('30s')
            ->modifyQueryUsing(fn ($query) => $query->with('murid'))
            ->recordCheckboxPosition(null)
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('murid.name')->label('Nama Murid')->sortable(),
                TextColumn::make('murid.kelas')->label('Kelas')->sortable(),
                TextColumn::make('tanggal')->label('Tanggal')->date(),
                TextColumn::make('status')->label('Status')->sortable(),
            ])
            ->filters([
                Filter::make('tanggal')
                    ->label('Filter Tanggal')
                    ->form([DatePicker::make('tanggal')]),

                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                    ]),

                SelectFilter::make('murid.kelas')
                    ->label('Filter Kelas')
                    ->options([
                        '10 IPA' => '10 IPA',
                        '10 IPS' => '10 IPS',
                        '11 IPA' => '11 IPA',
                        '11 IPS' => '11 IPS',
                        '12 IPA' => '12 IPA',
                        '12 IPS' => '12 IPS',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('export_excel')
                    ->label('Export Excel')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(fn($records) => Excel::download(new AbsensiExport($records), 'laporan-absensi.xlsx'))
                    ->deselectRecordsAfterCompletion(),
                
                BulkAction::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-m-document-arrow-down')
                    ->color('danger')
                    ->action(function ($records) {
                        $pdf = \PDF::loadView('pdf.laporan-absensi', [
                            'absensis' => $records,
                            'tanggal' => now()->format('d F Y'),
                        ]);
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'laporan-absensi-' . now()->format('Y-m-d') . '.pdf');
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKehadirans::route('/'),
        ];
    }
}