@echo off
setlocal EnableExtensions
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line Store - إيقاف Docker
echo ========================================
echo.

docker stop smartline_app >nul 2>&1
docker rm -f smartline_app >nul 2>&1
echo [OK] App stopped

docker stop smartline_mysql >nul 2>&1
echo [OK] MySQL stopped (data kept)
echo.
pause
