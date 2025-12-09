@echo off
echo ========================================
echo   LARAVEL OCTANE - DEVELOPMENT MODE
echo ========================================
echo.

echo Starting Laravel Octane with auto-reload...
echo.
echo Server: http://localhost:8000
echo Workers: 2 (development mode)
echo Auto-reload: ENABLED
echo.
echo The server will automatically reload when you change files.
echo Press Ctrl+C to stop the server
echo.

php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --workers=2 --watch
