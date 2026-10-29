{{-- تخطيط افتراضي بسيط. لاستخدام تخطيط مشروعك غيّر flipbook.layout في الإعدادات --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'الكتب التفاعلية')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Tajawal', system-ui, sans-serif; background: #f4f1ec; color: #2b2724; }
        .fb-container { max-width: 1200px; margin: 0 auto; padding: 24px 16px 48px; }
    </style>
</head>
<body>
    <div class="fb-container">
        @yield('content')
    </div>
</body>
</html>
