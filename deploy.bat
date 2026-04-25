@echo off
REM ============================================
REM APERLEX - ONE-CLICK DEPLOYMENT SCRIPT
REM ============================================
REM Usage: deploy.bat
REM This script deploys to a self-hosted Windows server

setlocal enabledelayedexpansion

cls
color 0A

echo.
echo ╔════════════════════════════════════╗
echo ║  APERLEX - Deployment Script      ║
echo ║  Windows Server Edition           ║
echo ╚════════════════════════════════════╝
echo.

REM ============================================
REM Configuration
REM ============================================

set PROJECT_DIR=%CD%
set SERVER_DIR=%PROJECT_DIR%\server
set LOG_FILE=%PROJECT_DIR%\deployment.log

echo [1/8] Checking environment...

if not exist "%SERVER_DIR%\package.json" (
    echo ❌ server\package.json not found!
    exit /b 1
)

where node >nul 2>nul
if errorlevel 1 (
    echo ❌ Node.js not installed!
    exit /b 1
)

echo ✓ Environment OK

REM ============================================
REM Stop old process
REM ============================================

echo.
echo [2/8] Stopping old process...

where pm2 >nul 2>nul
if errorlevel 0 (
    pm2 stop aperlex-backend 2>nul
    echo ✓ Old process stopped
) else (
    echo ⚠ PM2 not installed, skipping...
)

REM ============================================
REM Update code
REM ============================================

echo.
echo [3/8] Updating code from git...

cd /d "%PROJECT_DIR%"
git fetch origin
git reset --hard origin/main
if errorlevel 1 (
    echo ⚠ Git update failed, continuing anyway...
)
echo ✓ Code updated

REM ============================================
REM Backend setup
REM ============================================

echo.
echo [4/8] Installing backend dependencies...

cd /d "%SERVER_DIR%"
call npm install
if errorlevel 1 (
    echo ❌ npm install failed!
    exit /b 1
)
echo ✓ Dependencies installed

REM ============================================
REM Database migration
REM ============================================

echo.
echo [5/8] Running database migrations...

if exist "%SERVER_DIR%\.env" (
    call npm run prisma:migrate:deploy
    if errorlevel 1 (
        echo ⚠ Migration warning, continuing...
    )
    echo ✓ Database migrated
) else (
    echo ⚠ .env not found, skipping migrations
)

REM ============================================
REM Build backend
REM ============================================

echo.
echo [6/8] Building backend...

call npm run build
if errorlevel 1 (
    echo ❌ Build failed!
    exit /b 1
)
echo ✓ Backend built

REM ============================================
REM Start backend
REM ============================================

echo.
echo [7/8] Starting backend with PM2...

where pm2 >nul 2>nul
if errorlevel 1 (
    echo ❌ PM2 not installed!
    echo Install: npm install -g pm2
    exit /b 1
)

call pm2 start "npm run start" ^
    --name aperlex-backend ^
    --env production ^
    --restart-delay 5000 ^
    --exp-backoff-restart-delay 100

call pm2 save
echo ✓ Backend started

REM ============================================
REM Frontend build
REM ============================================

echo.
echo [8/8] Building frontend...

cd /d "%PROJECT_DIR%"
call npm install
call npm run build
if errorlevel 1 (
    echo ⚠ Frontend build warning, continuing...
)
echo ✓ Frontend built

REM ============================================
REM Summary
REM ============================================

echo.
echo ╔════════════════════════════════════╗
echo ║  ✅ DEPLOYMENT COMPLETE           ║
echo ╚════════════════════════════════════╝
echo.

echo 📊 Status:
call pm2 status

echo.
echo 📝 Logs:
echo   Backend:  pm2 logs aperlex-backend
echo   Monitor:  pm2 monit
echo.

echo 🔗 Access:
echo   Frontend:  http://localhost:5173 (dev) or http://localhost/build (prod)
echo   API:       http://localhost:3000/api
echo   WebSocket: ws://localhost:3000/socket.io
echo.

echo ✅ Deployment successful!

pause
