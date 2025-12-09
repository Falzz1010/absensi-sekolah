<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Absensi;

echo "=== CHECK PENGAJUAN IZIN/SAKIT ===\n\n";

// Get all absensis with verification_status
$pengajuan = Absensi::whereNotNull('verification_status')
    ->with('murid')
    ->latest('created_at')
    ->get();

echo "Total Pengajuan: " . $pengajuan->count() . "\n\n";

if ($pengajuan->isEmpty()) {
    echo "❌ Tidak ada pengajuan ditemukan\n\n";
    
    // Check all absensis
    echo "Checking all absensis...\n";
    $allAbsensi = Absensi::with('murid')->latest()->take(5)->get();
    
    foreach ($allAbsensi as $abs) {
        echo "\nID: {$abs->id}\n";
        echo "Murid: " . ($abs->murid->name ?? 'NULL') . "\n";
        echo "Tanggal: {$abs->tanggal}\n";
        echo "Status: {$abs->status}\n";
        echo "verification_status: " . ($abs->verification_status ?? 'NULL') . "\n";
        echo "proof_document: " . ($abs->proof_document ?? 'NULL') . "\n";
        echo "---\n";
    }
} else {
    echo "✓ Pengajuan ditemukan!\n\n";
    
    foreach ($pengajuan as $p) {
        echo "ID: {$p->id}\n";
        echo "Murid: " . ($p->murid->name ?? 'NULL') . "\n";
        echo "Kelas: {$p->kelas}\n";
        echo "Tanggal: {$p->tanggal}\n";
        echo "Status: {$p->status}\n";
        echo "Verification Status: {$p->verification_status}\n";
        echo "Proof: " . ($p->proof_document ?? 'NULL') . "\n";
        echo "Created: {$p->created_at}\n";
        echo "---\n";
    }
}

// Check query that resource uses
echo "\n=== QUERY RESOURCE ===\n";
$resourceQuery = Absensi::whereNotNull('verification_status')
    ->whereIn('status', ['Sakit', 'Izin'])
    ->with(['murid'])
    ->latest('tanggal')
    ->get();

echo "Total by Resource Query: " . $resourceQuery->count() . "\n";

if ($resourceQuery->isNotEmpty()) {
    echo "\nData yang akan tampil di resource:\n";
    foreach ($resourceQuery as $r) {
        echo "- {$r->murid->name} | {$r->status} | {$r->verification_status}\n";
    }
}

echo "\n=== SELESAI ===\n";
