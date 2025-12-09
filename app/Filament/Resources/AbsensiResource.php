<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Models\Absensi;
use App\Models\Murid;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }

    protected static function getVerificationDetails($record): string
    {
        if (!$record) return '';
        
        $details = [];
        if ($record->qr_scan_done) {
            $time = $record->qr_scan_time ? $record->qr_scan_time->format('H:i') : '';
            $details[] = "✓ QR ({$time})";
        } else {
            $details[] = '✗ QR';
        }
        
        if ($record->manual_checkin_done) {
            $time = $record->manual_checkin_time ? $record->manual_checkin_time->format('H:i') : '';
            $details[] = "✓ Manual ({$time})";
        } else {
            $details[] = '✗ Manual';
        }
        
        return implode(' | ', $details);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('murid_id')
                    ->label('Nama Murid')
                    ->relationship(
                        name: 'murid',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->whereHas('user', function ($q) {
                            $q->whereHas('roles', function ($r) {
                                $r->where('name', 'murid');
                            });
                        })
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' (' . $record->kelas . ')')
                    ->searchable(['name', 'kelas'])
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, callable $set) {
                        $muridId = $get('murid_id');
                        if ($muridId) {
                            $murid = Murid::find($muridId);
                            if ($murid) {
                                $set('kelas', $murid->kelas);
                            }
                        }
                    })
                    ->helperText('Pilih murid (hanya yang punya akun user dengan role murid)'),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()->toDateString()),

                Forms\Components\TextInput::make('kelas')
                    ->label('Kelas')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->helperText('Otomatis terisi dari data murid'),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                    ])
                    ->default('Hadir')
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->placeholder('Tambahkan catatan (opsional, misal: izin ke dokter)')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('30s')
            ->modifyQueryUsing(fn ($query) => $query->with('murid'))
            ->columns([
                TextColumn::make('murid.name')
                    ->label('Nama Murid')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Sakit' => 'info',
                        'Izin' => 'warning',
                        'Alfa' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('is_complete')
                    ->label('Verifikasi')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $state ? '✅ Lengkap' : '⚠️ Belum Lengkap')
                    ->color(fn ($state): string => $state ? 'success' : 'warning')
                    ->description(fn ($record) => self::getVerificationDetails($record))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('check_in_time')
                    ->label('Waktu Check-in')
                    ->time('H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_late')
                    ->label('Terlambat')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $state ? "Ya ({$record->late_duration} mnt)" : 'Tidak')
                    ->color(fn ($state): string => $state ? 'danger' : 'success')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->default('-')
                    ->toggleable(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->defaultPaginationPageOption(25)
            ->filters([
                Tables\Filters\SelectFilter::make('kelas')
                    ->label('Filter Kelas')
                    ->options([
                        '10 IPA' => '10 IPA',
                        '10 IPS' => '10 IPS',
                        '11 IPA' => '11 IPA',
                        '11 IPS' => '11 IPS',
                        '12 IPA' => '12 IPA',
                        '12 IPS' => '12 IPS',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                        'Alfa' => 'Alfa',
                    ]),

                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn($query) => $query->whereDate('tanggal', today()))
                    ->toggle(),
                
                Tables\Filters\SelectFilter::make('is_complete')
                    ->label('Status Verifikasi')
                    ->options([
                        '1' => '✅ Lengkap',
                        '0' => '⚠️ Belum Lengkap',
                    ])
                    ->placeholder('Semua Status'),

                Tables\Filters\Filter::make('belum_lengkap_hari_ini')
                    ->label('Belum Lengkap Hari Ini')
                    ->query(fn($query) => $query
                        ->whereDate('tanggal', today())
                        ->where('is_complete', false)
                        ->where('status', 'Hadir')
                    )
                    ->toggle(),
                
                Tables\Filters\SelectFilter::make('guru')
                    ->label('Filter Guru')
                    ->options(\App\Models\Guru::pluck('name', 'kelas')->toArray())
                    ->attribute('kelas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('sendReminder')
                        ->label('Kirim Reminder')
                        ->icon('heroicon-o-bell-alert')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Kirim Reminder Verifikasi')
                        ->modalDescription('Kirim notifikasi ke siswa yang dipilih untuk melengkapi verifikasi absensi mereka.')
                        ->modalSubmitActionLabel('Kirim Reminder')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $sent = 0;
                            foreach ($records as $record) {
                                if (!$record->is_complete && $record->murid) {
                                    // Tentukan metode yang belum dilakukan
                                    $missingMethod = '';
                                    if (!$record->qr_scan_done) {
                                        $missingMethod = 'QR Scan';
                                    } elseif (!$record->manual_checkin_done) {
                                        $missingMethod = 'Absensi Manual';
                                    }
                                    
                                    if ($missingMethod) {
                                        // Kirim ke StudentNotification (untuk Student Panel)
                                        \App\Models\StudentNotification::create([
                                            'murid_id' => $record->murid->id,
                                            'type' => 'reminder',
                                            'title' => 'Reminder: Lengkapi Verifikasi Absensi',
                                            'message' => "Anda belum melakukan {$missingMethod} untuk tanggal " . $record->tanggal->format('d/m/Y') . ". Segera lengkapi verifikasi Anda!",
                                            'data' => [
                                                'absensi_id' => $record->id,
                                                'tanggal' => $record->tanggal->format('Y-m-d'),
                                                'missing_method' => $missingMethod,
                                            ],
                                        ]);
                                        
                                        // Juga kirim ke Filament notification jika user ada (untuk bell icon)
                                        if ($record->murid->user) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Reminder: Lengkapi Verifikasi Absensi')
                                                ->body("Anda belum melakukan {$missingMethod} untuk tanggal " . $record->tanggal->format('d/m/Y') . ". Segera lengkapi verifikasi Anda!")
                                                ->warning()
                                                ->sendToDatabase($record->murid->user);
                                        }
                                        
                                        $sent++;
                                    }
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Reminder Terkirim')
                                ->body("Berhasil mengirim {$sent} reminder ke siswa.")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}