<?php

return [

    // بادئة الروابط: /flipbooks, /flipbooks/create, /flipbooks/{slug} ...
    'prefix' => 'flipbooks',

    // Middleware لكل مسارات الحزمة
    'middleware' => ['web'],

    // Middleware إضافي لصفحات الإضافة والحذف فقط، مثلاً ['auth'] أو ['auth', 'can:manage-flipbooks']
    'manage_middleware' => [],

    // القرص والمجلد اللذان تُحفظ فيهما الملفات
    'disk' => 'public',
    'directory' => 'flipbooks',

    // التخطيط الذي ترث منه صفحات الحزمة، واسم الـ section الذي يُعرض فيه المحتوى.
    // لاستخدام تخطيط مشروعك: 'layout' => 'layouts.app'
    'layout' => 'flipbook::layout',
    'section' => 'content',

    // حدود الرفع (بالكيلوبايت)
    'max_pdf_size' => 51200,   // 50MB
    'max_image_size' => 10240, // 10MB لكل صورة
    'max_images' => 500,

    // اجعلها false إن نشرت الـ migration إلى مشروعك (vendor:publish --tag=flipbook-migrations)
    'load_migrations' => true,

];
