@echo off
setlocal EnableExtensions
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line - Build client ZIP
echo ========================================
echo.

set "OUTDIR=%CD%\dist"
set "STAGE=%OUTDIR%\SmartLine_Store"
set "ZIPPATH=%OUTDIR%\SmartLine_Store_Source.zip"

if not exist "%OUTDIR%" mkdir "%OUTDIR%"
if exist "%STAGE%" rmdir /s /q "%STAGE%"
mkdir "%STAGE%"

echo [1/3] Frontend build check...
where npm >nul 2>&1
if not errorlevel 1 (
    if not exist "public\build\manifest.json" (
        call npm install
        call npm run build
    ) else (
        echo [OK] public\build already present
    )
) else (
    echo [WARN] npm not found - ZIP will rely on existing public\build if present
)

echo.
echo [2/3] Copying clean project files...
robocopy "%CD%" "%STAGE%" /E /NFL /NDL /NJH /NJS /nc /ns /np ^
  /XD vendor node_modules .git .cursor .claude .ai .codex dist ^
  storage\logs storage\framework\cache storage\framework\sessions storage\framework\views ^
  /XF .env .env.backup AGENTS.md CLAUDE.md *.zip

REM robocopy exit codes 0-7 are success
if %ERRORLEVEL% GEQ 8 (
    echo [ERROR] Copy failed with code %ERRORLEVEL%
    pause
    exit /b 1
)

REM ensure empty storage dirs exist for Laravel
mkdir "%STAGE%\storage\framework\cache" >nul 2>&1
mkdir "%STAGE%\storage\framework\sessions" >nul 2>&1
mkdir "%STAGE%\storage\framework\views" >nul 2>&1
mkdir "%STAGE%\storage\logs" >nul 2>&1
mkdir "%STAGE%\storage\app\public" >nul 2>&1
echo. > "%STAGE%\storage\logs\.gitignore"
echo. > "%STAGE%\bootstrap\cache\.gitignore"

echo.
echo [3/3] Creating ZIP...
if exist "%ZIPPATH%" del /f /q "%ZIPPATH%"
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "Compress-Archive -Path '%STAGE%\*' -DestinationPath '%ZIPPATH%' -Force; $s=(Get-Item '%ZIPPATH%').Length/1MB; Write-Host ('Created: %ZIPPATH%'); Write-Host ('Size MB: ' + [math]::Round($s,2))"

if errorlevel 1 (
    echo [ERROR] ZIP failed
    pause
    exit /b 1
)

echo.
echo ========================================
echo   Done
echo   ZIP : %ZIPPATH%
echo   Ask developer to open INSTALL_AR.md
echo   then double-click start.bat
echo ========================================
echo.
explorer "%OUTDIR%"
pause
