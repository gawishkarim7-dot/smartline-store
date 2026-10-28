<?php

/**
 * Smart Line Store — One-time web installer for shared PHP hosting (no SSH needed).
 *
 * How to use:
 *   1) Upload the project to the server and point the web root at /public.
 *   2) Edit .env (DB credentials + APP_URL).
 *   3) Open https://your-domain/setup.php in the browser.
 *   4) Set the admin password in the form and click "Install".
 *   5) This file self-deletes on success. If it remains, delete it manually.
 *
 * This script runs migrations, seeders, creates the storage symlink, and sets
 * the admin password you choose. It locks itself after a successful run.
 */

define('LARAVEL_START', microtime(true));

$basePath  = dirname(__DIR__);
$autoload  = $basePath.'/vendor/autoload.php';
$bootstrap = $basePath.'/bootstrap/app.php';
$lockFile  = $basePath.'/storage/app/installed.lock';
$tokenFile = $basePath.'/storage/app/setup_token';

/* -----------------------------------------------------------------
 * Security gate: refuse to run if already installed.
 * ----------------------------------------------------------------- */
if (file_exists($lockFile)) {
    http_response_code(403);
    pageHeader('إعداد مكتمل');
    echo '<div class="box warn">المتجر مثبّت بالفعل. لأسباب أمنية احذف ملف <code>setup.php</code> من مجلد <code>public</code> يدويًا.</div>';
    pageFooter();
    exit;
}

/* -----------------------------------------------------------------
 * Basic environment checks.
 * ----------------------------------------------------------------- */
$fatal = null;
if (PHP_VERSION_ID < 80200) {
    $fatal = 'PHP 8.2 على الأقل مطلوب. الإصدار الحالي: '.PHP_VERSION;
} elseif (! file_exists($autoload)) {
    $fatal = 'ملف <code>vendor/autoload.php</code> غير موجود. تأكد من رفع مجلد <code>vendor</code> كاملًا.';
} elseif (! file_exists($bootstrap)) {
    $fatal = 'ملف <code>bootstrap/app.php</code> غير موجود.';
} elseif (! file_exists($basePath.'/.env')) {
    $fatal = 'ملف <code>.env</code> غير موجود. انسخ <code>.env.production</code> إلى <code>.env</code> وعدّل بيانات قاعدة البيانات.';
}

if ($fatal) {
    http_response_code(500);
    pageHeader('تعذّر الإعداد');
    echo '<div class="box err">'.$fatal.'</div>';
    pageFooter();
    exit;
}

$requiredExt = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'fileinfo', 'gd'];
$missingExt  = array_values(array_filter($requiredExt, fn ($e) => ! extension_loaded($e)));

// zip is recommended (some hosts lack it) but not required for runtime.
$zipNote = extension_loaded('zip') ? '' : ' (يُفضّل تفعيل zip أيضًا، لكنه غير إلزامي للتشغيل)';

if ($missingExt) {
    http_response_code(500);
    pageHeader('إضافات PHP ناقصة');
    echo '<div class="box err">الإضافات التالية مطلوبة ومفقودة: <code>'.implode(', ', $missingExt).'</code>. فعّلها من cPanel ← Select PHP Version.'.$zipNote.'</div>';
    pageFooter();
    exit;
}

/* -----------------------------------------------------------------
 * Bootstrap Laravel so we can run Artisan commands.
 * ----------------------------------------------------------------- */
require $autoload;
$app    = require $bootstrap;
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$messages = [];
$errors   = [];

/* -----------------------------------------------------------------
 * Handle install request.
 * ----------------------------------------------------------------- */
if (($_POST['action'] ?? '') === 'install') {

    // CSRF token check (file-based, since DB sessions may not exist yet).
    $postedToken = $_POST['token'] ?? '';
    $storedToken = is_file($tokenFile) ? trim((string) file_get_contents($tokenFile)) : '';
    @unlink($tokenFile);
    if (! hash_equals((string) $storedToken, (string) $postedToken) || $storedToken === '') {
        http_response_code(400);
        pageHeader('رمز غير صالح');
        echo '<div class="box err">رمز التحقق غير صالح. أعد فتح الصفحة وحاول مجددًا.</div>';
        pageFooter();
        exit;
    }

    // Ensure storage dirs are writable.
    foreach (['storage/app', 'storage/framework/cache', 'storage/framework/sessions', 'storage/framework/views', 'storage/logs', 'bootstrap/cache'] as $dir) {
        $p = $basePath.'/'.$dir;
        if (! is_dir($p)) { @mkdir($p, 0775, true); }
        if (! is_writable($p)) { $errors[] = 'المجلد غير قابل للكتابة: '.$dir; }
    }

    if (! $errors) {
        // 1) Application key (only if missing).
        if (empty(config('app.key'))) {
            try { Artisan::call('key:generate', ['--force' => true]); $messages[] = '✓ تم توليد مفتاح التطبيق APP_KEY'; }
            catch (\Throwable $e) { $errors[] = 'فشل توليد APP_KEY: '.$e->getMessage(); }
        } else {
            $messages[] = '✓ مفتاح التطبيق APP_KEY موجود';
        }
    }

    if (! $errors) {
        // 2) Database connection test.
        try {
            DB::select('SELECT 1');
            $messages[] = '✓ الاتصال بقاعدة البيانات ناجح';
        } catch (\Throwable $e) {
            $errors[] = 'فشل الاتصال بقاعدة البيانات: '.$e->getMessage().
                ' — تأكد من إنشاء قاعدة البيانات في cPanel ومن صحة DB_* في .env.';
        }
    }

    if (! $errors) {
        // 3) Migrate.
        try {
            Artisan::call('migrate', ['--force' => true]);
            $messages[] = '✓ تم تنفيذ الترحيلات';
        } catch (\Throwable $e) { $errors[] = 'فشل تنفيذ الترحيلات: '.$e->getMessage(); }

        // 4) Seed (admin + settings + catalog + pages).
        try {
            Artisan::call('db:seed', ['--force' => true]);
            $messages[] = '✓ تم تحميل البيانات الأساسية (الإدارة + المنتجات + الإعدادات)';
        } catch (\Throwable $e) { $errors[] = 'فشل تحميل البيانات: '.$e->getMessage(); }

        // 5) Storage symlink (best effort — uploads need this).
        try {
            Artisan::call('storage:link');
            $messages[] = '✓ تم إنشاء رابط storage للصور والمرفقات';
        } catch (\Throwable $e) {
            $messages[] = '⚠ تعذّر إنشاء رابط storage تلقائيًا: '.$e->getMessage().
                ' — أنشئه يدويًا أو ستحتاج لرفع صور المنتجات من لوحة الإدارة.';
        }

        // 6) Cache clear so new key/config take effect.
        try { Artisan::call('config:clear'); Artisan::call('cache:clear'); } catch (\Throwable $e) {}
    }

    // 7) Set the admin password chosen by the client (overrides the seeder default).
    if (! $errors) {
        $adminEmail = trim($_POST['admin_email'] ?? 'admin@smartlinesa.net');
        $adminPass  = (string) ($_POST['admin_password'] ?? '');
        if (strlen($adminPass) < 8) {
            $errors[] = 'كلمة مرور الإدارة يجب أن تكون 8 أحرف على الأقل.';
        } else {
            try {
                $admin = User::where('email', 'admin@smartlinesa.net')->first();
                if (! $admin) {
                    $admin = User::create([
                        'name'     => 'Smart Line Admin',
                        'email'    => 'admin@smartlinesa.net',
                        'password' => Hash::make($adminPass),
                        'is_active' => true,
                        'email_verified_at' => now(),
                    ]);
                } else {
                    $admin->password = Hash::make($adminPass);
                    $admin->save();
                }
                $messages[] = '✓ تم تعيين كلمة مرور الإدارة';

                // Lock the installer so it can't run again.
                @file_put_contents($lockFile, json_encode([
                    'installed_at' => date('c'),
                    'php'          => PHP_VERSION,
                    'admin_email'  => 'admin@smartlinesa.net',
                ]));

                // Best-effort self-delete.
                $deleted = @unlink(__FILE__);

                pageHeader('تم الإعداد بنجاح');
                echo '<div class="box ok"><strong>تم تثبيت المتجر بنجاح.</strong></div>';
                echo '<ul class="results">';
                foreach ($messages as $m) { echo '<li>'.e($m).'</li>'; }
                echo '</ul>';
                echo '<div class="box info">';
                echo '<strong>بيانات دخول الإدارة:</strong><br>';
                echo 'الرابط: <code>'.e(config('app.url')).'/admin</code><br>';
                echo 'البريد: <code>admin@smartlinesa.net</code><br>';
                echo 'كلمة المرور: <em>(التي أدخلتها)</em>';
                echo '</div>';
                echo '<div class="box warn">'.($deleted
                    ? 'تم حذف ملف setup.php تلقائيًا.'
                    : 'ادخل الآن واحذف ملف <code>public/setup.php</code> يدويًا من FTP/File Manager لأسباب أمنية.').'</div>';
                echo '<p class="next"><a class="btn" href="'.e(config('app.url')).'">فتح المتجر ←</a> '
                    .'<a class="btn" href="'.e(config('app.url')).'/admin">لوحة الإدارة ←</a></p>';
                pageFooter();
                exit;
            } catch (\Throwable $e) {
                $errors[] = 'فشل تعيين كلمة مرور الإدارة: '.$e->getMessage();
            }
        }
    }

    // If we got here, something failed — show errors and the form again.
}

/* -----------------------------------------------------------------
 * Show the install form (GET or after a failed POST).
 * ----------------------------------------------------------------- */
$token = bin2hex(random_bytes(16));
@file_put_contents($tokenFile, $token);

pageHeader('تثبيت متجر Smart Line');

if ($errors) {
    echo '<div class="box err"><strong>حدثت أخطاء:</strong><ul>';
    foreach ($errors as $er) { echo '<li>'.e($er).'</li>'; }
    echo '</ul></div>';
}

$appUrl = config('app.url');
?>
<div class="box intro">
    ستقوم هذه الخطوة بإنشاء جداول قاعدة البيانات، تحميل المنتجات والإعدادات، وإنشاء رابط الصور.
    قبل المتابعة تأكد أنك أنشأت قاعدة بيانات فارغة في cPanel وملأت بياناتها في ملف <code>.env</code>.
</div>
<form method="post" autocomplete="off">
    <input type="hidden" name="action" value="install">
    <input type="hidden" name="token" value="<?php echo e($token); ?>">

    <div class="field">
        <label>بريد الإدارة</label>
        <input type="email" name="admin_email" value="admin@smartlinesa.net" readonly>
        <small>ثابت. لتغييره لاحقًا من لوحة الإدارة.</small>
    </div>

    <div class="field">
        <label>كلمة مرور الإدارة <span class="req">*</span></label>
        <input type="password" name="admin_password" minlength="8" required placeholder="8 أحرف على الأقل">
        <small>ستُستخدم للدخول إلى لوحة الإدارة. احفظها في مكان آمن.</small>
    </div>

    <div class="field">
        <label class="check"><input type="checkbox" id="show" onclick="var p=document.getElementsByName('admin_password')[0];p.type=this.checked?'text':'password';"> إظهار كلمة المرور</label>
    </div>

    <button type="submit" class="btn big">تثبيت المتجر</button>
</form>

<div class="box info" style="margin-top:24px">
    <strong>المعلومات المكتشفة:</strong><br>
    PHP: <code><?php echo e(PHP_VERSION); ?></code> —
    امتدادات: <code>OK</code><br>
    Application URL: <code><?php echo e($appUrl); ?></code><br>
    قاعدة البيانات: <code><?php echo e(config('database.default')); ?></code> →
    <code><?php echo e(config('database.connections.'.config('database.default').'.database') ?: '(من .env)'); ?></code>
</div>
<?php
pageFooter();
exit;

/* -----------------------------------------------------------------
 * Tiny helpers.
 * ----------------------------------------------------------------- */
function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

function pageHeader($title) {
    $dir = 'rtl';
    echo '<!doctype html><html lang="ar" dir="'.$dir.'"><head><meta charset="utf-8">'
        .'<meta name="viewport" content="width=device-width, initial-scale=1">'
        .'<title>'.e($title).'</title>'
        .'<style>'
        .':root{--bd:#d97706;--bg:#f7f7f8;--ok:#0f766e;--er:#b91c1c;}'
        .'*{box-sizing:border-box}body{font-family:"Segoe UI",Tahoma,sans-serif;background:'.('var(--bg)').';color:#1f2937;margin:0;padding:24px;direction:rtl}'
        .'.wrap{max-width:560px;margin:0 auto}'
        .'h1{font-size:20px;margin:0 0 16px;color:#111}'
        .'.box{border-radius:10px;padding:14px 16px;margin:14px 0;line-height:1.7}'
        .'.box.intro{background:#eef2ff;border:1px solid #c7d2fe}'
        .'.box.ok{background:#ecfdf5;border:1px solid #a7f3d0;color:'.('var(--ok)').'}'
        .'.box.err{background:#fef2f2;border:1px solid #fecaca;color:'.('var(--er)').'}'
        .'.box.warn{background:#fffbeb;border:1px solid #fde68a}'
        .'.box.info{background:#f8fafc;border:1px solid #e2e8f0}'
        .'.results{padding:0 18px;margin:8px 0}'
        .'.results li{margin:4px 0}'
        .'.field{margin:14px 0}'
        .'.field label{display:block;font-weight:600;margin-bottom:6px}'
        .'.field small{color:#6b7280;display:block;margin-top:4px}'
        .'.field input{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:15px;font-family:inherit}'
        .'.req{color:'.('var(--er)').'}'
        .'.btn{display:inline-block;background:#d97706;color:#fff;border:none;padding:11px 20px;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none}'
        .'.btn.big{width:100%;padding:14px;font-size:16px}'
        .'.next{margin-top:18px}.next .btn{margin-inline-end:8px}'
        .'code{background:#eef0f2;padding:1px 6px;border-radius:4px;font-size:14px}'
        .'</style></head><body><div class="wrap"><h1>🛠️ '.e($title).'</h1>';
}

function pageFooter() {
    echo '</div></body></html>';
}
