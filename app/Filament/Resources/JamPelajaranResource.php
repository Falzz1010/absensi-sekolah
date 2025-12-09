<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JamPelajaranResource\Pages;
use App\Filament\Resources\JamPelajaranResource\RelationManagers;
use App\Models\JamPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JamPelajaranResource extends Resource
{
    protected static ?string $model = JamPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Jam Pelajaran';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jam Pelajaran')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Jam')
                            ->placeholder('Contoh: Jam ke-1')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->seconds(false),

                                Forms\Components\TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->seconds(false)
                                    ->after('jam_mulai'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Jam')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('durasi')
                    ->label('Durasi')
                    ->state(function (JamPelajaran $record) {
                        $start = \Carbon\Carbon::parse($record->jam_mulai);
                        $end = \Carbon\Carbon::parse($record->jam_selesai);
                        return $start->diffInMinutes($end) . ' menit';
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('urutan', 'asc')
            ->filters([
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
            'index' => Pages\ListJamPelajarans::route('/'),
            'create' => Pages\CreateJamPelajaran::route('/create'),
            'edit' => Pages\EditJamPelajaran::route('/{record}/edit'),
        ];
    }
}
