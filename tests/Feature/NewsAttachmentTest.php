<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 8h-p — Lampiran materi pada berita:
 * - .md   → isinya dikonversi jadi `content` (fallback server), file tidak disimpan.
 * - .pdf  → disimpan & tampil ter-embed (iframe) di halaman publik.
 * - .docx → disimpan & tampil tombol unduh.
 */
class NewsAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_upload_pdf_and_it_is_embedded_publicly(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Modul Jaringan Dasar PDF',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->create('modul.pdf', 800, 'application/pdf'),
        ])->assertRedirect(route('guru.news.index'));

        $news = News::query()->where('title', 'Modul Jaringan Dasar PDF')->firstOrFail();
        $this->assertSame('pdf', $news->file_type);
        Storage::disk('public')->assertExists($news->file_path);

        $show = $this->get(route('news.show', $news->slug));
        $show->assertOk();
        $show->assertSee('Unduh PDF');
        $show->assertSeeText('Modul Jaringan Dasar PDF');
    }

    public function test_guru_can_upload_docx_and_public_page_shows_download(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Modul Word Akuntansi',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->create('modul-akl.docx', 400, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ])->assertRedirect(route('guru.news.index'));

        $news = News::query()->where('title', 'Modul Word Akuntansi')->firstOrFail();
        $this->assertSame('docx', $news->file_type);
        $this->assertSame('modul-akl.docx', $news->file_original_name);
        Storage::disk('public')->assertExists($news->file_path);

        $this->get(route('news.show', $news->slug))
            ->assertOk()
            ->assertSee('Unduh DOCX');
    }

    public function test_md_file_content_is_converted_into_content_and_not_stored(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $markdown = "# Modul VLAN\n\n## Langkah 1\n- Siapkan switch\n- Login Winbox\n\n**Catatan:** gunakan VLAN 10.\n";

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Modul Dari Markdown',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->createWithContent('modul.md', $markdown),
        ])->assertRedirect(route('guru.news.index'));

        $news = News::query()->where('title', 'Modul Dari Markdown')->firstOrFail();
        // Fallback server: markdown dikonversi jadi HTML pada kolom content
        $this->assertSame('', (string) $news->file_path);
        $this->assertStringContainsString('Modul VLAN', (string) $news->content);
        $this->assertStringContainsString('Langkah 1', (string) $news->content);
        $this->assertStringContainsString('Siapkan switch', (string) $news->content);
        $this->assertStringContainsString('<strong>Catatan:</strong>', (string) $news->content);

        // Tidak ada file md yang tersimpan
        $files = Storage::disk('public')->allFiles('news/files');
        $this->assertSame([], $files);
    }

    public function test_invalid_attachment_format_is_rejected(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Lampiran Aneh',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->create('virus.exe', 100),
        ])->assertSessionHasErrors('attachment');

        $this->assertNull(News::query()->where('title', 'Lampiran Aneh')->first());
    }

    public function test_attachment_can_be_removed_and_is_deleted_on_news_delete(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Modul Hapus Lampiran',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->create('modul.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('guru.news.index'));

        $news = News::query()->where('title', 'Modul Hapus Lampiran')->firstOrFail();
        $path = $news->file_path;

        // Remove via checkbox
        $this->actingAs($guru)->patch(route('guru.news.update', $news), [
            'title' => 'Modul Hapus Lampiran',
            'category' => 'materi',
            'status' => 'published',
            'remove_attachment' => '1',
        ])->assertRedirect(route('guru.news.index'));

        $news->refresh();
        $this->assertNull($news->file_path);
        Storage::disk('public')->assertMissing($path);

        // Upload ulang lalu hapus berita → ikut terhapus
        $this->actingAs($guru)->patch(route('guru.news.update', $news), [
            'title' => 'Modul Hapus Lampiran',
            'category' => 'materi',
            'status' => 'published',
            'attachment' => UploadedFile::fake()->create('modul2.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('guru.news.index'));
        $news->refresh();
        $path = (string) $news->file_path;
        Storage::disk('public')->assertExists($path);

        $this->actingAs($guru)->delete(route('guru.news.destroy', $news))->assertRedirect(route('guru.news.index'));
        Storage::disk('public')->assertMissing($path);
    }
}
