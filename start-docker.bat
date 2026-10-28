@echo off
chcp 65001 >nul
echo ========================================
echo   Smart Line Store - تشغيل Docker
echo   (يحتاج Docker Desktop)
echo ========================================
echo.

docker info >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Docker غير مفعّل
    echo.
    echo شغّل Docker Desktop من:
    echo - https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

echo [OK] Docker يعمل
echo.

echo [1/6] تشغيل MySQL...
docker container inspect smartline_mysql >nul 2>&1
if errorlevel 1 goto create_mysql

for /f "usebackq delims=" %%S in (`docker container inspect -f "{{.State.Running}}" smartline_mysql`) do set "MYSQL_RUNNING=%%S"
if /i "%MYSQL_RUNNING%"=="true" (
    echo [OK] MySQL يعمل بالفعل
    goto mysql_ready
)

echo [INFO] تشغيل MySQL الموجود...
docker start smartline_mysql >nul 2>&1
echo [OK] MySQL تم تشغيله
goto mysql_ready

:create_mysql
echo [INFO] إنشاء MySQL جديد...
docker run --name smartline_mysql -e MYSQL_ROOT_PASSWORD=root -e MYSQL_DATABASE=smartline_store -e MYSQL_USER=smartline -e MYSQL_PASSWORD=smartline123 -p 3306:3306 -d mysql:8.0
if errorlevel 1 (
    echo [ERROR] فشل إنشاء MySQL
    pause
    exit /b 1
)
echo [OK] MySQL تم إنشاؤه وتشغيله

:mysql_ready

echo.
echo [2/6] انتظار MySQL...
timeout /t 8 /nobreak >nul

echo.
echo [3/6] إعداد الصورة PHP...
docker image inspect smartline-php >nul 2>&1
if errorlevel 1 (
    docker build -t smartline-php .
    if errorlevel 1 (
        echo [ERROR] فشل بناء الصورة
        pause
        exit /b 1
    )
)
echo [OK] الصورة جاهزة

echo.
echo [4/6] إعداد .env...
if not exist .env copy .env.example .env >nul
powershell -NoProfile -Command "$c=Get-Content .env -Raw; $c=$c -replace 'APP_URL=http://localhost:8000','APP_URL=http://localhost:8007'; $c=$c -replace 'DB_CONNECTION=sqlite','DB_CONNECTION=mysql'; $c=$c -replace '(?m)^#\s*DB_HOST=.*','DB_HOST=mysql'; if($c -notmatch '(?m)^DB_HOST='){ $c=$c -replace 'DB_CONNECTION=mysql',"DB_CONNECTION=mysql`nDB_HOST=mysql`nDB_PORT=3306`nDB_DATABASE=smartline_store`nDB_USERNAME=root`nDB_PASSWORD=root" }; $c=$c -replace '(?m)^DB_HOST=.*','DB_HOST=mysql'; $c=$c -replace '(?m)^DB_PORT=.*','DB_PORT=3306'; $c=$c -replace '(?m)^DB_DATABASE=.*','DB_DATABASE=smartline_store'; $c=$c -replace '(?m)^DB_USERNAME=.*','DB_USERNAME=root'; $c=$c -replace '(?m)^DB_PASSWORD=.*','DB_PASSWORD=root'; Set-Content .env $c"
echo [OK] .env جاهز

echo.
echo [5/6] key + migrate + seed...
docker run --rm -v D:/Laialy/SmartLine:/app -w /app --link smartline_mysql:mysql -e DB_HOST=mysql -e DB_DATABASE=smartline_store -e DB_USERNAME=root -e DB_PASSWORD=root smartline-php bash -c "php artisan key:generate --force && php artisan storage:link --force && php artisan migrate --force && php artisan db:seed --force"
if errorlevel 1 (
    echo [ERROR] فشل الإعداد
    pause
    exit /b 1
)
echo [OK] قاعدة البيانات جاهزة

if not exist public\build\manifest.json (
    echo [INFO] بناء الواجهة...
    call npm install
    call npm run build
)

echo.
echo [6/6] تشغيل السيرفر...
docker rm -f smartline_app >nul 2>&1
echo.
echo ========================================
echo   المتجر : http://localhost:8007
echo   الإدارة: http://localhost:8007/admin
echo   الدخول : admin@smartlinesa.net / SmartLine@2026
echo   إيقاف  : Ctrl+C
echo ========================================
echo.

docker run --rm --name smartline_app -v D:/Laialy/SmartLine:/app -w /app -p 8007:8000 --link smartline_mysql:mysql -e DB_HOST=mysql -e DB_DATABASE=smartline_store -e DB_USERNAME=root -e DB_PASSWORD=root smartline-php

pause
