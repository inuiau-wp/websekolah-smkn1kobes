<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_page_can_be_rendered(): void
    {
        Gallery::factory()->create(['title' => 'Upacara Uji Coba']);

        $response = $this->get(route('gallery.index'));

        $response->assertOk();
        $response->assertSee('Upacara Uji Coba');
    }

    public function test_gallery_page_shows_category_summary(): void
    {
        Gallery::factory()->create(['category' => 'Fasilitas']);
        Gallery::factory()->create(['category' => 'Fasilitas']);
        Gallery::factory()->create(['category' => 'Kegiatan']);

        $response = $this->get(route('gallery.index'));

        $response->assertOk();
        $response->assertSee('Fasilitas (2)');
        $response->assertSee('Kegiatan (1)');
    }

    public function test_gallery_page_shows_empty_state_when_no_photos(): void
    {
        $response = $this->get(route('gallery.index'));

        $response->assertOk();
        $response->assertSee('Belum ada foto');
    }
}
