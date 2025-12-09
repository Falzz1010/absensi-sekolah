<?php

namespace App\Notifications;

use App\Models\Absensi;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PengajuanIzinNotification extends Notification
{
    use Queueable;

    protected $absensi;
    protected $muridName;

    public function __construct(Absensi $absensi, string $muridName)
    {
        $this->absensi = $absensi;
        $this->muridName = $muridName;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Pengajuan Baru')
            ->body("{$this->muridName} mengajukan {$this->absensi->status} untuk tanggal " . 
                  \Carbon\Carbon::parse($this->absensi->tanggal)->format('d M Y'))
            ->icon('heroicon-o-document-text')
            ->iconColor('warning')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat')
                    ->url('/admin/pengajuan-izins/' . $this->absensi->id)
                    ->button(),
            ])
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
