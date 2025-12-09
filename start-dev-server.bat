@echo off
echo ========================================
echo Starting Laravel Development Server
echo ========================================
echo.
echo Server akan berjalan di: http://127.0.0.1:8000
echo.
echo Landing Page: http://127.0.0.1:8000
echo Admin Panel:  http://127.0.0.1:8000/admin
echo Student Panel: http://127.0.0.1:8000/student
echo.
echo Tekan Ctrl+C untuk stop server
echo.
php artisan serve --host=127.0.0.1 --port=8000
