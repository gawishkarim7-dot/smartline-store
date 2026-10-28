@echo off
chcp 65001 >nul
echo ========================================
echo   Smart Line E-Commerce Store - Restart
echo ========================================
echo.

echo [1/2] Stopping services...
call stop.bat

echo.
echo [2/2] Starting services...
call start.bat
