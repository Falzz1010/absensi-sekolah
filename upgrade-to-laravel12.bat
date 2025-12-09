@echo off
echo ========================================
echo   UPGRADE TO LARAVEL 12
echo ========================================
echo.

echo [1/8] Backup database...
php artisan db:backup
echo.

echo [2/8] Clear all caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo.

echo [3/8] Update Composer dependencies...
composer update
echo.

echo [4/8] Publish Laravel 12 config files...
php artisan vendor:publish --tag=laravel-config --force
echo.

echo [5/8] Run database migrations...
php artisan migrate
echo.

echo [6/8] Clear caches again...
php artisan config:clear
php artisan cache:clear
php artisan optimize
echo.

echo [7/8] Rebuild assets...
npm install
npm run build
echo.

echo [8/8] Run tests...
php artisan test
echo.

echo ========================================
echo   UPGRADE COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Check application in browser
echo 2. Test all features
echo 3. Monitor error logs
echo.
pause
