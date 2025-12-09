<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KelasResource\Pages;
use App\Filament\Resources\KelasResource\RelationManagers;
use App\Models\Kelas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Manajemen Kelas';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Kelas')
                            ->placeholder('Contoh: X IPA 1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('tingkat')
                            ->label('Tingkat')
                            ->options([
                                '10' => 'Kelas 10',
                                '11' => 'Kelas 11',
                                '12' => 'Kelas 12',
                            ])
                            ->required(),

                        Forms\Components\Select::make('jurusan')
                            ->label('Jurusan')
                            ->options([
                                'IPA' => 'IPA',
                                'IPS' => 'IPS',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('nomor_kelas')
                            ->label('Nomor Kelas')
                            ->numeric()
                            ->placeholder('1, 2, 3, dst')
                            ->minValue(1),

                        Forms\Components\Select::make('wali_kelas_id')
                            ->label('Wali Kelas')
                            ->relationship('waliKelas', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('kapasitas')
                            ->label('Kapasitas Maksimal')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->minValue(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'IPA' => 'success',
                        'IPS' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('waliKelas.name')
                    ->label('Wali Kelas')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('murids_count')
                    ->label('Jumlah Murid')
                    ->counts('murids')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('kapasitas')
                    ->label('Kapasitas')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->defaultSort('tingkat')
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat')
                    ->options([
                        '10' => 'Kelas 10',
                        '11' => 'Kelas 11',
                        '12' => 'Kelas 12',
                    ]),

                Tables\Filters\SelectFilter::make('jurusan')
                    ->options([
                        'IPA' => 'IPA',
                        'IPS' => 'IPS',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
