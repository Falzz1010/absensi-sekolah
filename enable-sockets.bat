@echo off
echo ========================================
echo Enable PHP Sockets Extension
echo ========================================
echo.

echo [1/3] Checking current PHP configuration...
php -m | findstr /C:"sockets" >nul
if %errorlevel% equ 0 (
    echo [OK] Sockets extension is already enabled!
    echo.
    goto :install_roadrunner
) else (
    echo [WARNING] Sockets extension is NOT enabled
    echo.
)

echo [2/3] Locating php.ini file...
php --ini | findstr /C:"Loaded Configuration File"
echo.

echo MANUAL STEPS REQUIRED:
echo.
echo 1. Open php.ini file (location shown above)
echo 2. Find line: ;extension=sockets
echo 3. Remove semicolon: extension=sockets
echo 4. Save file
echo 5. Restart Apache/PHP
echo.
echo Common php.ini locations:
echo   - C:\xampp\php\php.ini
echo   - C:\php\php.ini
echo   - C:\wamp\bin\php\php8.x\php.ini
echo.

pause

echo.
echo [3/3] After enabling sockets, press any key to verify...
pause >nul

php -m | findstr /C:"sockets" >nul
if %errorlevel% equ 0 (
    echo [OK] Sockets extension is now enabled!
    echo.
    goto :install_roadrunner
) else (
    echo [ERROR] Sockets extension still not enabled
    echo Please restart Apache/PHP and try again
    echo.
    pause
    exit /b 1
)

:install_roadrunner
echo.
echo ========================================
echo Ready to Install RoadRunner
echo ========================================
echo.
echo Next steps:
echo   1. composer require spiral/roadrunner-cli spiral/roadrunner-http
echo   2. php artisan octane:install --server=roadrunner
echo   3. php artisan octane:start --server=roadrunner
echo.
echo Or run: install-roadrunner.bat
echo.
pause
