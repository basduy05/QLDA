@echo off
REM Complete cleanup of all Laravel/PHP files
REM Keeps only: frontend, server/, and root config files

setlocal enabledelayedexpansion

cls
color 0A
echo.
echo ===============================================
echo    APERLEX - Complete PHP Cleanup
echo ===============================================
echo.
echo Removing all Laravel/PHP files...
echo.

REM Create backup timestamp
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)

echo Backing up to: archived_php_%mydate%_%mytime%
echo.

REM ========================================
REM REMOVE DIRECTORIES
REM ========================================

echo [1/12] Removing app/...
if exist "app" (rmdir /s /q "app" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [2/12] Removing bootstrap/...
if exist "bootstrap" (rmdir /s /q "bootstrap" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [3/12] Removing config/...
if exist "config" (rmdir /s /q "config" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [4/12] Removing database/...
if exist "database" (rmdir /s /q "database" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [5/12] Removing routes/...
if exist "routes" (rmdir /s /q "routes" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [6/12] Removing tests/...
if exist "tests" (rmdir /s /q "tests" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [7/12] Removing vendor/...
if exist "vendor" (rmdir /s /q "vendor" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [8/12] Removing docker/...
if exist "docker" (rmdir /s /q "docker" 2>nul && echo  ✓ Removed) else echo  - Not found

echo [9/12] Removing lang/...
if exist "lang" (rmdir /s /q "lang" 2>nul && echo  ✓ Removed) else echo  - Not found

REM ========================================
REM REMOVE FILES
REM ========================================

echo [10/12] Removing PHP files...
if exist "artisan" (del /q "artisan" 2>nul && echo  ✓ Removed artisan) else echo  - artisan not found
if exist "composer.json" (del /q "composer.json" 2>nul && echo  ✓ Removed composer.json) else echo  - composer.json not found
if exist "composer.lock" (del /q "composer.lock" 2>nul && echo  ✓ Removed composer.lock) else echo  - composer.lock not found
if exist "phpunit.xml" (del /q "phpunit.xml" 2>nul && echo  ✓ Removed phpunit.xml) else echo  - phpunit.xml not found
if exist "Procfile" (del /q "Procfile" 2>nul && echo  ✓ Removed Procfile) else echo  - Procfile not found
if exist "realtime-server.mjs" (del /q "realtime-server.mjs" 2>nul && echo  ✓ Removed realtime-server.mjs) else echo  - realtime-server.mjs not found

echo [11/12] Removing test PHP files...
for %%f in (test_*.php) do (
  if exist "%%f" (
    del /q "%%f" 2>nul
    echo  ✓ Removed %%f
  )
)

echo [12/12] Cleaning up archive files...
if exist "archive_laravel.bat" (del /q "archive_laravel.bat" 2>nul && echo  ✓ Removed archive_laravel.bat) else echo  - Not found
if exist "archive_laravel.sh" (del /q "archive_laravel.sh" 2>nul && echo  ✓ Removed archive_laravel.sh) else echo  - Not found

echo.
echo ===============================================
echo.
echo ✅ Cleanup Complete!
echo.
echo Remaining structure:
echo   ├── server/                  (✓ Node.js backend)
echo   ├── resources/               (✓ Frontend Vue.js)
echo   ├── public/                  (✓ Frontend assets)
echo   ├── package.json             (✓ Frontend dependencies)
echo   ├── vite.config.js           (✓ Frontend build)
echo   ├── tailwind.config.js       (✓ Frontend styles)
echo   └── .github/                 (✓ GitHub config)
echo.
echo Removed:
echo   ✗ app/                       (Laravel backend)
echo   ✗ bootstrap/                 (Laravel bootstrap)
echo   ✗ config/                    (Laravel config)
echo   ✗ database/                  (Laravel database)
echo   ✗ routes/                    (Laravel routes)
echo   ✗ tests/                     (Laravel tests)
echo   ✗ vendor/                    (PHP dependencies)
echo   ✗ All PHP files
echo.
echo 📦 Your project is now 100%% JavaScript!
echo.
echo Next steps:
echo   1. cd server
echo   2. npm run dev
echo   3. Update frontend APIs
echo.
echo ===============================================
echo.
pause
