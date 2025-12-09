<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources\AbsensiResource;

// Route::middleware(['auth'])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');

//     Route::middleware(['role:guru'])->group(function () {
//         Route::get('/admin/absensi', AbsensiResource::class)->name('absensi.index');
//     });
// });

Route::get('/', function () {
    return view('welcome');
});

// QR Code Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/qr-code/{qrCode}/download', [App\Http\Controllers\QrCodeController::class, 'download'])->name('qr.download');
    Route::get('/qr-code/{qrCode}/view', [App\Http\Controllers\QrCodeController::class, 'view'])->name('qr.view');
});

// File Access Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/files/attendance-proof/{path}', [App\Http\Controllers\FileAccessController::class, 'serveAttendanceProof'])
        ->name('files.attendance-proof')
        ->where('path', '.*');
    Route::get('/files/profile-photo/{path}', [App\Http\Controllers\FileAccessController::class, 'serveProfilePhoto'])
        ->name('files.profile-photo')
        ->where('path', '.*');
});