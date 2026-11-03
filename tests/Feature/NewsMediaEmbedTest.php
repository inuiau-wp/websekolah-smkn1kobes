<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 8h-r — insert gambar di TinyMCE + embed post Instagram:
 * - Endpoint upload gambar (admin & guru) mengembalikan `location` (storage publik).
 * - Blockquote `instagram-media` (class + data-instgrm-permalink URL valid) selamat dari
 *   sanitasi; atribut tak valid/berbahaya tetap dibuang.
 * - Halaman detail memuat embed.js Instagram hanya jika kontennya ada embed itu.
 */
class NewsMediaEmbedTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_guru_can_upload_content_image(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->post(route('news.image-upload.store'), [
            'file' => UploadedFile::fake()->image('grafik.jpg', 800, 600),
        ]);

        $response->assertOk();
        $location = $response->json('location');
        $this->assertNotNull($location);
        $this->assertStringContainsString('news/images', $location);
        $this->assertMatchesRegularExpression('#/storage/news/images/[\w.-]+\.jpg$#', $location);
    }

    public function test_image_upload_requires_auth_and_valid_file(): void
    {
        Storage::fake('public');

        // Tamu ditolak
        $this->post(route('news.image-upload.store'), [
            'file' => UploadedFile::fake()->image('x.jpg'),
        ])->assertRedirect(route('login'));

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->post(route('news.image-upload.store'), [
            'file' => UploadedFile::fake()->create('virus.exe', 100),
        ])->assertSessionHasErrors('file');
    }

    public function test_sanitizer_preserves_image_dimensions_from_editor(): void
    {
        $img = '<p><img src="https://smkn1kotabesi.test/storage/news/images/a.jpg" alt="Grafik" width="450" height="300"></p>';
        $clean = News::sanitizeContent($img);

        $this->assertStringContainsString('width="450"', (string) $clean);
        $this->assertStringContainsString('height="300"', (string) $clean);

        // Nilai berbahaya/non-angka dibuang
        $dirty = '<p><img src="/storage/news/images/b.png" width="alert(1)" height="-5"></p>';
        $clean2 = News::sanitizeContent($dirty);
        $this->assertStringNotContainsString('alert(1)', (string) $clean2);
        $this->assertStringNotContainsString('height="-5"', (string) $clean2);
    }

    public function test_sanitizer_preserves_editor_text_alignment_and_float(): void
    {
        $content = '<p style="text-align: center;">Tengah</p>'
            .'<h2 style="text-align: right;">Kanan</h2>'
            .'<img src="/storage/news/images/a.jpg" style="float: right; margin-left: 12px;" width="300"">'
            .'<p style="color: red; position: absolute;">Bahaya</p>';

        $clean = News::sanitizeContent($content);

        $this->assertStringContainsString('<p style="text-align: center">', (string) $clean);
        $this->assertStringContainsString('<h2 style="text-align: right">', (string) $clean);
        $this->assertStringContainsString('<img src="/storage/news/images/a.jpg" style="float: right; margin-left: 12px;"', (string) $clean);

        // Gambar di-center TinyMCE: display block + margin auto
        $centered = News::sanitizeContent('<img src="/storage/news/images/c.png" style="display: block; margin-left: auto; margin-right: auto;" width="500">');
        $this->assertStringContainsString('display: block; margin-left: auto; margin-right: auto', (string) $centered);
        // deklarasi berbahaya dibuang
        $this->assertStringNotContainsString('color: red', (string) $clean);
        $this->assertStringNotContainsString('position: absolute', (string) $clean);
    }

    public function test_sanitizer_preserves_instagram_embed_and_strips_others(): void
    {
        $valid = '<blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/p/Cxyz123/" data-instgrm-version="14" onclick="alert(1)"><p>Sekolah</p></blockquote>';
        $dirty = '<blockquote class="instagram-media" data-instgrm-permalink="https://evil.example.com/steal/"><script>alert(1)</script></blockquote>';

        $clean = News::sanitizeContent($valid.$dirty);

        $this->assertStringContainsString('class="instagram-media"', (string) $clean);
        $this->assertStringContainsString('data-instgrm-permalink="https://www.instagram.com/p/Cxyz123/"', (string) $clean);
        // Atribut lain & permalink invalid tidak lolos
        $this->assertStringNotContainsString('data-instgrm-version', (string) $clean);
        $this->assertStringNotContainsString('onclick', (string) $clean);
        $this->assertStringNotContainsString('evil.example.com', (string) $clean);
        $this->assertStringNotContainsString('script', (string) $clean);
    }

    public function test_show_page_loads_instagram_embed_script_only_when_present(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $withEmbed = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Berita Ada Instagram',
            'category' => 'news',
            'status' => 'published',
            'content' => '<p>Kegiatan:</p><blockquote class="instagram-media" data-instgrm-permalink="https://www.instagram.com/reel/Cabc123/"></blockquote>',
        ])->assertRedirect(route('admin.news.index'));

        $news = News::query()->where('title', 'Berita Ada Instagram')->firstOrFail();

        $this->get(route('news.show', $news->slug))
            ->assertOk()
            ->assertSee('instagram.com/embed.js', false);

        // Tanpa embed — script tidak dimuat
        $tanpa = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Berita Normal',
            'category' => 'news',
            'status' => 'published',
            'content' => '<p>Isi biasa saja.</p>',
        ])->assertRedirect(route('admin.news.index'));

        $normal = News::query()->where('title', 'Berita Normal')->firstOrFail();
        $this->get(route('news.show', $normal->slug))
            ->assertOk()
            ->assertDontSee('instagram.com/embed.js', false);
    }
}
