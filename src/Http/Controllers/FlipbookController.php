<?php

namespace Djouamaah\Flipbook\Http\Controllers;

use Djouamaah\Flipbook\Models\Flipbook;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class FlipbookController extends Controller
{
    public function index()
    {
        $flipbooks = Flipbook::latest()->paginate(12);

        return view('flipbook::index', compact('flipbooks'));
    }

    public function create()
    {
        return view('flipbook::create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:pdf,images'],
            'rtl' => ['nullable', 'boolean'],
            'pdf' => ['required_if:type,pdf', 'nullable', 'file', 'mimes:pdf', 'max:'.config('flipbook.max_pdf_size')],
            'images' => ['required_if:type,images', 'nullable', 'array', 'min:1', 'max:'.config('flipbook.max_images')],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.config('flipbook.max_image_size')],
        ]);

        $book = new Flipbook([
            'title' => $data['title'],
            'slug' => (Str::slug($data['title']) ?: 'book').'-'.Str::lower(Str::random(6)),
            'type' => $data['type'],
            'rtl' => $request->boolean('rtl'),
        ]);

        $disk = config('flipbook.disk');
        $dir = $book->directory();

        if ($data['type'] === 'pdf') {
            $book->pdf_path = $request->file('pdf')->storeAs($dir, 'book.pdf', $disk);
        } else {
            // ترتيب الصور طبيعياً حسب اسم الملف: 1.jpg, 2.jpg, ..., 10.jpg
            $files = collect($request->file('images'))
                ->sort(fn ($a, $b) => strnatcasecmp($a->getClientOriginalName(), $b->getClientOriginalName()))
                ->values();

            $book->images = $files->map(function ($file, $i) use ($dir, $disk) {
                $name = str_pad($i + 1, 4, '0', STR_PAD_LEFT).'.'.$file->extension();

                return $file->storeAs("{$dir}/pages", $name, $disk);
            })->all();
        }

        $book->save();

        return redirect()->route('flipbook.show', $book)->with('success', 'تم إنشاء الكتاب بنجاح');
    }

    public function show(Flipbook $flipbook)
    {
        return view('flipbook::show', ['book' => $flipbook]);
    }

    /** نسخة بدون واجهة للتضمين داخل iframe في مواقع أخرى */
    public function embed(Flipbook $flipbook)
    {
        return view('flipbook::embed', ['book' => $flipbook]);
    }

    /**
     * ملف الـ PDF للعارض فقط: رابط بدون امتداد .pdf ونوع محتوى غير application/pdf،
     * حتى لا تعترضه برامج التحميل (مثل IDM) أو المتصفح وتطلب تنزيله بدل عرضه.
     */
    public function document(Flipbook $flipbook)
    {
        abort_unless($flipbook->isPdf() && $flipbook->pdf_path, 404);

        $headers = [
            'Content-Type' => 'application/x-flipbook',
            'Cache-Control' => 'public, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ];

        // القرص المحلي يدعم طلبات Range فيحمّل PDF.js الصفحات تدريجياً
        if (config('filesystems.disks.'.config('flipbook.disk').'.driver') === 'local') {
            return response()->file(Flipbook::disk()->path($flipbook->pdf_path), $headers);
        }

        return response()->stream(function () use ($flipbook) {
            fpassthru(Flipbook::disk()->readStream($flipbook->pdf_path));
        }, 200, $headers);
    }

    public function destroy(Flipbook $flipbook)
    {
        $flipbook->delete();

        return redirect()->route('flipbook.index')->with('success', 'تم حذف الكتاب');
    }
}
