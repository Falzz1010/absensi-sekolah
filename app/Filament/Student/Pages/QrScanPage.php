<?php

namespace App\Filament\Student\Pages;

use Filament\Pages\Page;

class QrScanPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.student.pages.qr-scan-page';

    protected static ?string $navigationLabel = 'Scan QR';

    protected static ?string $title = 'Scan QR Code';

    protected static ?int $navigationSort = 2;

    public ?string $scanResult = null;
    public ?string $errorMessage = null;
    public bool $isScanning = false;

    public function mount(): void
    {
        $this->scanResult = null;
        $this->errorMessage = null;
        $this->isScanning = false;
    }

    public function getTitle(): string
    {
        return 'Scan QR Code';
    }
}
