<?php

/**
 * Test Widget Authorization
 * 
 * This script tests if widgets are properly authorized for each panel.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TEST WIDGET AUTHORIZATION ===\n\n";

// Get test users
$admin = User::where('email', 'admin@example.com')->first();
$murid = User::where('email', 'murid@example.com')->first();

if (!$admin || !$murid) {
    echo "❌ ERROR: Test users not found. Please run seeders first.\n";
    exit(1);
}

// Admin widgets
$adminWidgets = [
    'App\Filament\Widgets\StatsOverview',
    'App\Filament\Widgets\AbsensiChart',
    'App\Filament\Widgets\RekapMingguan',
    'App\Filament\Widgets\RekapBulanan',
    'App\Filament\Widgets\RankingKehadiranKelas',
    'App\Filament\Widgets\RekapAbsensiKelas',
];

// Student widgets
$studentWidgets = [
    'App\Filament\Student\Widgets\TodayAttendanceWidget',
    'App\Filament\Student\Widgets\NotificationsWidget',
    'App\Filament\Student\Widgets\AttendanceSummaryWidget',
    'App\Filament\Student\Widgets\TodayScheduleWidget',
];

echo "📋 Testing Admin Widgets Authorization\n";
echo "========================================\n\n";

foreach ($adminWidgets as $widget) {
    $widgetName = class_basename($widget);
    
    // Test as admin
    Auth::login($admin);
    $canViewAsAdmin = method_exists($widget, 'canView') ? $widget::canView() : true;
    
    // Test as murid
    Auth::login($murid);
    $canViewAsMurid = method_exists($widget, 'canView') ? $widget::canView() : true;
    
    echo "Widget: {$widgetName}\n";
    echo "  - Admin can view: " . ($canViewAsAdmin ? "✅ YES" : "❌ NO") . "\n";
    echo "  - Murid can view: " . ($canViewAsMurid ? "❌ YES (SECURITY ISSUE!)" : "✅ NO") . "\n";
    
    if ($canViewAsAdmin && !$canViewAsMurid) {
        echo "  - Status: ✅ CORRECT\n";
    } else {
        echo "  - Status: ❌ INCORRECT - Need to add authorization!\n";
    }
    echo "\n";
}

echo "\n📋 Testing Student Widgets Authorization\n";
echo "==========================================\n\n";

foreach ($studentWidgets as $widget) {
    $widgetName = class_basename($widget);
    
    // Test as admin
    Auth::login($admin);
    $canViewAsAdmin = method_exists($widget, 'canView') ? $widget::canView() : true;
    
    // Test as murid
    Auth::login($murid);
    $canViewAsMurid = method_exists($widget, 'canView') ? $widget::canView() : true;
    
    echo "Widget: {$widgetName}\n";
    echo "  - Admin can view: " . ($canViewAsAdmin ? "⚠️  YES (optional)" : "❌ NO") . "\n";
    echo "  - Murid can view: " . ($canViewAsMurid ? "✅ YES" : "❌ NO") . "\n";
    
    if ($canViewAsMurid) {
        echo "  - Status: ✅ CORRECT\n";
    } else {
        echo "  - Status: ❌ INCORRECT - Student should be able to view!\n";
    }
    echo "\n";
}

echo "\n=== SUMMARY ===\n\n";
echo "✅ Expected Results:\n";
echo "   - Admin widgets: Admin ✅, Murid ❌\n";
echo "   - Student widgets: Murid ✅, Admin optional\n\n";

echo "📝 Notes:\n";
echo "   - If admin widgets show 'Murid can view: YES', add canView() method\n";
echo "   - Student widgets don't need authorization (they filter by user_id)\n";
echo "   - Clear cache after changes: php artisan optimize:clear\n";

Auth::logout();
