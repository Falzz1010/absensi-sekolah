@echo off
echo ========================================
echo Starting Laravel Octane with FrankenPHP
echo ========================================
echo.

REM Check if frankenphp.exe exists
if not exist "frankenphp.exe" (
    echo [ERROR] frankenphp.exe not found!
    echo.
    echo Please download FrankenPHP first:
    echo   Option 1: Run download-frankenphp.ps1
    echo   Option 2: Download manually from GitHub
    echo.
    echo To download automatically, run:
    echo   powershell -ExecutionPolicy Bypass -File download-frankenphp.ps1
    echo.
    pause
    exit /b 1
)

echo [OK] FrankenPHP binary found
echo.
echo Server akan berjalan di: http://127.0.0.1:8000
echo.
echo Landing Page: http://127.0.0.1:8000
echo Admin Panel:  http://127.0.0.1:8000/admin
echo Student Panel: http://127.0.0.1:8000/student
echo.
echo Performance: 15-30x faster than standard PHP server!
echo.
echo Tekan Ctrl+C untuk stop server
echo.

php artisan octane:start --server=frankenphp --host=127.0.0.1 --port=8000 --workers=4
