<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Manajemen User';
    protected static ?string $navigationLabel = 'Users';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn($record) => $record === null)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Show kelas field only if 'murid' or 'student' role is selected
                        $isMurid = collect($state)->contains(fn($roleId) => 
                            \Spatie\Permission\Models\Role::find($roleId)?->name === 'murid' ||
                            \Spatie\Permission\Models\Role::find($roleId)?->name === 'student'
                        );
                    }),
                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas')
                    ->options(\App\Models\Kelas::pluck('nama', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(function ($get) {
                        $roles = $get('roles');
                        if (!$roles) {
                            return false;
                        }
                        
                        // Check if any selected role is 'murid' or 'student'
                        return collect($roles)->contains(function ($roleId) {
                            $role = \Spatie\Permission\Models\Role::find($roleId);
                            return $role && ($role->name === 'murid' || $role->name === 'student');
                        });
                    })
                    ->helperText('Pilih kelas untuk siswa ini (opsional, bisa diatur nanti)')
                    ->dehydrated(false), // Don't save to users table
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Filter Role'),
            ])
            ->actions([
                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->same('confirm_password')
                            ->helperText('Minimal 8 karakter'),
                        Forms\Components\TextInput::make('confirm_password')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'password' => bcrypt($data['new_password']),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Password berhasil direset!')
                            ->body('Password untuk ' . $record->name . ' telah diubah')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password User')
                    ->modalDescription('Anda akan mereset password untuk user ini. Pastikan user mengetahui password barunya.')
                    ->modalSubmitActionLabel('Reset Password'),
                
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
