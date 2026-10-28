@echo off
setlocal EnableExtensions
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line Store - إيقاف
echo ========================================
echo.
echo لو شغّلت بـ start.bat:
echo   اضغط Ctrl+C في نافذة السيرفر
echo.
echo لو شغّلت بـ start-docker.bat:
echo   استخدم stop-docker.bat
echo.
pause
