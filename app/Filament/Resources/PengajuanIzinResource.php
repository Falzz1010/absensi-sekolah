<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanIzinResource\Pages;
use App\Models\Absensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengajuanIzinResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Pengajuan Izin/Sakit';

    protected static ?string $modelLabel = 'Pengajuan Izin/Sakit';

    protected static ?string $pluralModelLabel = 'Pengajuan Izin/Sakit';

    protected static ?string $navigationGroup = 'Manajemen Absensi';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        // Only show records with verification_status (izin/sakit submissions)
        return parent::getEloquentQuery()
            ->whereNotNull('verification_status')
            ->whereIn('status', ['Sakit', 'Izin'])
            ->with(['murid'])
            ->latest('tanggal');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Siswa')
                    ->schema([
                        Forms\Components\Select::make('murid_id')
                            ->label('Nama Siswa')
                            ->relationship('murid', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('kelas')
                            ->label('Kelas')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('status')
                            ->label('Jenis')
                            ->options([
                                'Sakit' => 'Sakit',
                                'Izin' => 'Izin',
                            ])
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Alasan')
                            ->rows(4)
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('proof_document')
                            ->label('Bukti Pendukung')
                            ->image()
                            ->visibility('public')
                            ->disabled()
                            ->dehydrated()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Verifikasi')
                    ->schema([
                        Forms\Components\Select::make('verification_status')
                            ->label('Status Verifikasi')
                            ->options([
                                'pending' => 'Menunggu Verifikasi',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->native(false)
                            ->default('pending'),

                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->helperText('Tambahkan catatan jika diperlukan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('30s')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('murid.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sakit' => 'warning',
                        'Izin' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Alasan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),

                Tables\Columns\ImageColumn::make('proof_document')
                    ->label('Bukti')
                    ->circular()
                    ->defaultImageUrl(url('/images/no-image.png'))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('verification_status')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Jenis')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                    ]),

                Tables\Filters\SelectFilter::make('verification_status')
                    ->label('Status Verifikasi')
                    ->options([
                        'pending' => 'Menunggu Verifikasi',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending'),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal'),
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
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan ini?')
                    ->action(function (Absensi $record) {
                        $record->update([
                            'verification_status' => 'approved',
                            'verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->body('Pengajuan telah disetujui')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Absensi $record): bool => $record->verification_status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan ini?')
                    ->form([
                        Forms\Components\Textarea::make('verification_notes')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Absensi $record, array $data) {
                        $record->update([
                            'verification_status' => 'rejected',
                            'verification_notes' => $data['verification_notes'],
                            'verified_at' => now(),
                            'verified_by' => auth()->id(),
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil')
                            ->body('Pengajuan telah ditolak')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Absensi $record): bool => $record->verification_status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_bulk')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->verification_status === 'pending') {
                                    $record->update([
                                        'verification_status' => 'approved',
                                        'verified_at' => now(),
                                        'verified_by' => auth()->id(),
                                    ]);
                                }
                            });
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil')
                                ->body('Pengajuan terpilih telah disetujui')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanIzins::route('/'),
            'view' => Pages\ViewPengajuanIzin::route('/{record}'),
            'edit' => Pages\EditPengajuanIzin::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNotNull('verification_status')
            ->where('verification_status', 'pending')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
