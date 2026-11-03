<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_news_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        News::factory()->create(['title' => 'Kabar Uji Coba']);

        $response = $this->actingAs($admin)->get(route('admin.news.index'));

        $response->assertOk();
        $response->assertSee('Kabar Uji Coba');
    }

    public function test_admin_can_create_published_news(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Penerimaan Peserta Didik Baru 2026',
            'excerpt' => 'PPDB telah dibuka.',
            'content' => 'Isi lengkap berita...',
            'category' => 'announcement',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.news.index'));

        $news = News::query()->where('title', 'Penerimaan Peserta Didik Baru 2026')->first();
        $this->assertNotNull($news);
        $this->assertSame('announcement', $news->category);
        $this->assertSame('published', $news->status);
        $this->assertNotNull($news->published_at); // otomatis dari model
        $this->assertSame($admin->id, $news->author_id); // penulis = pembuat
    }

    public function test_admin_can_upload_cover(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Berita Uji Cover',
            'category' => 'news',
            'status' => 'draft',
            'cover' => UploadedFile::fake()->image('cover.jpg', 800, 500),
        ]);

        $news = News::query()->where('title', 'Berita Uji Cover')->first();

        Storage::disk('public')->assertExists($news->cover_image);
    }

    public function test_admin_can_update_news_and_replace_cover(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $news = News::factory()->create([
            'title' => 'Judul Awal',
            'status' => 'draft',
            'cover_image' => 'berita/lama.jpg',
        ]);
        Storage::disk('public')->put('berita/lama.jpg', 'x');

        $this->actingAs($admin)->patch(route('admin.news.update', $news), [
            'title' => 'Judul Baru',
            'category' => 'agenda',
            'status' => 'published',
            'cover' => UploadedFile::fake()->image('baru.jpg', 800, 500),
        ]);

        $news->refresh();

        $this->assertSame('Judul Baru', $news->title);
        $this->assertSame('agenda', $news->category);
        $this->assertSame($admin->id, $news->author_id);
        $this->assertNotSame('berita/lama.jpg', $news->cover_image);
        Storage::disk('public')->assertMissing('berita/lama.jpg');
        Storage::disk('public')->assertExists($news->cover_image);
    }

    public function test_admin_can_delete_news(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $news = News::factory()->create(['title' => 'Berita Terhapus']);

        $this->actingAs($admin)->delete(route('admin.news.destroy', $news))
            ->assertRedirect(route('admin.news.index'));

        $this->assertSoftDeleted($news); // softDeletes — tidak hilang permanen
    }

    public function test_validation_rejects_invalid_category_and_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Kategori Salah',
            'category' => 'dudette',
            'status' => 'mungkin',
        ])->assertSessionHasErrors(['category', 'status']);
    }

    public function test_news_can_be_related_to_jurusan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $jurusan = Jurusan::create(['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ', 'color' => '#1c398e', 'slug' => 'tkj']);

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Prestasi TKJ',
            'category' => 'news',
            'status' => 'published',
            'jurusan_id' => $jurusan->id,
        ]);

        $news = News::query()->where('title', 'Prestasi TKJ')->first();
        $this->assertSame($jurusan->id, $news->jurusan_id);
    }

    public function test_non_admin_cannot_manage_news(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('admin.news.index'))->assertNotFound();
        $this->actingAs($guru)->post(route('admin.news.store'), ['title' => 'X', 'category' => 'news', 'status' => 'draft'])->assertNotFound();
    }
}
