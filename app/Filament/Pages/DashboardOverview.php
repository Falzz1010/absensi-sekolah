<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class DashboardOverview extends Dashboard
{
    protected static string $routePath = '/';
    protected static ?string $title = 'Dashboard Overview';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\WelcomeWidget::class,
            \App\Filament\Widgets\VerificationStatusWidget::class,
            \App\Filament\Widgets\StatsOverview::class,
            \App\Filament\Widgets\AbsensiChart::class,
            \App\Filament\Widgets\RekapMingguan::class,
            \App\Filament\Widgets\RekapBulanan::class,
            \App\Filament\Widgets\RankingKehadiranKelas::class,
            \App\Filament\Widgets\IncompleteVerificationTable::class,
        ];
    }
}