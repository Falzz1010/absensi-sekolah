<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Murid;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MuridResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class MuridResource extends Resource
{
    protected static ?string $model = Murid::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Murid';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 3;
    
    // Hide from navigation - use UserResource instead
    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Murid')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Murid')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),

                        Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelasRelation', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $kelas = \App\Models\Kelas::find($state);
                                    if ($kelas) {
                                        $set('kelas', $kelas->nama);
                                    }
                                }
                            }),

                        TextInput::make('kelas')
                            ->label('Kelas (Text)')
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Otomatis terisi dari kelas yang dipilih'),

                        Toggle::make('is_active')
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
            ->modifyQueryUsing(fn ($query) => $query->with('kelasRelation'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('kelasRelation.nama')
                    ->label('Kelas')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Filter Kelas')
                    ->relationship('kelasRelation', 'nama'),
                
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        1 => 'Aktif',
                        0 => 'Tidak Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('assign_kelas')
                    ->label('Pindah Kelas')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('kelas_id')
                            ->label('Kelas Baru')
                            ->relationship('kelasRelation', 'nama')
                            ->required(),
                    ])
                    ->action(function (Murid $record, array $data) {
                        $kelas = \App\Models\Kelas::find($data['kelas_id']);
                        $record->update([
                            'kelas_id' => $data['kelas_id'],
                            'kelas' => $kelas->nama,
                        ]);
                        
                        Notification::make()
                            ->title('Berhasil memindahkan murid ke ' . $kelas->nama)
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign_kelas_bulk')
                        ->label('Assign ke Kelas')
                        ->icon('heroicon-o-user-group')
                        ->form([
                            Select::make('kelas_id')
                                ->label('Pilih Kelas')
                                ->relationship('kelasRelation', 'nama')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $kelas = \App\Models\Kelas::find($data['kelas_id']);
                            foreach ($records as $record) {
                                $record->update([
                                    'kelas_id' => $data['kelas_id'],
                                    'kelas' => $kelas->nama,
                                ]);
                            }
                            
                            Notification::make()
                                ->title('Berhasil assign ' . count($records) . ' murid ke ' . $kelas->nama)
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListMurids::route('/'),
            'create' => Pages\CreateMurid::route('/create'),
            'edit' => Pages\EditMurid::route('/{record}/edit'),
        ];
    }

    public static function getHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required()
                        ->helperText('Format: nama, email, kelas, status (opsional)'),
                ])
                ->action(function (array $data) {
                    try {
                        \Maatwebsite\Excel\Facades\Excel::import(
                            new \App\Imports\MuridImport,
                            $data['file']
                        );

                        Notification::make()
                            ->title('Import berhasil!')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import gagal!')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}