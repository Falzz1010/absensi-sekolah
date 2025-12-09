@echo off
echo ========================================
echo Setup FrankenPHP untuk Laravel Octane
echo ========================================
echo.

echo [1/3] Downloading FrankenPHP binary...
echo.
echo CATATAN: FrankenPHP binary (~50MB) akan didownload dari GitHub
echo URL: https://github.com/dunglas/frankenphp/releases/latest
echo.
echo Silakan download manual dari:
echo https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-windows-x86_64.zip
echo.
echo Setelah download:
echo 1. Extract file frankenphp.exe ke folder root project ini
echo 2. Jalankan: php artisan octane:start --server=frankenphp
echo.

echo [2/3] Checking PHP extensions...
php -m | findstr /C:"pdo_mysql" >nul
if %errorlevel% equ 0 (
    echo [OK] PDO MySQL extension loaded
) else (
    echo [WARNING] PDO MySQL extension not found
)

php -m | findstr /C:"mbstring" >nul
if %errorlevel% equ 0 (
    echo [OK] Mbstring extension loaded
) else (
    echo [WARNING] Mbstring extension not found
)

echo.
echo [3/3] Alternative: Use RoadRunner instead
echo.
echo RoadRunner lebih mudah di-install otomatis:
echo   php artisan octane:install --server=roadrunner
echo.
echo Kemudian jalankan:
echo   php artisan octane:start
echo.

pause
