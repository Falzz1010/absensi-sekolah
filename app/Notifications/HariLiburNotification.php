<?php

namespace App\Notifications;

use App\Models\HariLibur;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class HariLiburNotification extends Notification
{
    use Queueable;

    protected $hariLibur;
    protected $action;

    public function __construct(HariLibur $hariLibur, string $action = 'created')
    {
        $this->hariLibur = $hariLibur;
        $this->action = $action;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $title = $this->action === 'created' ? 'Pengumuman Hari Libur' : 'Update Hari Libur';
        $icon = 'heroicon-o-calendar';
        $iconColor = 'warning';
        
        $body = $this->action === 'created' 
            ? "Hari libur: {$this->hariLibur->nama} pada tanggal " . 
              \Carbon\Carbon::parse($this->hariLibur->tanggal)->format('d M Y')
            : "Hari libur {$this->hariLibur->nama} telah diperbarui";

        if ($this->hariLibur->keterangan) {
            $body .= ". {$this->hariLibur->keterangan}";
        }

        return \Filament\Notifications\Notification::make()
            ->title($title)
            ->body($body)
            ->icon($icon)
            ->iconColor($iconColor)
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
