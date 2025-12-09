<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Guru;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\GuruResource\Pages;
use App\Filament\Resources\GuruResource\Pages\EditGuru;
use App\Filament\Resources\GuruResource\Pages\ListGurus;
use App\Filament\Resources\GuruResource\Pages\CreateGuru;

class GuruResource extends Resource
{
    protected static ?string $model = Guru::class;
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Guru';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->label('Akun User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Pilih akun user yang sudah terdaftar dengan role Guru')
                    ->required()
                    ->unique(ignoreRecord: true),
                
                TextInput::make('name')
                    ->label('Nama Guru')
                    ->required(),
                
                TextInput::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->required(),
                
                Select::make('kelas')
                    ->label('Kelas')
                    ->options([
                        '10 IPA' => '10 IPA',
                        '10 IPS' => '10 IPS',
                        '11 IPA' => '11 IPA',
                        '11 IPS' => '11 IPS',
                        '12 IPA' => '12 IPA',
                        '12 IPS' => '12 IPS',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->poll('60s')
            ->defaultPaginationPageOption(25)
            ->columns([
                TextColumn::make('user.name')
                    ->label('Akun User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('name')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('mata_pelajaran')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGurus::route('/'),
            'create' => Pages\CreateGuru::route('/create'),
            'edit' => Pages\EditGuru::route('/{record}/edit'),
        ];
    }
}