<?php

namespace Djouamaah\Flipbook\Tests\Feature;

use Djouamaah\Flipbook\Models\Flipbook;
use Djouamaah\Flipbook\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FlipbookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_index_and_create_pages_render(): void
    {
        $this->get(route('flipbook.index'))->assertOk()->assertSee('لا توجد كتب بعد');
        $this->get(route('flipbook.create'))->assertOk();
    }

    public function test_it_stores_a_pdf_flipbook(): void
    {
        $response = $this->post(route('flipbook.store'), [
            'title' => 'كتاب تجريبي',
            'type' => 'pdf',
            'rtl' => '1',
            'pdf' => UploadedFile::fake()->create('book.pdf', 200, 'application/pdf'),
        ]);

        $book = Flipbook::firstOrFail();
        $response->assertRedirect(route('flipbook.show', $book));
        $this->assertTrue($book->rtl);
        Storage::disk('public')->assertExists($book->pdf_path);

        $this->get(route('flipbook.show', $book))->assertOk()->assertSee('كتاب تجريبي');
        $this->get(route('flipbook.embed', $book))->assertOk();

        $this->get(route('flipbook.document', $book))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/x-flipbook')
            ->assertHeaderMissing('Content-Disposition');
    }

    public function test_it_stores_images_in_natural_filename_order(): void
    {
        $this->post(route('flipbook.store'), [
            'title' => 'Album',
            'type' => 'images',
            'rtl' => '0',
            'images' => [
                UploadedFile::fake()->image('10.jpg'),
                UploadedFile::fake()->image('2.jpg'),
                UploadedFile::fake()->image('1.jpg'),
            ],
        ])->assertRedirect();

        $book = Flipbook::firstOrFail();
        $this->assertFalse($book->rtl);
        $this->assertCount(3, $book->images);
        $this->assertStringEndsWith('0001.jpg', $book->images[0]);
        foreach ($book->images as $path) {
            Storage::disk('public')->assertExists($path);
        }

        $this->get(route('flipbook.index'))->assertOk()->assertSee('Album');
    }

    public function test_it_requires_a_file_matching_the_type(): void
    {
        $this->post(route('flipbook.store'), ['title' => 'x', 'type' => 'pdf'])
            ->assertSessionHasErrors('pdf');

        $this->post(route('flipbook.store'), [
            'title' => 'x',
            'type' => 'images',
            'images' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors('images.0');
    }

    public function test_deleting_removes_files(): void
    {
        $this->post(route('flipbook.store'), [
            'title' => 'Delete me',
            'type' => 'images',
            'images' => [UploadedFile::fake()->image('1.png')],
        ]);
        $book = Flipbook::firstOrFail();

        $this->delete(route('flipbook.destroy', $book))->assertRedirect(route('flipbook.index'));

        $this->assertModelMissing($book);
        Storage::disk('public')->assertMissing($book->images[0]);
    }
}
