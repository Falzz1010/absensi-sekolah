<?php

namespace App\Filament\Student\Widgets;

use App\Models\Absensi;
use App\Models\Murid;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodayAttendanceWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.today-attendance-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getTodayAttendance(): ?Absensi
    {
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();
        
        if (!$murid) {
            return null;
        }
        
        return Absensi::where('murid_id', $murid->id)
            ->whereDate('tanggal', today())
            ->first();
    }
    
    public function getMurid(): ?Murid
    {
        $user = Auth::user();
        return Murid::where('user_id', $user->id)->first();
    }
}
