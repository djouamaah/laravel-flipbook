<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $book->title }}</title>
    <style>body { margin: 0; background: #3b3632; font-family: system-ui, sans-serif; }</style>
</head>
<body>
    @include('flipbook::partials.viewer', ['book' => $book, 'full' => true])
</body>
</html>
