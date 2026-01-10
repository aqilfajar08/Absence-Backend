@echo off
echo ========================================
echo   INSTALLING LARAVEL DEPENDENCIES
echo ========================================
echo.

REM Set PHP path
set PHP=C:\xampp\php\php.exe

echo [1/5] Checking PHP...
%PHP% -v
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PHP not found!
    pause
    exit /b 1
)
echo OK - PHP found
echo.

echo [2/5] Downloading Composer (if needed)...
if not exist "composer.phar" (
    echo Downloading Composer installer...
    %PHP% -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    echo Installing Composer...
    %PHP% composer-setup.php --quiet
    del composer-setup.php
    echo OK - Composer downloaded
) else (
    echo OK - Composer already exists
)
echo.

echo [3/5] Installing Laravel dependencies...
echo This may take 5-10 minutes depending on your internet speed...
echo.
%PHP% composer.phar install --no-interaction --prefer-dist
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Composer install failed!
    pause
    exit /b 1
)
echo OK - Dependencies installed
echo.

echo [4/5] Generating application key...
%PHP% artisan key:generate --ansi
echo OK - App key generated
echo.

echo [5/5] Creating storage link...
%PHP% artisan storage:link
echo.

echo ========================================
echo   INSTALLATION COMPLETE!
echo ========================================
echo.
echo Next steps:
echo 1. Make sure XAMPP MySQL is running
echo 2. Create database 'laravel_absence_backend' in phpMyAdmin
echo 3. Run: migrate-database.bat
echo.
pause
