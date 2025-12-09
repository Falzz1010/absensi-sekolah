<?php

namespace App\Filament\Student\Pages;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AbsenceSubmissionPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.student.pages.absence-submission-page';

    protected static ?string $navigationLabel = 'Ajukan Izin/Sakit';

    protected static ?string $title = 'Ajukan Izin/Sakit';

    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Jenis Ketidakhadiran')
                    ->options([
                        'Sakit' => 'Sakit',
                        'Izin' => 'Izin',
                    ])
                    ->required()
                    ->native(false)
                    ->helperText('Pilih jenis ketidakhadiran Anda'),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->native(false)
                    ->maxDate(now())
                    ->helperText('Pilih tanggal ketidakhadiran'),

                Textarea::make('keterangan')
                    ->label('Alasan')
                    ->required()
                    ->rows(4)
                    ->maxLength(500)
                    ->helperText('Jelaskan alasan ketidakhadiran Anda (maksimal 500 karakter)'),

                FileUpload::make('proof_document')
                    ->label('Bukti Pendukung')
                    ->required()
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                    ->maxSize(5120) // 5MB in KB
                    ->directory('attendance-proofs')
                    ->visibility('public')
                    ->helperText('Upload foto surat dokter atau izin orang tua (JPEG, PNG, atau PDF, maksimal 5MB)')
                    ->imagePreviewHeight('200')
                    ->panelLayout('integrated')
                    ->removeUploadedFileButtonPosition('right')
                    ->uploadButtonPosition('left')
                    ->uploadProgressIndicatorPosition('left')
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio(null)
                    ->imageResizeTargetWidth('1920')
                    ->imageResizeTargetHeight('1920')
                    ->orientImagesFromExif(true)
                    ->extraAttributes([
                        'accept' => 'image/jpeg,image/png,application/pdf',
                        'capture' => 'environment', // Enable camera on mobile
                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Get authenticated student
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();

        if (!$murid) {
            Notification::make()
                ->title('Error')
                ->body('Data siswa tidak ditemukan')
                ->danger()
                ->send();
            
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Data siswa tidak ditemukan',
                'duration' => 5000
            ]);
            return;
        }

        // Check if izin/sakit submission already exists for this date
        $existingSubmission = Absensi::where('murid_id', $murid->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->whereNotNull('verification_status')
            ->first();

        if ($existingSubmission) {
            Notification::make()
                ->title('Gagal')
                ->body('Anda sudah mengajukan izin/sakit untuk tanggal ini')
                ->warning()
                ->send();
            
            $this->dispatch('alert', [
                'type' => 'warning',
                'title' => 'Gagal',
                'message' => 'Anda sudah mengajukan izin/sakit untuk tanggal ini',
                'duration' => 5000
            ]);
            return;
        }

        // Create absence record
        $absensi = Absensi::create([
            'murid_id' => $murid->id,
            'tanggal' => $data['tanggal'],
            'status' => $data['status'],
            'kelas' => $murid->kelas,
            'keterangan' => $data['keterangan'],
            'proof_document' => $data['proof_document'],
            'verification_status' => 'pending',
        ]);

        // Send notification to student
        Notification::make()
            ->title('Berhasil')
            ->body('Pengajuan izin/sakit berhasil dikirim dan menunggu verifikasi')
            ->success()
            ->send();

        // Send notification to all admin/guru users
        try {
            $adminGuruUsers = \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['admin', 'guru']);
            })->get();
            
            foreach ($adminGuruUsers as $adminUser) {
                $adminUser->notify(new \App\Notifications\PengajuanIzinNotification($absensi, $murid->name));
            }
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'message' => 'Pengajuan izin/sakit berhasil dikirim dan menunggu verifikasi',
            'duration' => 5000
        ]);

        // Reset form
        $this->form->fill();
    }

    public function getTitle(): string
    {
        return 'Ajukan Izin/Sakit';
    }
}
