@echo off
echo ========================================
echo Starting Real-Time Services
echo ========================================
echo.

echo [1/4] Starting Laravel Reverb Server...
start "Reverb Server" cmd /k "php artisan reverb:start"
timeout /t 2 /nobreak >nul

echo [2/4] Starting Queue Worker...
start "Queue Worker" cmd /k "php artisan queue:work"
timeout /t 2 /nobreak >nul

echo [3/4] Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 2 /nobreak >nul

echo [4/4] Starting Vite Dev Server...
start "Vite Dev" cmd /k "npm run dev"

echo.
echo ========================================
echo All services started!
echo ========================================
echo.
echo Services running:
echo - Reverb Server: http://localhost:8080
echo - Laravel App: http://localhost:8000
echo - Vite Dev: http://localhost:5173
echo.
echo Press any key to exit...
pause >nul
