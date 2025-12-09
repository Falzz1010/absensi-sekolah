<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QrCodeResource\Pages;
use App\Models\QrCode;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

class QrCodeResource extends Resource
{
    protected static ?string $model = QrCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR Code Absensi';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole(['admin', 'guru']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi QR Code')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama QR Code')
                            ->placeholder('Contoh: QR Global Sekolah')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('tipe')
                            ->label('Tipe QR Code')
                            ->options([
                                'global' => 'Global (Semua Kelas)',
                                'kelas' => 'Per Kelas',
                            ])
                            ->required()
                            ->live()
                            ->default('global'),

                        Forms\Components\Select::make('kelas')
                            ->label('Pilih Kelas')
                            ->options(Kelas::pluck('nama', 'nama'))
                            ->searchable()
                            ->visible(fn (Get $get) => $get('tipe') === 'kelas')
                            ->required(fn (Get $get) => $get('tipe') === 'kelas'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('berlaku_dari')
                                    ->label('Berlaku Dari')
                                    ->default(now()),

                                Forms\Components\DatePicker::make('berlaku_sampai')
                                    ->label('Berlaku Sampai')
                                    ->after('berlaku_dari'),
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('code_info')
                            ->label('Kode QR')
                            ->content(fn ($record) => $record ? $record->code : 'Kode akan digenerate otomatis')
                            ->visible(fn ($record) => $record !== null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'global' => 'success',
                        'kelas' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'global' => 'Global',
                        'kelas' => 'Per Kelas',
                    }),

                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->default('-'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin!')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->code),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('berlaku_dari')
                    ->label('Berlaku Dari')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('berlaku_sampai')
                    ->label('Berlaku Sampai')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'global' => 'Global',
                        'kelas' => 'Per Kelas',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_qr')
                    ->label('Lihat QR')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (QrCode $record) => route('qr.view', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('download_qr')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (QrCode $record) => route('qr.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrCodes::route('/'),
            'create' => Pages\CreateQrCode::route('/create'),
            'edit' => Pages\EditQrCode::route('/{record}/edit'),
        ];
    }
}
