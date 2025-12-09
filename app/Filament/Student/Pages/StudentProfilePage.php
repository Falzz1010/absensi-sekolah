<?php

namespace App\Filament\Student\Pages;

use App\Models\Murid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentProfilePage extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static string $view = 'filament.student.pages.student-profile-page';
    
    protected static ?string $title = 'Profil Saya';
    
    protected static ?string $navigationLabel = 'Profil';
    
    public ?array $data = [];
    
    public function mount(): void
    {
        $this->form->fill();
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Upload Foto Profil')
                    ->schema([
                        FileUpload::make('photo')
                            ->label('Foto Profil')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->maxSize(2048) // 2MB in KB
                            ->directory(fn () => 'profile-photos/' . $this->getMurid()->id)
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->helperText('Format: JPEG atau PNG. Maksimal 2MB.')
                            ->required(false)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('500')
                            ->imageResizeTargetHeight('500')
                            ->orientImagesFromExif(true)
                            ->extraAttributes([
                                'accept' => 'image/jpeg,image/png',
                                'capture' => 'user', // Enable front camera for selfies on mobile
                            ])
                    ])
            ])
            ->statePath('data');
    }
    
    public function getMurid(): ?Murid
    {
        $user = Auth::user();
        return Murid::with(['kelasRelation.waliKelas', 'user'])
            ->where('user_id', $user->id)
            ->first();
    }
    
    public function getProfileData(): array
    {
        $murid = $this->getMurid();
        
        if (!$murid) {
            return [];
        }
        
        return [
            'name' => $murid->name,
            'email' => $murid->email,
            'nis' => $murid->id, // Using ID as NIS
            'kelas' => $murid->kelasRelation?->nama ?? $murid->kelas,
            'wali_kelas' => $murid->kelasRelation?->waliKelas?->name ?? '-',
            'photo' => $murid->photo,
        ];
    }
    
    public function submit(): void
    {
        $data = $this->form->getState();
        $murid = $this->getMurid();
        
        if (!$murid) {
            Notification::make()
                ->title('Error')
                ->body('Data siswa tidak ditemukan.')
                ->danger()
                ->send();
            return;
        }
        
        // Delete old photo if exists and new photo is uploaded
        if (isset($data['photo']) && $murid->photo && $data['photo'] !== $murid->photo) {
            Storage::disk('public')->delete($murid->photo);
        }
        
        // Update photo path
        if (isset($data['photo'])) {
            $murid->photo = $data['photo'];
            $murid->save();
            
            Notification::make()
                ->title('Berhasil')
                ->body('Foto profil berhasil diperbarui.')
                ->success()
                ->send();
                
            // Refresh the page to show new photo
            $this->redirect(static::getUrl());
        }
    }
}
