@extends(config('flipbook.layout'))

@section('title', $book->title)

@section(config('flipbook.section'))
    @include('flipbook::partials.styles')
    <style>
        .fb-embed-code { margin-top: 20px; padding: 16px; }
        .fb-embed-code textarea { width: 100%; direction: ltr; font-family: monospace; font-size: .85rem; border: 1px solid var(--fb-border); border-radius: 8px; padding: 10px; resize: vertical; }
    </style>

    <div class="fb-root" dir="rtl">
        @if (session('success'))
            <div class="fb-alert">{{ session('success') }}</div>
        @endif

        <div class="fb-head">
            <h1>{{ $book->title }}</h1>
            <div class="fb-actions">
                <a href="{{ route('flipbook.index') }}" class="fb-btn fb-btn-outline">كل الكتب</a>
                @if ($book->isPdf())
                    <a href="{{ $book->pdfUrl() }}" class="fb-btn fb-btn-outline" download>تحميل PDF</a>
                @endif
                <form method="POST" action="{{ route('flipbook.destroy', $book) }}" onsubmit="return confirm('حذف هذا الكتاب نهائياً؟')">
                    @csrf @method('DELETE')
                    <button class="fb-btn fb-btn-danger">حذف</button>
                </form>
            </div>
        </div>

        @include('flipbook::partials.viewer', ['book' => $book])

        <div class="fb-card fb-embed-code">
            <label><strong>كود التضمين في موقع آخر</strong></label>
            <textarea rows="2" readonly onclick="this.select()"><iframe src="{{ route('flipbook.embed', $book) }}" width="100%" height="700" style="border:0" allowfullscreen></iframe></textarea>
        </div>
    </div>
@endsection
