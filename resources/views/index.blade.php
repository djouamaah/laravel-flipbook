@extends(config('flipbook.layout'))

@section('title', 'الكتب التفاعلية')

@section(config('flipbook.section'))
    @include('flipbook::partials.styles')
    <style>
        .fb-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .fb-book-card { overflow: hidden; display: flex; flex-direction: column; color: var(--fb-text) !important; transition: transform .15s, box-shadow .15s; }
        .fb-book-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.08); }
        .fb-cover { aspect-ratio: 3 / 4; background: #ebe4da center / cover no-repeat; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--fb-accent); }
        .fb-info { padding: 12px 14px; }
        .fb-info h3 { margin: 0 0 4px; font-size: 1rem; }
        .fb-info small { color: var(--fb-muted); }
        .fb-empty { text-align: center; padding: 64px 16px; color: var(--fb-muted); }
    </style>

    <div class="fb-root" dir="rtl">
        @if (session('success'))
            <div class="fb-alert">{{ session('success') }}</div>
        @endif

        <div class="fb-head">
            <h1>الكتب</h1>
            <a href="{{ route('flipbook.create') }}" class="fb-btn">+ كتاب جديد</a>
        </div>

        @if ($flipbooks->isEmpty())
            <div class="fb-card fb-empty">
                <p>لا توجد كتب بعد.</p>
                <a href="{{ route('flipbook.create') }}" class="fb-btn">أضف أول كتاب</a>
            </div>
        @else
            <div class="fb-grid">
                @foreach ($flipbooks as $book)
                    <a href="{{ route('flipbook.show', $book) }}" class="fb-card fb-book-card">
                        @if ($book->coverUrl())
                            <div class="fb-cover" style="background-image: url('{{ $book->coverUrl() }}')"></div>
                        @else
                            <div class="fb-cover">PDF</div>
                        @endif
                        <div class="fb-info">
                            <h3>{{ $book->title }}</h3>
                            <small>
                                {{ $book->isPdf() ? 'ملف PDF' : count($book->images).' صورة' }}
                                · {{ $book->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </a>
                @endforeach
            </div>

            <div style="margin-top: 24px">{{ $flipbooks->links() }}</div>
        @endif
    </div>
@endsection
