@extends(config('flipbook.layout'))

@section('title', 'كتاب جديد')

@section(config('flipbook.section'))
    @include('flipbook::partials.styles')
    <style>
        .fb-form { max-width: 640px; padding: 24px; }
        .fb-field { margin-bottom: 20px; }
        .fb-field > label { display: block; font-weight: 500; margin-bottom: 8px; }
        .fb-field input[type=text], .fb-field input[type=file] { width: 100%; padding: 10px 12px; border: 1px solid var(--fb-border); border-radius: 8px; font: inherit; background: #fff; }
        .fb-types { display: flex; gap: 12px; flex-wrap: wrap; }
        .fb-types label { flex: 1; min-width: 160px; display: flex; gap: 8px; align-items: center; padding: 12px 14px; border: 1px solid var(--fb-border); border-radius: 8px; cursor: pointer; }
        .fb-types label:has(input:checked) { border-color: var(--fb-accent); background: #fbf6f0; }
        .fb-hint { color: var(--fb-muted); font-size: .875rem; margin-top: 6px; }
        .fb-error { color: var(--fb-danger); font-size: .875rem; margin-top: 6px; }
        .fb-check { display: flex; gap: 8px; align-items: center; }
    </style>

    <div class="fb-root" dir="rtl">
        <div class="fb-head">
            <h1>إضافة كتاب جديد</h1>
            <a href="{{ route('flipbook.index') }}" class="fb-btn fb-btn-outline">كل الكتب</a>
        </div>

        <form class="fb-card fb-form" method="POST" action="{{ route('flipbook.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="fb-field">
                <label for="fb-title">عنوان الكتاب</label>
                <input type="text" id="fb-title" name="title" value="{{ old('title') }}" required>
                @error('title') <div class="fb-error">{{ $message }}</div> @enderror
            </div>

            <div class="fb-field">
                <label>نوع المحتوى</label>
                <div class="fb-types">
                    <label><input type="radio" name="type" value="pdf" @checked(old('type', 'pdf') === 'pdf')> ملف PDF</label>
                    <label><input type="radio" name="type" value="images" @checked(old('type') === 'images')> مجموعة صور</label>
                </div>
            </div>

            <div class="fb-field" id="fb-pdf-field">
                <label for="fb-pdf">ملف PDF</label>
                <input type="file" id="fb-pdf" name="pdf" accept="application/pdf">
                @error('pdf') <div class="fb-error">{{ $message }}</div> @enderror
            </div>

            <div class="fb-field" id="fb-images-field">
                <label for="fb-images">الصور (صفحة لكل صورة)</label>
                <input type="file" id="fb-images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
                <div class="fb-hint">تُرتَّب الصفحات حسب اسم الملف: 1.jpg، 2.jpg، … 10.jpg</div>
                @error('images') <div class="fb-error">{{ $message }}</div> @enderror
                @error('images.*') <div class="fb-error">{{ $message }}</div> @enderror
            </div>

            <div class="fb-field">
                <label class="fb-check">
                    <input type="hidden" name="rtl" value="0">
                    <input type="checkbox" name="rtl" value="1" @checked(old('rtl', '1') === '1')>
                    تقليب من اليمين إلى اليسار (كتاب عربي)
                </label>
            </div>

            <div class="fb-hint" style="margin-bottom: 16px">
                حدود الخادم الحالية: حجم الملف {{ ini_get('upload_max_filesize') }} · مجموع الطلب {{ ini_get('post_max_size') }} · عدد الملفات {{ ini_get('max_file_uploads') }}
            </div>

            <button type="submit" class="fb-btn">حفظ وعرض الكتاب</button>
        </form>
    </div>

    <script>
        (function () {
            const pdfField = document.getElementById('fb-pdf-field');
            const imagesField = document.getElementById('fb-images-field');
            function toggleFields() {
                const type = document.querySelector('input[name=type]:checked').value;
                pdfField.hidden = type !== 'pdf';
                imagesField.hidden = type !== 'images';
            }
            document.querySelectorAll('input[name=type]').forEach(r => r.addEventListener('change', toggleFields));
            toggleFields();
        })();
    </script>
@endsection
