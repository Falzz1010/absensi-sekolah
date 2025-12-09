<?php

use App\Http\Controllers\Api\QrScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// QR Code Scan API (public endpoint)
Route::post('/qr-scan', [QrScanController::class, 'scan']);
