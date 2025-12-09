<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Murid;

echo "=== CHECK STUDENT PANEL ACCESS ===\n\n";

$user = User::where('email', 'murid@example.com')->first();

if (!$user) {
    echo "❌ User tidak ditemukan\n";
    exit(1);
}

echo "1. USER INFO\n";
echo "   Name: {$user->name}\n";
echo "   Email: {$user->email}\n";
echo "   ID: {$user->id}\n\n";

echo "2. ROLES\n";
$roles = $user->roles->pluck('name')->toArray();
echo "   Roles: " . implode(', ', $roles) . "\n";
echo "   Has 'murid' role: " . ($user->hasRole('murid') ? 'YES' : 'NO') . "\n";
echo "   Has 'student' role: " . ($user->hasRole('student') ? 'YES' : 'NO') . "\n";
echo "   Has 'admin' role: " . ($user->hasRole('admin') ? 'YES' : 'NO') . "\n";
echo "   Has 'guru' role: " . ($user->hasRole('guru') ? 'YES' : 'NO') . "\n\n";

echo "3. MURID RECORD\n";
$murid = Murid::where('user_id', $user->id)->first();
if ($murid) {
    echo "   ✓ Murid record exists\n";
    echo "   Murid ID: {$murid->id}\n";
    echo "   Murid Name: {$murid->name}\n";
} else {
    echo "   ❌ Murid record NOT found\n";
}
echo "\n";

echo "4. ACCESS CHECK (canAccessPanel)\n";

// Check condition 1: Has student/murid role
$hasStudentRole = $user->hasAnyRole(['student', 'murid']);
echo "   Condition 1 - Has student/murid role: " . ($hasStudentRole ? '✓ YES' : '❌ NO') . "\n";

// Check condition 2: Does NOT have admin/guru role
$hasAdminRole = $user->hasAnyRole(['admin', 'guru']);
echo "   Condition 2 - Does NOT have admin/guru role: " . (!$hasAdminRole ? '✓ YES' : '❌ NO (has admin/guru)') . "\n";

// Check condition 3: Has Murid record
$hasMuridRecord = Murid::where('user_id', $user->id)->exists();
echo "   Condition 3 - Has Murid record: " . ($hasMuridRecord ? '✓ YES' : '❌ NO') . "\n\n";

// Final result
$canAccess = $hasStudentRole && !$hasAdminRole && $hasMuridRecord;
echo "5. RESULT\n";
if ($canAccess) {
    echo "   ✓ User CAN access Student Panel\n";
} else {
    echo "   ❌ User CANNOT access Student Panel\n";
    echo "\n   REASON:\n";
    if (!$hasStudentRole) {
        echo "   - Missing student/murid role\n";
    }
    if ($hasAdminRole) {
        echo "   - Has admin/guru role (blocked)\n";
    }
    if (!$hasMuridRecord) {
        echo "   - Missing Murid record\n";
    }
}

echo "\n=== CHECK SELESAI ===\n";
