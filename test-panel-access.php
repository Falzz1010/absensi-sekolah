<?php

/**
 * Test Panel Access Security
 * 
 * This script tests if users can access the correct panels based on their roles.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Filament\Facades\Filament;

echo "=== TEST PANEL ACCESS SECURITY ===\n\n";

// Get test users
$admin = User::where('email', 'admin@example.com')->first();
$guru = User::where('email', 'guru@example.com')->first();
$murid = User::where('email', 'murid@example.com')->first();

if (!$admin || !$guru || !$murid) {
    echo "❌ ERROR: Test users not found. Please run seeders first.\n";
    exit(1);
}

// Get panels
$adminPanel = Filament::getPanel('admin');
$studentPanel = Filament::getPanel('student');

echo "📋 Testing Admin User (admin@example.com)\n";
echo "   - Can access Admin Panel: " . ($admin->canAccessPanel($adminPanel) ? "✅ YES" : "❌ NO") . "\n";
echo "   - Can access Student Panel: " . ($admin->canAccessPanel($studentPanel) ? "❌ YES (SECURITY ISSUE!)" : "✅ NO") . "\n";
echo "   - Roles: " . implode(', ', $admin->getRoleNames()->toArray()) . "\n\n";

echo "📋 Testing Guru User (guru@example.com)\n";
echo "   - Can access Admin Panel: " . ($guru->canAccessPanel($adminPanel) ? "✅ YES" : "❌ NO") . "\n";
echo "   - Can access Student Panel: " . ($guru->canAccessPanel($studentPanel) ? "❌ YES (SECURITY ISSUE!)" : "✅ NO") . "\n";
echo "   - Roles: " . implode(', ', $guru->getRoleNames()->toArray()) . "\n\n";

echo "📋 Testing Murid User (murid@example.com)\n";
echo "   - Can access Admin Panel: " . ($murid->canAccessPanel($adminPanel) ? "❌ YES (SECURITY ISSUE!)" : "✅ NO") . "\n";
echo "   - Can access Student Panel: " . ($murid->canAccessPanel($studentPanel) ? "✅ YES" : "❌ NO") . "\n";
echo "   - Roles: " . implode(', ', $murid->getRoleNames()->toArray()) . "\n";
echo "   - Has Murid Record: " . (\App\Models\Murid::where('user_id', $murid->id)->exists() ? "✅ YES" : "❌ NO") . "\n\n";

// Check routes
echo "=== REGISTERED ROUTES ===\n\n";

$routes = \Illuminate\Support\Facades\Route::getRoutes();

echo "Admin Panel Routes:\n";
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'admin') && str_contains($route->uri(), 'dashboard')) {
        echo "   - " . $route->uri() . " → " . $route->getName() . "\n";
    }
}

echo "\nStudent Panel Routes:\n";
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'student') && str_contains($route->uri(), 'dashboard')) {
        echo "   - " . $route->uri() . " → " . $route->getName() . "\n";
    }
}

echo "\n=== TEST COMPLETE ===\n";
echo "\n✅ Expected Results:\n";
echo "   - Admin: Can access admin panel, CANNOT access student panel\n";
echo "   - Guru: Can access admin panel, CANNOT access student panel\n";
echo "   - Murid: CANNOT access admin panel, Can access student panel\n";
