{{--
    عارض الكتاب. يمكن تضمينه في أي صفحة من مشروعك:
    @include('flipbook::partials.viewer', ['book' => $book])
    الخيار full => true يجعله يملأ ارتفاع الشاشة (يُستخدم في صفحة التضمين).
--}}
@php($fbId = 'fb-'.\Illuminate\Support\Str::random(8))

@once
<style>
    .fb-viewer { position: relative; background: #3b3632; border-radius: 12px; padding: 24px 16px 12px; display: flex; flex-direction: column; align-items: center; }
    .fb-viewer.fb-full { border-radius: 0; min-height: 100vh; justify-content: center; }
    .fb-viewer:fullscreen { border-radius: 0; justify-content: center; }
    .fb-stage { width: 100%; max-width: 1100px; }
    .fb-book { margin: 0 auto; }
    .fb-page { background: #fff; overflow: hidden; }
    .fb-page img, .fb-page canvas { width: 100%; height: 100%; object-fit: contain; display: block; }
    .fb-toolbar { direction: ltr; display: flex; align-items: center; gap: 10px; margin-top: 16px; color: #eee; flex-wrap: wrap; justify-content: center; }
    .fb-toolbar button { background: rgba(255,255,255,.12); color: #fff; border: 0; border-radius: 8px; width: 40px; height: 40px; font-size: 1.1rem; cursor: pointer; }
    .fb-toolbar button:hover { background: rgba(255,255,255,.22); }
    .fb-toolbar input { width: 64px; padding: 8px; border-radius: 8px; border: 0; text-align: center; font: inherit; }
    .fb-count { min-width: 60px; }
    .fb-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: #fff; background: #3b3632; border-radius: inherit; z-index: 10; font-size: 1.05rem; }
    .fb-loading[hidden] { display: none; }
</style>
<script src="https://cdn.jsdelivr.net/npm/page-flip@2.0.7/dist/js/page-flip.browser.js"></script>
<script>
window.FlipbookViewer = (function () {
    const PDFJS = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/';
    let pdfjsReady = null;
    let active = null; // الكتاب الذي تتحكم فيه لوحة المفاتيح عند وجود أكثر من كتاب في الصفحة

    document.addEventListener('keydown', e => {
        if (!active || ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;
        if (e.key === 'ArrowLeft') active.flipPrev();
        if (e.key === 'ArrowRight') active.flipNext();
    });

    // PDF.js يُحمَّل فقط عند وجود كتاب PDF في الصفحة
    function loadPdfJs() {
        pdfjsReady ??= new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = PDFJS + 'pdf.min.js';
            s.onload = () => {
                window.pdfjsLib.GlobalWorkerOptions.workerSrc = PDFJS + 'pdf.worker.min.js';
                resolve(window.pdfjsLib);
            };
            s.onerror = reject;
            document.head.appendChild(s);
        });
        return pdfjsReady;
    }

    function makePage(inner) {
        const div = document.createElement('div');
        div.className = 'fb-page';
        div.appendChild(inner);
        return div;
    }

    async function init(root, config) {
        const bookEl = root.querySelector('.fb-book');
        const stage = root.querySelector('.fb-stage');
        const loading = root.querySelector('.fb-loading');
        const pageInput = root.querySelector('.fb-page-input');
        const pageCount = root.querySelector('.fb-count');

        let pdf = null;
        let pageWidth = 600, pageHeight = 800;
        const pages = []; // عناصر الصفحات بالترتيب المنطقي (الصفحة 1 أولاً)

        // ---------- تجهيز الصفحات ----------
        if (config.type === 'pdf') {
            const pdfjsLib = await loadPdfJs();
            const task = pdfjsLib.getDocument(config.pdf);
            task.onProgress = ({ loaded, total }) => {
                if (total) loading.textContent = `جارٍ تحميل الكتاب… ${Math.round(loaded / total * 100)}%`;
            };
            pdf = await task.promise;

            const vp = (await pdf.getPage(1)).getViewport({ scale: 1 });
            pageWidth = vp.width;
            pageHeight = vp.height;

            for (let i = 1; i <= pdf.numPages; i++) {
                pages.push(makePage(document.createElement('canvas')));
            }
        } else {
            const size = await new Promise(resolve => {
                const img = new Image();
                img.onload = () => resolve({ w: img.naturalWidth, h: img.naturalHeight });
                img.onerror = () => resolve({ w: 600, h: 800 });
                img.src = config.images[0];
            });
            pageWidth = size.w;
            pageHeight = size.h;

            config.images.forEach((src, i) => {
                const img = new Image();
                img.loading = i < 4 ? 'eager' : 'lazy';
                img.decoding = 'async';
                img.alt = `صفحة ${i + 1}`;
                img.src = src;
                pages.push(makePage(img));
            });
        }

        const total = pages.length;
        pageInput.max = total;
        pageCount.textContent = `/ ${total}`;

        // للكتب العربية: نعكس ترتيب الصفحات ونبدأ من آخرها ليكون التقليب من اليمين لليسار
        const ordered = config.rtl ? [...pages].reverse() : pages;
        ordered.forEach(p => bookEl.appendChild(p));

        // تحويل بين فهرس المكتبة (index) ورقم الصفحة المنطقي (1..total)
        const toLogical = idx => config.rtl ? total - idx : idx + 1;
        const toIndex = num => config.rtl ? total - num : num - 1;

        // ---------- إنشاء الكتاب ----------
        // نحدّ عرض الكتاب حتى يتسع ارتفاعه داخل الشاشة مع شريط الأدوات
        function fitStage() {
            const fullPage = document.fullscreenElement === root || root.classList.contains('fb-full');
            const availableHeight = window.innerHeight - (fullPage ? 110 : 230);
            const spreadWidth = 2 * availableHeight * pageWidth / pageHeight;
            stage.style.maxWidth = Math.max(320, Math.min(1100, spreadWidth)) + 'px';
        }
        fitStage();
        window.addEventListener('resize', fitStage); // يُسجَّل قبل PageFlip حتى يُطبَّق قبل إعادة حسابه للحجم

        const baseWidth = 550;
        const pageFlip = new St.PageFlip(bookEl, {
            width: baseWidth,
            height: Math.round(baseWidth * pageHeight / pageWidth),
            size: 'stretch',
            minWidth: 250,
            maxWidth: 1000,
            minHeight: Math.round(250 * pageHeight / pageWidth),
            maxHeight: Math.round(1000 * pageHeight / pageWidth),
            showCover: true,
            usePortrait: true,
            mobileScrollSupport: false,
            maxShadowOpacity: 0.4,
            flippingTime: 700,
        });

        pageFlip.loadFromHTML(ordered);
        if (config.rtl) pageFlip.turnToPage(total - 1);

        active ??= pageFlip;
        root.addEventListener('pointerdown', () => active = pageFlip);

        // ---------- رسم صفحات PDF عند الحاجة فقط ----------
        const rendered = new Map();
        function renderPdfPage(num) {
            if (!pdf || num < 1 || num > total || rendered.has(num)) return;
            const canvas = pages[num - 1].querySelector('canvas');
            const job = pdf.getPage(num).then(page => {
                const targetWidth = Math.min(1600, bookEl.clientWidth * (window.devicePixelRatio || 1));
                const viewport = page.getViewport({ scale: targetWidth / page.getViewport({ scale: 1 }).width });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                return page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
            }).catch(() => rendered.delete(num));
            rendered.set(num, job);
        }

        function onPageChange() {
            // في الوضع العربي مع صفحتين متقابلتين، الصفحة الأولى منطقياً هي اليمنى (index + 1)
            const idx = pageFlip.getCurrentPageIndex();
            const spread = config.rtl && pageFlip.getOrientation() === 'landscape';
            const current = Math.max(1, toLogical(spread ? idx + 1 : idx));
            pageInput.value = current;
            for (let n = current - 2; n <= current + 3; n++) renderPdfPage(n);
        }

        pageFlip.on('flip', onPageChange);
        onPageChange();
        loading.hidden = true;

        // ---------- أزرار التحكم ----------
        // الأزرار تعمل بصرياً: السهم الأيسر يقلّب الصفحة اليسرى، والأيمن يقلّب اليمنى
        root.querySelector('.fb-left').onclick = () => pageFlip.flipPrev();
        root.querySelector('.fb-right').onclick = () => pageFlip.flipNext();

        pageInput.addEventListener('change', () => {
            const num = Math.min(total, Math.max(1, parseInt(pageInput.value, 10) || 1));
            pageFlip.flip(toIndex(num));
        });

        root.querySelector('.fb-fullscreen').onclick = () => {
            document.fullscreenElement ? document.exitFullscreen() : root.requestFullscreen();
        };
    }

    return function (root, config) {
        init(root, config).catch(err => {
            console.error(err);
            root.querySelector('.fb-loading').textContent = 'تعذّر تحميل الكتاب';
        });
    };
})();
</script>
@endonce

<div class="fb-viewer {{ ($full ?? false) ? 'fb-full' : '' }}" id="{{ $fbId }}">
    <div class="fb-loading">جارٍ تحميل الكتاب…</div>

    <div class="fb-stage">
        <div class="fb-book"></div>
    </div>

    <div class="fb-toolbar">
        <button type="button" class="fb-left" title="تقليب الصفحة اليسرى">◀</button>
        <input type="number" class="fb-page-input" min="1" value="1" title="انتقل إلى صفحة">
        <span class="fb-count">/ 0</span>
        <button type="button" class="fb-right" title="تقليب الصفحة اليمنى">▶</button>
        <button type="button" class="fb-fullscreen" title="ملء الشاشة">⛶</button>
    </div>
</div>

<script>FlipbookViewer(document.getElementById('{{ $fbId }}'), @json($book->viewerConfig()));</script>
