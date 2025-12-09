<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.welcome-widget';
    protected static ?int $sort = -1;
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

    public function getUserClass(): string
    {
        $murid = auth()->user()->murid;
        return $murid ? $murid->kelas : '-';
    }

    public function getCurrentDate(): string
    {
        return now()->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }
}
