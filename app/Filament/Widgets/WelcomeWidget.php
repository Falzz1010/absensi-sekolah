<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-widget';
    protected static ?int $sort = -1; // Tampil paling atas
    protected int | string | array $columnSpan = 'full';
    
    public static function canView(): bool
    {
        return true; // Always visible
    }

    public function getGreeting(): string
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Selamat Pagi';
        } elseif ($hour < 15) {
            return 'Selamat Siang';
        } elseif ($hour < 18) {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }

    public function getUserName(): string
    {
        return auth()->user()->name;
    }

    public function getUserRole(): string
    {
        $roles = auth()->user()->roles->pluck('name')->toArray();
        
        if (in_array('admin', $roles)) {
            return 'Administrator';
        } elseif (in_array('guru', $roles)) {
            return 'Guru';
        } elseif (in_array('murid', $roles)) {
            return 'Siswa';
        }
        
        return 'User';
    }

    public function getCurrentDate(): string
    {
        return now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }
}
