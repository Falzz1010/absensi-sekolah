<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeController extends Controller
{
    public function download(QrCode $qrCode)
    {
        try {
            // Try PNG first (requires imagick)
            $qrCodeImage = QrCodeGenerator::format('png')
                ->size(400)
                ->margin(2)
                ->generate($qrCode->code);

            return response($qrCodeImage)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="qr-' . $qrCode->nama . '.png"');
        } catch (\Exception $e) {
            // Fallback to SVG (no imagick needed)
            $qrCodeImage = QrCodeGenerator::format('svg')
                ->size(400)
                ->margin(2)
                ->generate($qrCode->code);

            return response($qrCodeImage)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="qr-' . $qrCode->nama . '.svg"');
        }
    }

    public function view(QrCode $qrCode)
    {
        // Generate QR Code untuk preview
        $qrCodeImage = QrCodeGenerator::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($qrCode->code);

        return view('qr-code.view', [
            'qrCode' => $qrCode,
            'qrCodeImage' => $qrCodeImage,
        ]);
    }
}
