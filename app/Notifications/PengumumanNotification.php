<?php

namespace App\Notifications;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengumumanNotification extends Notification
{
    use Queueable;

    protected $pengumuman;

    public function __construct(Pengumuman $pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $iconColor = match($this->pengumuman->prioritas) {
            'tinggi' => 'danger',
            'sedang' => 'warning',
            'rendah' => 'info',
            default => 'primary',
        };

        $icon = match($this->pengumuman->prioritas) {
            'tinggi' => 'heroicon-o-exclamation-triangle',
            'sedang' => 'heroicon-o-megaphone',
            'rendah' => 'heroicon-o-information-circle',
            default => 'heroicon-o-megaphone',
        };

        // Strip HTML tags for notification body
        $isiClean = strip_tags($this->pengumuman->isi);
        $isiPreview = strlen($isiClean) > 150 
            ? substr($isiClean, 0, 150) . '...' 
            : $isiClean;

        return \Filament\Notifications\Notification::make()
            ->title($this->pengumuman->judul)
            ->body($isiPreview)
            ->icon($icon)
            ->iconColor($iconColor)
            ->getDatabaseMessage();
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
