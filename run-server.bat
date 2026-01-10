@echo off
REM ============================================
REM Laravel Development Server (Using XAMPP)
REM ============================================

echo.
echo ========================================
echo   STARTING LARAVEL SERVER
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

REM Check if vendor folder exists
if not exist "vendor" (
    echo [ERROR] Dependencies not installed!
    echo Please run setup.bat first
    pause
    exit /b 1
)

echo [INFO] Starting Laravel development server...
echo [INFO] Server will be available at: http://localhost:8000
echo [INFO] Press Ctrl+C to stop the server
echo.

REM Start Laravel server
%PHP_PATH% artisan serve

pause
