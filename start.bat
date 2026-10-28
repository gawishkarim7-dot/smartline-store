@echo off
setlocal EnableExtensions EnableDelayedExpansion
chcp 65001 >nul
cd /d "%~dp0"

echo ========================================
echo   Smart Line Store - تشغيل عادي
echo   (PHP + MySQL محلي / بدون Docker)
echo ========================================
echo.
echo Project: %CD%
echo.

REM ---------- Find PHP ----------
set "PHP_EXE="
where php >nul 2>&1 && for /f "delims=" %%P in ('where php') do (
  if not defined PHP_EXE set "PHP_EXE=%%P"
)

if not defined PHP_EXE if exist "C:\laragon\bin\php\php-8.4*\php.exe" (
  for /d %%D in ("C:\laragon\bin\php\php-8.4*") do if exist "%%D\php.exe" set "PHP_EXE=%%D\php.exe"
)
if not defined PHP_EXE if exist "C:\laragon\bin\php\php-8.3*\php.exe" (
  for /d %%D in ("C:\laragon\bin\php\php-8.3*") do if exist "%%D\php.exe" set "PHP_EXE=%%D\php.exe"
)
if not defined PHP_EXE (
  for /d %%D in ("C:\laragon\bin\php\php-*") do if exist "%%D\php.exe" set "PHP_EXE=%%D\php.exe"
)
if not defined PHP_EXE if exist "C:\xampp\php\php.exe" set "PHP_EXE=C:\xampp\php\php.exe"

if not defined PHP_EXE (
  echo [ERROR] PHP غير موجود.
  echo.
  echo ثبّت Laragon من: https://laragon.org/download/
  echo ثم من Laragon اضغط Start All
  echo وبعدها دبل كليك على start.bat مرة أخرى
  echo.
  echo المطلوب: PHP 8.3 أو 8.4 + MySQL + Composer
  echo.
  echo لو عندك Docker استخدم: start-docker.bat
  pause
  exit /b 1
)

echo [OK] PHP: %PHP_EXE%
"%PHP_EXE%" -v
echo.

REM ---------- Find Composer ----------
set "COMPOSER_CMD="
where composer >nul 2>&1 && set "COMPOSER_CMD=composer"
if not defined COMPOSER_CMD if exist "C:\laragon\bin\composer\composer.bat" set "COMPOSER_CMD=C:\laragon\bin\composer\composer.bat"
if not defined COMPOSER_CMD if exist "C:\ProgramData\ComposerSetup\bin\composer.bat" set "COMPOSER_CMD=C:\ProgramData\ComposerSetup\bin\composer.bat"

if not defined COMPOSER_CMD (
  echo [INFO] Composer غير موجود — جاري تنزيله داخل المشروع...
  if not exist "composer.phar" (
    powershell -NoProfile -ExecutionPolicy Bypass -Command "Invoke-WebRequest -Uri 'https://getcomposer.org/download/latest-stable/composer.phar' -OutFile 'composer.phar'"
    if errorlevel 1 (
      echo [ERROR] فشل تنزيل Composer
      pause
      exit /b 1
    )
  )
  set "COMPOSER_CMD=%PHP_EXE% composer.phar"
)

echo [OK] Composer: %COMPOSER_CMD%
echo.

if not exist ".env" (
  copy ".env.example" ".env" >nul
  echo [OK] تم إنشاء .env
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$p='.env'; $c=Get-Content $p -Raw; $c=$c -replace '(?m)^APP_URL=.*','APP_URL=http://localhost:8007'; $c=$c -replace '(?m)^DB_CONNECTION=.*','DB_CONNECTION=mysql'; $c=$c -replace '(?m)^DB_HOST=.*','DB_HOST=127.0.0.1'; $c=$c -replace '(?m)^DB_PORT=.*','DB_PORT=3306'; $c=$c -replace '(?m)^DB_DATABASE=.*','DB_DATABASE=smartline_store'; Set-Content -Path $p -Value $c -NoNewline"

echo.
echo [1/5] composer install...
call %COMPOSER_CMD% install --no-interaction --prefer-dist
if errorlevel 1 (
  echo [ERROR] composer install فشل
  pause
  exit /b 1
)

echo.
echo [2/5] key + storage...
"%PHP_EXE%" artisan key:generate --force
"%PHP_EXE%" artisan storage:link --force

echo.
echo [3/5] إنشاء قاعدة البيانات إن أمكن...
set "MYSQL_EXE="
where mysql >nul 2>&1 && for /f "delims=" %%M in ('where mysql') do if not defined MYSQL_EXE set "MYSQL_EXE=%%M"
if not defined MYSQL_EXE if exist "C:\laragon\bin\mysql\mysql-8*\bin\mysql.exe" (
  for /d %%D in ("C:\laragon\bin\mysql\mysql-8*") do if exist "%%D\bin\mysql.exe" set "MYSQL_EXE=%%D\bin\mysql.exe"
)
if not defined MYSQL_EXE if exist "C:\xampp\mysql\bin\mysql.exe" set "MYSQL_EXE=C:\xampp\mysql\bin\mysql.exe"

if defined MYSQL_EXE (
  "%MYSQL_EXE%" -u root -e "CREATE DATABASE IF NOT EXISTS smartline_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
  if errorlevel 1 "%MYSQL_EXE%" -u root -proot -e "CREATE DATABASE IF NOT EXISTS smartline_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
) else (
  echo [WARN] أنشئ يدوياً قاعدة: smartline_store
)

echo.
echo [4/5] migrate + seed...
"%PHP_EXE%" artisan migrate --force
if errorlevel 1 (
  echo.
  echo [ERROR] فشل الاتصال بـ MySQL
  echo - شغّل MySQL من Laragon ^(Start All^) أو XAMPP
  echo - عدّل DB_USERNAME و DB_PASSWORD في .env
  echo - تأكد من وجود قاعدة smartline_store
  pause
  exit /b 1
)
"%PHP_EXE%" artisan db:seed --force
if errorlevel 1 echo [WARN] seed قد يكون تم سابقاً

if not exist "public\build\manifest.json" (
  where npm >nul 2>&1
  if not errorlevel 1 (
    echo [INFO] npm run build...
    call npm install
    call npm run build
  )
)

echo.
echo [5/5] السيرفر...
echo ========================================
echo   المتجر : http://localhost:8007
echo   الإدارة: http://localhost:8007/admin
echo   الدخول : admin@smartlinesa.net / SmartLine@2026
echo   إيقاف  : Ctrl+C
echo ========================================
echo.

"%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8007
pause
