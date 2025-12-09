@echo off
echo ========================================
echo   LARAVEL OCTANE - HIGH PERFORMANCE
echo ========================================
echo.

echo Checking Octane installation...
php artisan octane:status 2>nul
if errorlevel 1 (
    echo.
    echo [WARNING] Octane server not running or binary not found.
    echo.
    echo Please download RoadRunner binary first:
    echo https://github.com/roadrunner-server/roadrunner/releases/latest
    echo.
    echo Extract rr.exe to this folder, then run this script again.
    echo.
    pause
    exit /b 1
)

echo.
echo Starting Laravel Octane with RoadRunner...
echo.
echo Server will run on: http://localhost:8000
echo Workers: 4
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --workers=4
