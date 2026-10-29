<?php

namespace Djouamaah\Flipbook\Models;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Flipbook extends Model
{
    protected $table = 'flipbooks';

    protected $fillable = ['title', 'slug', 'type', 'pdf_path', 'images', 'rtl'];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'rtl' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(config('flipbook.disk'));
    }

    /** مجلد ملفات هذا الكتاب على القرص */
    public function directory(): string
    {
        return trim(config('flipbook.directory'), '/').'/'.$this->slug;
    }

    public function isPdf(): bool
    {
        return $this->type === 'pdf';
    }

    /** رابط ملف الـ PDF الأصلي (لزر التحميل) */
    public function pdfUrl(): ?string
    {
        return $this->pdf_path ? static::disk()->url($this->pdf_path) : null;
    }

    /** روابط الصور بالترتيب */
    public function imageUrls(): array
    {
        return array_map(fn (string $path) => static::disk()->url($path), $this->images ?? []);
    }

    /** صورة الغلاف للعرض في القائمة (للصور فقط) */
    public function coverUrl(): ?string
    {
        return $this->imageUrls()[0] ?? null;
    }

    /** البيانات التي يحتاجها عارض الكتاب في JavaScript */
    public function viewerConfig(): array
    {
        return [
            'type' => $this->type,
            'pdf' => $this->isPdf() ? route('flipbook.document', $this) : null,
            'images' => $this->imageUrls(),
            'rtl' => $this->rtl,
        ];
    }

    /** حذف الملفات من القرص عند حذف الكتاب */
    protected static function booted(): void
    {
        static::deleting(function (Flipbook $book) {
            static::disk()->deleteDirectory($book->directory());
        });
    }
}
