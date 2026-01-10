@echo off
REM ============================================
REM Laravel Setup & Run Script (Using XAMPP)
REM ============================================

echo.
echo ========================================
echo   LARAVEL SETUP - ABSENCE BACKEND
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

echo [INFO] Using PHP: %PHP_PATH%
%PHP_PATH% -v
echo.

REM Check if Composer is installed globally
where composer >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [INFO] Composer found globally
    set COMPOSER_CMD=composer
) else (
    echo [WARNING] Composer not found globally
    echo [INFO] Downloading composer.phar...
    
    REM Download Composer if not exists
    if not exist "composer.phar" (
        %PHP_PATH% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        %PHP_PATH% composer-setup.php
        %PHP_PATH% -r "unlink('composer-setup.php');"
    )
    
    if exist "composer.phar" (
        echo [INFO] Using local composer.phar
        set COMPOSER_CMD=%PHP_PATH% composer.phar
    ) else (
        echo [ERROR] Failed to download Composer
        pause
        exit /b 1
    )
)

echo.
echo ========================================
echo   STEP 1: Installing Dependencies
echo ========================================
echo.

REM Install dependencies
%COMPOSER_CMD% install --no-interaction

if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer install failed
    pause
    exit /b 1
)

echo.
echo ========================================
echo   STEP 2: Checking .env file
echo ========================================
echo.

REM Check if .env exists
if not exist ".env" (
    echo [INFO] Creating .env from .env.example
    copy .env.example .env
) else (
    echo [INFO] .env file already exists
)

echo.
echo ========================================
echo   STEP 3: Generating Application Key
echo ========================================
echo.

REM Generate app key if not set
%PHP_PATH% artisan key:generate --ansi

echo.
echo ========================================
echo   STEP 4: Database Setup
echo ========================================
echo.

echo [INFO] Please make sure:
echo   1. XAMPP MySQL is running
echo   2. Database 'laravel_absence_backend' exists
echo   3. .env file has correct database credentials
echo.

choice /C YN /M "Do you want to run migrations now"
if %ERRORLEVEL% EQU 1 (
    echo [INFO] Running migrations...
    %PHP_PATH% artisan migrate --force
    
    if %ERRORLEVEL% EQU 0 (
        echo [SUCCESS] Migrations completed
        
        choice /C YN /M "Do you want to run seeders"
        if %ERRORLEVEL% EQU 1 (
            echo [INFO] Running seeders...
            %PHP_PATH% artisan db:seed --force
        )
    ) else (
        echo [WARNING] Migrations failed - check database connection
    )
) else (
    echo [INFO] Skipping migrations
)

echo.
echo ========================================
echo   STEP 5: Creating Storage Link
echo ========================================
echo.

%PHP_PATH% artisan storage:link

echo.
echo ========================================
echo   SETUP COMPLETE!
echo ========================================
echo.
echo Your Laravel application is ready!
echo.
echo To start the development server, run:
echo   run-server.bat
echo.
pause
