# Download FrankenPHP for Windows
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Download FrankenPHP Binary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$url = "https://github.com/dunglas/frankenphp/releases/download/v1.3.5/frankenphp-windows-x86_64.zip"
$zipFile = "frankenphp-windows-x86_64.zip"
$extractPath = "."

Write-Host "[1/3] Downloading FrankenPHP binary (~50MB)..." -ForegroundColor Yellow
Write-Host "URL: $url" -ForegroundColor Gray
Write-Host ""

try {
    # Download with progress
    $ProgressPreference = 'SilentlyContinue'
    Invoke-WebRequest -Uri $url -OutFile $zipFile -UseBasicParsing
    Write-Host "[OK] Download complete!" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "[2/3] Extracting frankenphp.exe..." -ForegroundColor Yellow
    Expand-Archive -Path $zipFile -DestinationPath $extractPath -Force
    Write-Host "[OK] Extraction complete!" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "[3/3] Cleaning up..." -ForegroundColor Yellow
    Remove-Item $zipFile -Force
    Write-Host "[OK] Cleanup complete!" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "FrankenPHP Setup Complete!" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "1. Update .env: OCTANE_SERVER=frankenphp" -ForegroundColor White
    Write-Host "2. Run: php artisan octane:start --server=frankenphp" -ForegroundColor White
    Write-Host ""
    Write-Host "Or use the batch file: start-octane-frankenphp.bat" -ForegroundColor Yellow
    Write-Host ""
    
} catch {
    Write-Host "[ERROR] Failed to download FrankenPHP" -ForegroundColor Red
    Write-Host "Error: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please download manually from:" -ForegroundColor Yellow
    Write-Host "https://github.com/dunglas/frankenphp/releases/latest" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Extract frankenphp.exe to this folder" -ForegroundColor Yellow
}

Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
