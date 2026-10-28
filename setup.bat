@echo off
setlocal EnableExtensions
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line Store - First Setup
echo ========================================
echo.
echo Normally you only need start.bat.
echo Use this file if you want to install packages first.
echo.

docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker is not running. Start Docker Desktop first.
    pause
    exit /b 1
)

set "HOST_PATH=%CD%"
set "HOST_PATH=%HOST_PATH:\=/%"

echo [1/3] composer install...
docker run --rm -v "%HOST_PATH%:/app" -w /app composer:2 install --no-interaction --prefer-dist
if errorlevel 1 (
    echo [ERROR] composer install failed
    pause
    exit /b 1
)

echo.
echo [2/3] .env...
if not exist ".env" (
    copy ".env.example" ".env" >nul
    echo [OK] .env created
) else (
    echo [INFO] .env already exists
)

echo.
echo [3/3] Done. Now double-click start.bat
echo.
pause
