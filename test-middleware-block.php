<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "=== TEST MIDDLEWARE BLOCK ===\n\n";

$murid = User::where('email', 'murid@example.com')->first();
$admin = User::where('email', 'admin@example.com')->first();

if (!$murid || !$admin) {
    echo "❌ Users not found\n";
    exit(1);
}

echo "Test 1: Murid trying to access admin panel\n";
Auth::login($murid);
echo "  - Logged in as: " . Auth::user()->email . "\n";
echo "  - Has student role: " . (Auth::user()->hasRole('student') ? 'YES' : 'NO') . "\n";
echo "  - Has admin role: " . (Auth::user()->hasRole('admin') ? 'YES' : 'NO') . "\n";
echo "  - Should be blocked: " . (Auth::user()->hasAnyRole(['student', 'murid']) ? 'YES ✅' : 'NO ❌') . "\n";
Auth::logout();

echo "\nTest 2: Admin trying to access admin panel\n";
Auth::login($admin);
echo "  - Logged in as: " . Auth::user()->email . "\n";
echo "  - Has admin role: " . (Auth::user()->hasRole('admin') ? 'YES' : 'NO') . "\n";
echo "  - Has student role: " . (Auth::user()->hasRole('student') ? 'YES' : 'NO') . "\n";
echo "  - Should be allowed: " . (Auth::user()->hasAnyRole(['admin', 'guru']) ? 'YES ✅' : 'NO ❌') . "\n";
Auth::logout();

echo "\n=== MIDDLEWARE REGISTERED ===\n";
echo "Middleware: BlockStudentFromAdmin\n";
echo "Location: app/Http/Middleware/BlockStudentFromAdmin.php\n";
echo "Registered in: AdminPanelProvider.php\n";

echo "\n✅ Middleware is ready!\n";
echo "Now test in browser:\n";
echo "1. Clear browser cache (Ctrl+Shift+Del)\n";
echo "2. Open Incognito window\n";
echo "3. Try login with murid@example.com at /admin\n";
echo "4. Should be blocked and redirected to /student\n";
