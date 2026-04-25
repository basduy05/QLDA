@echo off
REM Colors alternative (Windows doesn't support ANSI by default)
echo.
echo ========================================
echo Aperlex Server Setup
echo ========================================

REM Check Node.js
echo.
echo Checking Node.js...
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Node.js is not installed!
    exit /b 1
)
for /f "tokens=*" %%i in ('node --version') do set NODE_VERSION=%%i
echo OK: Node.js %NODE_VERSION%

REM Check npm
echo.
echo Checking npm...
npm --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: npm is not installed!
    exit /b 1
)
for /f "tokens=*" %%i in ('npm --version') do set NPM_VERSION=%%i
echo OK: npm %NPM_VERSION%

REM Install dependencies
echo.
echo Installing dependencies...
call npm install
if %errorlevel% neq 0 (
    echo Error: Failed to install dependencies
    exit /b 1
)
echo OK: Dependencies installed

REM Create .env if not exists
echo.
echo Setting up environment...
if not exist .env (
    copy .env.example .env
    echo OK: Created .env file (please edit with your database credentials^)
) else (
    echo OK: .env file already exists
)

REM Generate Prisma client
echo.
echo Generating Prisma client...
call npm run prisma:generate
if %errorlevel% neq 0 (
    echo Error: Failed to generate Prisma client
    exit /b 1
)
echo OK: Prisma client generated

REM Build TypeScript
echo.
echo Building TypeScript...
call npm run build
if %errorlevel% neq 0 (
    echo Error: Failed to compile TypeScript
    exit /b 1
)
echo OK: TypeScript compiled

echo.
echo ========================================
echo Setup completed successfully!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env with your database configuration
echo 2. Run: npm run prisma:migrate (to set up database^)
echo 3. Run: npm run dev (to start development server^)
echo.
echo For Docker setup:
echo   docker-compose up -d
echo.
