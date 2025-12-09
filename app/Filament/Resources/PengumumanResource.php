<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Filament\Resources\PengumumanResource\RelationManagers;
use App\Models\Kelas;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengumuman')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul Pengumuman')
                            ->placeholder('Contoh: Ujian Tengah Semester')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('isi')
                            ->label('Isi Pengumuman')
                            ->placeholder('Tulis isi pengumuman di sini...')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('prioritas')
                            ->label('Prioritas')
                            ->options([
                                'rendah' => 'Rendah',
                                'sedang' => 'Sedang',
                                'tinggi' => 'Tinggi',
                            ])
                            ->default('sedang')
                            ->required()
                            ->native(false)
                            ->helperText('Prioritas tinggi akan ditampilkan dengan warna merah'),

                        Forms\Components\Select::make('target')
                            ->label('Target Penerima')
                            ->options([
                                'semua' => 'Semua Siswa',
                                'kelas_tertentu' => 'Kelas Tertentu',
                            ])
                            ->default('semua')
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                $state === 'semua' ? $set('kelas_target', null) : null
                            ),

                        Forms\Components\Select::make('kelas_target')
                            ->label('Pilih Kelas')
                            ->multiple()
                            ->options(function () {
                                return Kelas::pluck('nama', 'nama')->toArray();
                            })
                            ->visible(fn (Forms\Get $get) => $get('target') === 'kelas_tertentu')
                            ->required(fn (Forms\Get $get) => $get('target') === 'kelas_tertentu')
                            ->helperText('Pilih satu atau lebih kelas yang akan menerima pengumuman'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk menyembunyikan pengumuman'),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->native(false)
                            ->helperText('Pengumuman akan dikirim pada tanggal ini'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('30s')
            ->defaultSort('published_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('isi')
                    ->label('Isi')
                    ->html()
                    ->limit(100)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('prioritas')
                    ->label('Prioritas')
                    ->colors([
                        'success' => 'rendah',
                        'warning' => 'sedang',
                        'danger' => 'tinggi',
                    ])
                    ->icons([
                        'heroicon-o-arrow-down' => 'rendah',
                        'heroicon-o-minus' => 'sedang',
                        'heroicon-o-arrow-up' => 'tinggi',
                    ]),

                Tables\Columns\BadgeColumn::make('target')
                    ->label('Target')
                    ->formatStateUsing(fn (string $state): string => 
                        $state === 'semua' ? 'Semua Siswa' : 'Kelas Tertentu'
                    )
                    ->colors([
                        'primary' => 'semua',
                        'info' => 'kelas_tertentu',
                    ]),

                Tables\Columns\TextColumn::make('kelas_target')
                    ->label('Kelas')
                    ->formatStateUsing(fn (?string $state): string => 
                        $state ? str_replace(',', ', ', $state) : '-'
                    )
                    ->limit(30),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Publikasi')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prioritas')
                    ->label('Prioritas')
                    ->options([
                        'rendah' => 'Rendah',
                        'sedang' => 'Sedang',
                        'tinggi' => 'Tinggi',
                    ]),

                Tables\Filters\SelectFilter::make('target')
                    ->label('Target')
                    ->options([
                        'semua' => 'Semua Siswa',
                        'kelas_tertentu' => 'Kelas Tertentu',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPengumumen::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
