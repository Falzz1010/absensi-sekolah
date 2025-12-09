<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Murid;

// Create test user like in the test
$user = User::factory()->create();
$murid = Murid::factory()->create(['user_id' => $user->id]);
$user->assignRole('murid');

app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
$user = User::find($user->id);

echo "User ID: " . $user->id . "\n";
echo "Has murid role: " . ($user->hasRole('murid') ? 'YES' : 'NO') . "\n";
echo "Has student role: " . ($user->hasRole('student') ? 'YES' : 'NO') . "\n";
echo "hasAnyRole(['student', 'murid']): " . ($user->hasAnyRole(['student', 'murid']) ? 'YES' : 'NO') . "\n";
echo "Murid record exists: " . (Murid::where('user_id', $user->id)->exists() ? 'YES' : 'NO') . "\n";

$panel = Filament\Facades\Filament::getPanel('student');
echo "\ncanAccessPanel('student'): " . ($user->canAccessPanel($panel) ? 'YES' : 'NO') . "\n";

// Cleanup
$murid->delete();
$user->delete();
