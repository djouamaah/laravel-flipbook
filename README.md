# Laravel Flipbook

حزمة Laravel تحوّل ملفات **PDF** أو **مجموعات الصور** إلى كتب تفاعلية بتأثير تقليب الصفحات.

- **PDF**: يُرسَم في المتصفح بـ PDF.js، ولا تُرسم إلا الصفحات القريبة من الصفحة الحالية، فيبقى سريعاً مع الكتب الكبيرة.
- **مجموعة صور**: كل صورة صفحة، وتُرتَّب طبيعياً حسب اسم الملف (`1.jpg`، `2.jpg`، … `10.jpg`).
- **تقليب عربي (RTL)** من اليمين إلى اليسار، يُختار لكل كتاب.
- أزرار وأسهم لوحة المفاتيح، والانتقال إلى صفحة محددة، وملء الشاشة، وصفحة واحدة على الجوال.
- **كود تضمين** (`iframe`) لعرض الكتاب في أي موقع آخر.
- يُقدَّم ملف الـ PDF للعارض عبر رابط بدون امتداد `.pdf`، فلا تعترضه برامج التحميل مثل IDM.

المكتبات الأمامية تُحمَّل من CDN، فلا حاجة إلى npm:
[StPageFlip](https://github.com/Nodlik/StPageFlip) و [PDF.js](https://mozilla.github.io/pdf.js/).

**المتطلبات:** PHP 8.2+ و Laravel 11 أو 12 أو 13.

## التثبيت

الحزمة غير منشورة على Packagist بعد، لذا أضف المستودع إلى `composer.json` في مشروعك:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/djouamaah/laravel-flipbook" }
]
```

ثم:

```bash
composer require djouamaah/laravel-flipbook:dev-main
php artisan migrate
php artisan storage:link
```

افتح `/flipbooks`.

> ⚠️ اجعل `APP_URL` في `.env` مطابقاً لعنوان موقعك **مع المنفذ** (مثل `http://localhost:8000`)، وإلا لن تُحمَّل الصور ولن يعمل زر تحميل الـ PDF.

## الروابط

| الرابط | الاسم | الوظيفة |
|---|---|---|
| `GET /flipbooks` | `flipbook.index` | قائمة الكتب |
| `GET /flipbooks/create` | `flipbook.create` | نموذج إضافة كتاب |
| `POST /flipbooks` | `flipbook.store` | حفظ الكتاب |
| `GET /flipbooks/{slug}` | `flipbook.show` | عرض الكتاب |
| `GET /flipbooks/{slug}/embed` | `flipbook.embed` | صفحة للتضمين في iframe |
| `GET /flipbooks/{slug}/document` | `flipbook.document` | ملف الـ PDF للعارض |
| `DELETE /flipbooks/{slug}` | `flipbook.destroy` | حذف الكتاب وملفاته |

## الإعدادات

```bash
php artisan vendor:publish --tag=flipbook-config
```

أهم الخيارات في `config/flipbook.php`:

```php
'prefix' => 'flipbooks',              // بادئة الروابط
'middleware' => ['web'],
'manage_middleware' => ['auth'],      // حماية الإضافة والحذف (فارغة افتراضياً)
'disk' => 'public',                   // قرص التخزين
'layout' => 'layouts.app',            // تخطيط مشروعك بدل التخطيط الافتراضي
'section' => 'content',               // اسم الـ section في التخطيط
'max_pdf_size' => 51200,              // KB
'max_image_size' => 10240,            // KB لكل صورة
```

> **مهم:** صفحتا الإضافة والحذف مفتوحتان للجميع افتراضياً. في الإنتاج اضبط `manage_middleware` على `['auth']` أو صلاحية مناسبة.

## عرض الكتاب داخل صفحاتك

يمكنك وضع العارض في أي صفحة Blade من مشروعك:

```blade
@php($book = \Djouamaah\Flipbook\Models\Flipbook::where('slug', 'my-book')->first())

@include('flipbook::partials.viewer', ['book' => $book])
```

يدعم أكثر من كتاب في الصفحة نفسها.

## تخصيص الواجهات والـ Migration

```bash
php artisan vendor:publish --tag=flipbook-views        # إلى resources/views/vendor/flipbook
php artisan vendor:publish --tag=flipbook-migrations
```

إذا نشرت الـ migration اجعل `'load_migrations' => false` في الإعدادات حتى لا يُنشأ الجدول مرتين.

## حجم الملفات المرفوعة

يجب أن تسمح إعدادات PHP بالأحجام المطلوبة في `php.ini`:

```ini
upload_max_filesize = 64M
post_max_size = 256M
max_file_uploads = 500   ; الافتراضي 20 فقط، وهو يحدّ عدد صور الكتاب الواحد
```

## الاختبارات

```bash
composer install
composer test
```

## الترخيص

MIT
