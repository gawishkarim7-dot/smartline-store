# طريقة تنصيب وتشغيل متجر Smart Line

---

## ملفان للتشغيل (اختر واحد)

| الملف | متى تستخدمه |
|------|-------------|
| **`start.bat`** | التشغيل العادي — PHP + MySQL محلي (**بدون Docker**) ← **موصى به لفريق الشركة** |
| **`start-docker.bat`** | لو جهاز المطوّر عليه Docker Desktop فقط |

---

## أ) التشغيل العادي بدون Docker — `start.bat`

### المتطلبات
1. Windows 10/11
2. **Laragon** (موصى به): https://laragon.org/download/  
   أو XAMPP مع PHP 8.3/8.4 + MySQL
3. Composer (غالباً يأتي مع Laragon؛ وإلا `start.bat` يحمّله تلقائياً)
4. إنترنت لأول تشغيل

### الخطوات
1. فك ضغط المشروع (مثال: `C:\Projects\SmartLine`)
2. افتح **Laragon** واضغط **Start All** (عشان MySQL يشتغل)
3. دبل كليك على **`start.bat`**
4. افتح المتصفح:
   - المتجر: http://localhost:8007
   - الإدارة: http://localhost:8007/admin

### دخول الإدارة
| | |
|--|--|
| الرابط | http://localhost:8007/admin |
| البريد (Username) | `admin@smartlinesa.net` |
| كلمة المرور | `SmartLine@2026` |

**تغيير البيانات:** بعد الدخول من القائمة الجانبية → **إعدادات الحساب**  
يمكن تغيير الاسم والبريد وكلمة المرور في أي وقت. يُفضّل تغيير كلمة المرور فور الاستلام.

### لو فشل الاتصال بقاعدة البيانات
1. تأكد MySQL شغال من Laragon
2. افتح ملف `.env` وعدّل:
   - `DB_USERNAME=root`
   - `DB_PASSWORD=` (فاضي في Laragon غالباً) أو كلمة مرور MySQL عندكم
3. أنشئ قاعدة باسم: `smartline_store` إن لم تُنشأ تلقائياً
4. أعد تشغيل `start.bat`

---

## ب) التشغيل بـ Docker — `start-docker.bat`

### المتطلبات
- Docker Desktop مثبت وشغّال

### الخطوات
1. افتح Docker Desktop وانتظر Ready
2. دبل كليك **`start-docker.bat`**
3. افتح http://localhost:8007
4. للإيقاف: `stop-docker.bat` أو Ctrl+C

---

## ج) التنصيب على السيرفر (إنتاج)

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# عدّل APP_URL و DB_* و APP_DEBUG=false
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
npm ci && npm run build
```

Document root = مجلد `public`  
نطاق مقترح: `store.smartlinesa.net`

### الربط مع الموقع الرئيسي
- من موقع الشركة: زر «المتجر» → نطاق المتجر
- داخل المتجر: زر «الموقع الرئيسي» → `https://smartlinesa.net`

---

## د) الدفع
بوابة الدفع الإلكتروني مغلقة. العمل بتحويل بنكي (بنك الرياض) + رفع إيصال + تأكيد من الأدمن.
