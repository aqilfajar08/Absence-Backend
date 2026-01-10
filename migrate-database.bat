@echo off
echo ========================================
echo   DATABASE MIGRATION
echo ========================================
echo.

REM Set PHP path
set PHP=C:\xampp\php\php.exe

echo IMPORTANT: Make sure you have:
echo 1. XAMPP MySQL is running
echo 2. Database 'laravel_absence_backend' created in phpMyAdmin
echo.
pause

echo [1/2] Running migrations...
%PHP% artisan migrate --force
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Migration failed!
    echo Please check:
    echo - MySQL is running
    echo - Database exists
    echo - .env credentials are correct
    pause
    exit /b 1
)
echo OK - Migrations completed
echo.

echo [2/2] Running seeders...
%PHP% artisan db:seed --force
if %ERRORLEVEL% NEQ 0 (
    echo WARNING: Seeder failed (this is optional)
)
echo OK - Seeders completed
echo.

echo ========================================
echo   DATABASE SETUP COMPLETE!
echo ========================================
echo.
echo Test users created:
echo - Admin: admin@gmail.com / 12345678
echo - Security: security@gmail.com / 12345678
echo - Employee: employee@gmail.com / 12345678
echo.
echo Now you can run: run-server.bat
echo.
pause
