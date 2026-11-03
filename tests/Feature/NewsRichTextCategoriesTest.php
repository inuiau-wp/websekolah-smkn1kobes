<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8h — Posting berita/modul dengan rich text & kategori baru:
 * - Kategori baru: materi (Modul Materi) & tutorial (Tutorial).
 * - Konten rich text (TinyMCE) disimpan sebagai HTML, tersanitasi & tampil publik.
 */
class NewsRichTextCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_include_materi_and_tutorial(): void
    {
        $this->assertSame([
            'news' => 'Berita',
            'announcement' => 'Pengumuman',
            'agenda' => 'Agenda',
            'materi' => 'Modul Materi',
            'tutorial' => 'Tutorial',
        ], News::categories());
    }

    public function test_admin_can_create_news_with_new_categories_and_large_html_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Konten HTML sangat besar (>20.000 karakter — limit lama)
        $content = '<h2>Modul VLAN</h2><p>Langkah konfigurasi:</p><ol>'.str_repeat('<li>Langkah konfigurasi switch dan router VLAN pada jaringan sekolah.</li>', 700).'</ol>';

        foreach (['materi', 'tutorial'] as $category) {
            $this->actingAs($admin)->post(route('admin.news.store'), [
                'title' => "Uji Kategori {$category}",
                'category' => $category,
                'status' => 'published',
                'content' => $content,
            ])->assertRedirect(route('admin.news.index'));

            $news = News::query()->where('title', "Uji Kategori {$category}")->firstOrFail();
            $this->assertSame($category, $news->category);
            $this->assertGreaterThan(20000, mb_strlen($news->content ?? ''));

            // Tampil publik sebagai HTML
            $this->get(route('news.show', $news->slug))
                ->assertOk()
                ->assertSee('Modul VLAN', false);
        }
    }

    public function test_invalid_category_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Kategori Aneh',
            'category' => 'haha',
            'status' => 'published',
            'content' => 'isi',
        ])->assertSessionHasErrors('category');

        $this->assertSame(0, News::query()->where('title', 'Kategori Aneh')->count());
    }

    public function test_guru_can_create_module_for_students(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Tutorial Konfigurasi Mikrotik Dasar',
            'category' => 'tutorial',
            'status' => 'published',
            'content' => '<p>Langkah 1: login ke Winbox.</p>',
        ])->assertRedirect(route('guru.news.index'));

        $news = News::query()->where('author_id', $guru->id)->firstOrFail();
        $this->assertSame('tutorial', $news->category);
        $this->assertSame('Tutorial', $news->categoryLabel());
    }

    public function test_content_is_sanitized_on_save(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Sanitasi Uji',
            'category' => 'materi',
            'status' => 'published',
            'content' => '<p>AMAN</p><script>alert("xss")</script><img src="https://x/y.png" onerror="alert(1)"><b>bold ok</b>',
        ])->assertRedirect(route('admin.news.index'));

        $news = News::query()->where('title', 'Sanitasi Uji')->firstOrFail();
        $this->assertStringNotContainsString('script', (string) $news->content);
        $this->assertStringNotContainsString('onerror', (string) $news->content);
        $this->assertStringContainsString('AMAN', (string) $news->content);
        $this->assertStringContainsString('bold ok', (string) $news->content);
    }
}
