@echo off
REM ============================================
REM Quick Fix for 419 PAGE EXPIRED Error
REM ============================================

echo.
echo ========================================
echo   QUICK FIX - 419 PAGE EXPIRED
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

echo [INFO] This script will:
echo   1. Clear all Laravel caches
echo   2. Optimize the application
echo.
echo Press any key to continue...
pause > nul
echo.

echo [STEP 1/3] Clearing config cache...
%PHP_PATH% artisan config:clear
if %errorlevel% neq 0 (
    echo [WARNING] Config clear had issues, continuing...
)
echo.

echo [STEP 2/3] Clearing application cache...
%PHP_PATH% artisan cache:clear
if %errorlevel% neq 0 (
    echo [WARNING] Cache clear had issues, continuing...
)
echo.

echo [STEP 3/3] Clearing all caches (optimize:clear)...
%PHP_PATH% artisan optimize:clear
if %errorlevel% neq 0 (
    echo [WARNING] Optimize clear had issues, continuing...
)
echo.

echo ========================================
echo   CACHE CLEARED!
echo ========================================
echo.
echo IMPORTANT NEXT STEPS:
echo.
echo 1. RESTART your Laravel server:
echo    - Press Ctrl+C in the server window
echo    - Run: run-server.bat
echo.
echo 2. CLEAR your browser cache:
echo    - Press Ctrl+Shift+Delete
echo    - Select "Cookies" and "Cached files"
echo    - Click "Clear data"
echo.
echo 3. Try logging in again at:
echo    http://127.0.0.1:8000/login
echo.
echo If still getting error 419, check SOLUSI_ERROR_419.md
echo for alternative solutions (changing SESSION_DRIVER to file)
echo.

pause
