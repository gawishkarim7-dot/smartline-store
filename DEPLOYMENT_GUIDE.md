# دليل نشر متجر Smart Line - خطوة بخطوة

## 📋 ملخص خطوات النشر

1. الدخول على لوحة الدومين/الاستضافة
2. ربط الدومين بالمشروع (DNS / Subdomain)
3. رفع السورس على السيرفر
4. إعداد `.env` + قاعدة البيانات
5. تشغيل `composer` / migrate / seed
6. تفعيل HTTPS
7. تجربة المتجر + الأدمن
8. زر «المتجر» من موقعهم ← للدومين الجديد
9. زر «الموقع الرئيسي» عندنا ← لموقعهم

---

## 🔐 المتطلبات المسبقة

### من العميل:
- [ ] شراء الدومين (مثال: `shop.smartlinesa.net` أو `smartlinesa.net`)
- [ ] بيانات الدخول لـ cPanel / استضافة
  - URL للوحة التحكم
  - Username
  - Password
- [ ] بيانات قاعدة البيانات (يمكن إنشاؤها لاحقاً)
  - Database Name
  - Database Username
  - Database Password
  - Database Host (عادة `localhost`)

### من المطور:
- [ ] مشروع Smart Line جاهز على الجهاز
- [ ] Git repository (اختياري - للنسخ الاحتياطي)
- [ ] FileZilla أو عميل FTP (أو استخدام File Manager في cPanel)
- [ ] محرر نصوص (Notepad++ أو VS Code)

---

## الخطوة 1: الدخول على لوحة الدومين/الاستضافة

### 1.1 الدخول إلى cPanel

```
URL: https://yourdomain.com/cpanel
أو: https://cpanel.yourhostingprovider.com
```

**أمثلة لمزودي الاستضافة الشائعين:**
- GoDaddy: `https://cpanel.godaddy.com`
- Namecheap: `https://cp.namecheap.com`
- Bluehost: `https://my.bluehost.com`
- Hostinger: `https://hpanel.hostinger.com`

### 1.2 التحقق من المتطلبات

في cPanel، ابحث عن **"Select PHP Version"** وتأكد من:
- PHP Version: **8.2 أو أعلى**
- Extensions المطلوبة:
  - pdo_mysql
  - mbstring
  - json
  - openssl
  - tokenizer
  - xml
  - ctype
  - fileinfo
  - zip
  - gd (للصور)

---

## الخطوة 2: ربط الدومين بالمشروع

### 2.1 إنشاء Subdomain (اختياري - إذا استخدم `shop.smartlinesa.net`)

في cPanel:
1. ابحث عن **"Subdomains"**
2. املأ:
   - Subdomain: `shop`
   - Domain: `smartlinesa.net`
   - Document Root: `/public_html/shop` (أو مسار مخصص)
3. اضغط **Create**

### 2.2 إذا استخدم الدومين الرئيسي مباشرة

- احذف أو احفظ محتوى `public_html` القديم
- سيتم رفع المشروع في `public_html`

### 2.3 إعدادات DNS (إذا الدومين مسجل في مزود مختلف)

1. افتح لوحة تحكم الدومين (حيث تم الشراء)
2. اذهب إلى **DNS Management** أو **DNS Records**
3. أضف أو عدل:
   - **Type:** CNAME
   - **Name:** `shop` (أو `@` للدومين الرئيسي)
   - **Value:** سيرفر الاستضافة (مثال: `host123.hostgator.com`)
   - **TTL:** 3600 (أو الافتراضي)

**ملاحظة:** تأخذ تحديثات DNS عادةً من 5 دقائق إلى 48 ساعة (عادةً 10-30 دقيقة)

---

## الخطوة 3: رفع السورس على السيرفر

### 3.1 تحضير المشروع للنشر

على جهازك المحلي:

```bash
cd D:\Laialy\SmartLine

# 1. إزالة الملفات غير الضرورية
rm -rf node_modules
rm -rf vendor
rm -rf .git
rm -rf storage/logs/*.log

# 2. ضغط المشروع
# Windows: انقر يمين على مجلد SmartLine → Send to → Compressed (zipped)
# أو استخدام 7-Zip لضغط أفضل
```

**⚠️ هام:** لا ترفع:
- `.env` (سننشئه على السيرفر)
- `node_modules/`
- `vendor/`
- `.git/`
- ملفات log

### 3.2 الرفع عبر cPanel File Manager

**الطريقة الأسرع:**

1. افتح cPanel
2. ابحث عن **"File Manager"**
3. انتقل إلى المجلد المستهدف:
   - `public_html` للدومين الرئيسي
   - `public_html/shop` للـ subdomain
4. احذف أي محتوى قديم (بعد حفظ نسخة احتياطية!)
5. اضغط **Upload**
6. ارفع ملف zip
7. بعد الرفع، اضغط يمين على الملف → **Extract**
8. احذف ملف zip بعد الاستخراج

### 3.3 أو رفع عبر FTP

استخدم FileZilla:
```
Host: yourdomain.com
Username: cpanel username
Password: cpanel password
Port: 21
```

ثم ارفع جميع الملفات (باستثناء الملفات المذكورة أعلاه)

---

## الخطوة 4: إعداد `.env` + قاعدة البيانات

### 4.1 إنشاء قاعدة البيانات

في cPanel:
1. ابحث عن **"MySQL Database Wizard"**
2. املأ:
   - New Database Name: `smartline_store` (أو أي اسم)
3. إنشاء Database User:
   - Username: `smartline_user`
   - Password: قوية وفريدة (استخدم password generator)
4. إضافة User إلى Database:
   - اختيار **ALL PRIVILEGES**
5. احفظ هذه البيانات:
   ```
   Database Name: smartline_store
   Username: smartline_user
   Password: ********
   Host: localhost
   ```

### 4.2 إنشاء ملف `.env`

في File Manager:
1. اذهب إلى المجلد الرئيسي للمشروع
2. ابحث عن `.env.example`
3. انسخه وأعد تسميته إلى `.env`
4. اضغط **Edit** على `.env`

عدل الإعدادات التالية:

```env
APP_NAME="Smart Line Store"
APP_ENV=production
APP_KEY= (سيتم توليده لاحقاً)
APP_DEBUG=false
APP_URL=https://shop.smartlinesa.net

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=smartline_store
DB_USERNAME=smartline_user
DB_PASSWORD=your_password_here

BROADCAST_DRIVER=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="info@smartlinesa.net"
MAIL_FROM_NAME="${APP_NAME}"

# Store Configuration
STORE_DEFAULT_CURRENCY=SAR
STORE_BASE_CURRENCY=SAR
STORE_DEFAULT_COUNTRY=SA

# Payment Gateway (Disabled by default)
PAYMENT_GATEWAY_ENABLED=false
PAYMENT_PROVIDER=

# Shipping Configuration
SHIPPING_ENABLED=true
SHIPPING_DEFAULT_WEIGHT_UNIT=kg

# File Upload
MAX_UPLOAD_SIZE=10240
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,webp
ALLOWED_DOCUMENT_TYPES=pdf,doc,docx
```

**⚠️ هام:**
- `APP_DEBUG=false` في Production
- `APP_ENV=production`
- استخدم كلمة مرور قوية لقاعدة البيانات

---

## الخطوة 5: تشغيل composer / migrate / seed

### 5.1 تثبيت Dependencies

في cPanel، ابحث عن **"Terminal"** (أو استخدم SSH):

```bash
cd public_html/shop  # أو المسار الصحيح

# 1. تثبيت Composer (إذا غير موجود)
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader

# أو إذا Composer موجود:
composer install --no-dev --optimize-autoloader
```

**إذا Terminal غير متاح:**
استخدم File Manager، وابحث عن **"Composer"** في cPanel (بعض الاستضافات توفره)

### 5.2 توليد Application Key

```bash
php artisan key:generate
```

سيتم تحديث `APP_KEY` في `.env` تلقائياً

### 5.3 إعداد Permissions

```bash
# جعل مجلد storage قابلاً للكتابة
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# إذا أعطى خطأ، استخدم:
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

### 5.4 إنشاء Symbolic Link للصور

```bash
php artisan storage:link
```

### 5.5 تشغيل الترحيلات

```bash
php artisan migrate --force
```

### 5.6 تشغيل Seeders (بيانات تجريبية)

```bash
php artisan db:seed --force
```

**⚠️ ملاحظة:** سيتم إنشاء:
- مستخدم Admin: `admin@smartlinesa.net` / `SmartLine@2026`
- إعدادات المتجر
- بيانات تجريبية للمنتجات

---

## الخطوة 6: تفعيل HTTPS

### 6.1 تفعيل SSL المجاني (Let's Encrypt)

في cPanel:
1. ابحث عن **"SSL/TLS Status"** أو **"Let's Encrypt"**
2. حدد الدومين
3. اضغط **Install** أو **Auto-SSL**

### 6.2 تفعيل HTTPS Force

في `.env`، تأكد:
```env
APP_URL=https://shop.smartlinesa.net
```

أضف في `public/.htaccess` (أنشئه إذا غير موجود):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

### 3. أو في cPanel:
1. ابحث عن **"Force HTTPS"**
2. فعّله للدومين

---

## الخطوة 7: تجربة المتجر + الأدمن

### 7.1 اختبار المتجر العام

افتح المتصفح:
```
https://shop.smartlinesa.net
```

**تأكد من:**
- [ ] الصفحة الرئيسية تظهر
- [ ] اللغة العربية تعمل (RTL)
- [ ] المنتجات تظهر
- [ ] التصفح سريع
- [ ] أيقونة القفل (HTTPS) موجودة

### 7.2 اختبار لوحة الإدارة

افتح:
```
https://shop.smartlinesa.net/admin
```

**بيانات الدخول:**
```
Email: admin@smartlinesa.net
Password: SmartLine@2026
```

**تأكد من:**
- [ ] تسجيل الدخول يعمل
- [ ] Dashboard يظهر
- [ ] إدارة المنتجات تعمل
- [ ] إدارة الطلبات تعمل
- [ ] اللغة العربية في لوحة الإدارة

### 7.3 اختبار الوظائف الأساسية

- [ ] إضافة منتج جديد
- [ ] إضافة صور للمنتج
- [ ] إنشاء طلب تجريبي
- [ ] عرض عروض الأسعار
- [ ] تغيير اللغة

---

## الخطوة 8: زر «المتجر» من موقعهم

### 8.1 إضافة الزر في موقعهم

في الموقع الرئيسي (`smartlinesa.net`):

```html
<!-- في مكان مناسب (مثلاً في Header أو Menu) -->
<a href="https://shop.smartlinesa.net" class="btn-store">
    المتجر الإلكتروني
</a>
```

**أو بالعربية:**
```html
<a href="https://shop.smartlinesa.net" class="btn-store">
    المتجر
</a>
```

### 8.2 إضافة أيقونة (اختياري)

```html
<a href="https://shop.smartlinesa.net" class="btn-store">
    <i class="icon-shopping-cart"></i>
    المتجر
</a>
```

### 8.3 تخصيص CSS (على حسب تصميمهم)

```css
.btn-store {
    display: inline-block;
    padding: 10px 20px;
    background-color: #your-brand-color;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
}

.btn-store:hover {
    background-color: #darker-color;
}
```

---

## الخطوة 9: زر «الموقع الرئيسي» عندنا

### 9.1 إضافة الزر في المتجر

في ملف `resources/views/store/layouts/app.blade.php`:

```blade
<!-- في Header أو Navigation -->
<a href="https://smartlinesa.net" class="btn-main-site">
    الموقع الرئيسي
</a>
```

### 9.2 في المكان المناسب (مثال):

```blade
<nav class="navbar">
    <a href="{{ route('store.home') }}" class="logo">Smart Line</a>

    <div class="nav-links">
        <a href="{{ route('store.products.index') }}">المنتجات</a>
        <a href="{{ route('store.quote.create') }}">طلب تسعير</a>
        <a href="https://smartlinesa.net" class="btn-main-site">
            الموقع الرئيسي
        </a>
    </div>
</nav>
```

### 9.3 تحديث وإعادة النشر

```bash
# على السيرفر
cd public_html/shop
git pull  # إذا استخدم Git
# أو ارفع الملف المعدل يدوياً
```

---

## 🔧 استكشاف الأخطاء الشائعة

### مشكلة: صفحة بيضاء (White Screen)
**الحل:**
```bash
# تحقق من أخطاء PHP
tail -f storage/logs/laravel.log

# تأكد من Permissions
chmod -R 775 storage
```

### مشكلة: Database connection failed
**الحل:**
- تأكد من بيانات قاعدة البيانات في `.env`
- تأكد أن MySQL user له الصلاحيات

### مشكلة: Images not loading
**الحل:**
```bash
php artisan storage:link
chmod -R 775 public/storage
```

### مشكلة: HTTPS redirect loop
**الحل:**
- احذف أو علق على `.htaccess` force HTTPS
- تأكد أن SSL مفعّل في cPanel

### مشكلة: Admin login doesn't work
**الحل:**
```bash
# إعادة إنشاء مستخدم Admin
php artisan tinker
>>> \App\Models\User::where('email', 'admin@smartlinesa.net')->delete()
>>> \App\Models\User::create([
... 'name' => 'Admin',
... 'email' => 'admin@smartlinesa.net',
... 'password' => bcrypt('SmartLine@2026')
... ])
```

---

## 📝 قائمة التحقق النهائية

قبل تسليم المشروع:

- [ ] الدومين يعمل ويشير للسيرفر الصحيح
- [ ] HTTPS مفعّل والقفل الأخضر يظهر
- [ ] المتجر يعمل على الهاتف والكمبيوتر
- [ ] اللغة العربية تعمل بشكل صحيح (RTL)
- [ ] اللغة الإنجليزية تعمل بشكل صحيح (LTR)
- [ ] تسجيل الدخول للأدمن يعمل
- [ ] إنشاء المنتجات يعمل
- [ ] رفع الصور يعمل
- [ ] زر «المتجر» في موقعهم يعمل
- [ ] زر «الموقع الرئيسي» في المتجر يعمل
- [ ] بيانات العميل الافتراضية في الإعدادات
- [ ] Email settings مُعدة (اختياري)
- [ ] Backup plan مُعد
- [ ] ملفات `.env` و `vendor` مُستثناة من Git

---

## 🔄 الصيانة المستقبلية

### تحديث المشروع
```bash
# على السيرفر
cd public_html/shop
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### النسخ الاحتياطي
```bash
# Backup Database
mysqldump -u username -p database_name > backup.sql

# Backup Files
tar -czf backup.tar.gz public_html/shop
```

---

## 📞 الدعم

في حال وجود مشاكل:
1. تحقق من `storage/logs/laravel.log`
2. تأكد من PHP version >= 8.2
3. تأكد من MySQL version >= 8.0
4. تأكد من Extensions مُفعّلة

---

**آخر تحديث:** 2026-10-28
