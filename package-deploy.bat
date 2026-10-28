@echo off
setlocal EnableExtensions EnableDelayedExpansion
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line - Build DEPLOY package
echo   (shared PHP hosting, no SSH needed)
echo ========================================
echo.

set "OUTDIR=%CD%\dist"
set "STAGE=%OUTDIR%\SmartLine_Deploy"
set "ZIP=%OUTDIR%\SmartLine_Deploy.zip"

if not exist "%OUTDIR%" mkdir "%OUTDIR%"
if exist "%STAGE%" rmdir /s /q "%STAGE%"
mkdir "%STAGE%"
if exist "%ZIP%" del /f /q "%ZIP%"

echo [1/4] Frontend build check...
if not exist "public\build\manifest.json" (
    where npm >nul 2>&1
    if errorlevel 1 (
        echo [ERROR] public\build missing and npm not found. Run "npm install & npm run build" first.
        pause
        exit /b 1
    )
    echo ... running npm install ^+ build
    call npm install --ignore-scripts
    call npm run build
) else (
    echo [OK] public\build already present
)

echo.
echo [2/4] Copying project (incl. vendor)...
robocopy "%CD%" "%STAGE%" /E /XJ /R:1 /W:1 /NFL /NDL /NJH /NJS /NC /NS /NP ^
  /XD vendor\node_modules node_modules .git .cursor .claude .ai .codex dist ^
  "storage\logs" "storage\framework\cache" "storage\framework\sessions" "storage\framework\views" ^
  /XF .env .env.backup *.log *.zip database.sqlite SmartLine_Store_Products_v4.xlsx ^
  docker-compose.yml Dockerfile start.bat stop.bat start-docker.bat stop-docker.bat ^
  restart.bat setup.bat package-for-client.bat package-deploy.bat

if !ERRORLEVEL! GEQ 8 (
    echo [ERROR] robocopy failed with code !ERRORLEVEL!
    pause
    exit /b 1
)

echo.
echo [3/4] Preparing ready-to-edit .env (from .env.production)...
copy /y ".env.production" "%STAGE%\.env" >nul
REM keep .env.production as a clean reference too (already copied above)

REM Recreate empty writable storage dirs so Laravel boots cleanly
mkdir "%STAGE%\storage\framework\cache" >nul 2>&1
mkdir "%STAGE%\storage\framework\sessions" >nul 2>&1
mkdir "%STAGE%\storage\framework\views" >nul 2>&1
mkdir "%STAGE%\storage\logs" >nul 2>&1
mkdir "%STAGE%\storage\app\public" >nul 2>&1
echo. > "%STAGE%\storage\logs\.gitignore"
REM Remove the local sqlite db if it leaked through
if exist "%STAGE%\database\database.sqlite" del /f /q "%STAGE%\database\database.sqlite"

echo.
echo [4/4] Creating ZIP...
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Compress-Archive -Path '%STAGE%\*' -DestinationPath '%ZIP%' -Force; $s=(Get-Item '%ZIP%').Length/1MB; Write-Host ('Created: %ZIP%'); Write-Host ('Size MB: ' + [math]::Round($s,2))"

if errorlevel 1 (
    echo [ERROR] ZIP failed
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Done
echo   ZIP : %ZIP%
echo.
echo   Next for the client:
echo   1) Upload SmartLine_Deploy to the server
echo   2) Set web root to .../public
echo   3) Edit .env (DB credentials + APP_URL)
echo   4) Open https://DOMAIN/setup.php
echo   5) See DEPLOY_SHARED_HOSTING_AR.md
echo ========================================
echo.
explorer "%OUTDIR%"
pause
