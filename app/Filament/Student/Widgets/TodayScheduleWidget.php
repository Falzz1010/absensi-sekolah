<?php

namespace App\Filament\Student\Widgets;

use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\Murid;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TodayScheduleWidget extends Widget
{
    protected static string $view = 'filament.student.widgets.today-schedule-widget';
    
    protected int | string | array $columnSpan = 'full';
    
    public function getTodaySchedule(): array
    {
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();
        
        if (!$murid) {
            return [];
        }
        
        // Check if today is a holiday
        $holiday = HariLibur::whereDate('tanggal', today())->first();
        if ($holiday) {
            return ['holiday' => $holiday];
        }
        
        // Get day name in Indonesian
        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        
        $today = $dayNames[now()->format('l')];
        
        // Get schedule for today using kelas string field
        $schedules = Jadwal::where('kelas', $murid->kelas)
            ->where('hari', $today)
            ->with(['guru'])
            ->orderBy('jam_mulai')
            ->get();
        
        return ['schedules' => $schedules];
    }
    
    public function getCurrentClass(): ?Jadwal
    {
        $user = Auth::user();
        $murid = Murid::where('user_id', $user->id)->first();
        
        if (!$murid) {
            return null;
        }
        
        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        
        $today = $dayNames[now()->format('l')];
        $currentTime = now()->format('H:i:s');
        
        return Jadwal::where('kelas', $murid->kelas)
            ->where('hari', $today)
            ->where('jam_mulai', '<=', $currentTime)
            ->where('jam_selesai', '>=', $currentTime)
            ->with(['guru'])
            ->first();
    }
}
