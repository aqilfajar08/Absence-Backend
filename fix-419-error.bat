@echo off
REM ============================================
REM Fix 419 PAGE EXPIRED Error
REM ============================================

echo.
echo ========================================
echo   FIXING 419 PAGE EXPIRED ERROR
echo ========================================
echo.

REM Set PHP path from XAMPP
set PHP_PATH=C:\xampp\php\php.exe

REM Check if PHP exists
if not exist "%PHP_PATH%" (
    echo [ERROR] PHP not found at %PHP_PATH%
    echo Please install XAMPP or update PHP_PATH in this script
    pause
    exit /b 1
)

echo [STEP 1/5] Creating sessions table migration...
%PHP_PATH% artisan session:table
echo.

echo [STEP 2/5] Running migrations...
%PHP_PATH% artisan migrate
echo.

echo [STEP 3/5] Clearing application cache...
%PHP_PATH% artisan cache:clear
echo.

echo [STEP 4/5] Clearing config cache...
%PHP_PATH% artisan config:clear
echo.

echo [STEP 5/5] Clearing route cache...
%PHP_PATH% artisan route:clear
echo.

echo ========================================
echo   FIX COMPLETED!
echo ========================================
echo.
echo Next steps:
echo 1. Restart your Laravel server (Ctrl+C then run run-server.bat)
echo 2. Clear your browser cache (Ctrl+Shift+Delete)
echo 3. Try logging in again
echo.

pause
