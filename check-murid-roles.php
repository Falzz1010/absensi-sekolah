<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$murid = User::where('email', 'murid@example.com')->first();

if (!$murid) {
    echo "❌ User murid@example.com not found!\n";
    exit(1);
}

echo "=== CHECKING MURID USER ===\n\n";
echo "Email: " . $murid->email . "\n";
echo "Name: " . $murid->name . "\n";
echo "ID: " . $murid->id . "\n\n";

echo "Roles:\n";
$roles = $murid->getRoleNames()->toArray();
foreach ($roles as $role) {
    echo "  - " . $role . "\n";
}

echo "\nRole Checks:\n";
echo "  hasRole('admin'): " . ($murid->hasRole('admin') ? 'YES ⚠️' : 'NO ✅') . "\n";
echo "  hasRole('guru'): " . ($murid->hasRole('guru') ? 'YES ⚠️' : 'NO ✅') . "\n";
echo "  hasRole('student'): " . ($murid->hasRole('student') ? 'YES ✅' : 'NO ❌') . "\n";
echo "  hasRole('murid'): " . ($murid->hasRole('murid') ? 'YES ✅' : 'NO ❌') . "\n";

echo "\nhasAnyRole(['student', 'murid']): " . ($murid->hasAnyRole(['student', 'murid']) ? 'YES ✅' : 'NO ❌') . "\n";
echo "hasAnyRole(['admin', 'guru']): " . ($murid->hasAnyRole(['admin', 'guru']) ? 'YES ⚠️ PROBLEM!' : 'NO ✅') . "\n";

echo "\n=== PROBLEM DIAGNOSIS ===\n";
if ($murid->hasAnyRole(['admin', 'guru'])) {
    echo "❌ PROBLEM: Murid has admin or guru role!\n";
    echo "   This is why they can access admin panel.\n";
    echo "   Need to remove admin/guru role from this user.\n";
} else {
    echo "✅ Roles are correct. Problem might be:\n";
    echo "   1. Browser cache/session\n";
    echo "   2. SPA mode caching\n";
    echo "   3. Different user being tested\n";
}
