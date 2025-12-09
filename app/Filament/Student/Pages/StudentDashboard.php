<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class StudentDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.student.pages.student-dashboard';
    
    protected static ?string $title = 'Dashboard';
    
    protected static ?string $navigationLabel = 'Dashboard';
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Student\Widgets\WelcomeWidget::class,
            \App\Filament\Student\Widgets\TodayAttendanceWidget::class,
            \App\Filament\Student\Widgets\NotificationsWidget::class,
            \App\Filament\Student\Widgets\AttendanceSummaryWidget::class,
            \App\Filament\Student\Widgets\TodayScheduleWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 1;
    }
}
