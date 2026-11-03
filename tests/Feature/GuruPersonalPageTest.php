<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8g — Halaman pribadi guru (publik):
 * - Guru mengkustomisasi lewat /guru/pribadi (headline, CV, prestasi).
 * - Publik mengakses /guru/{id}: biodata + CV + prestasi + artikel; NIP tidak tampil.
 * - Kartu guru di /profil berisi tombol "Kunjungi Halaman".
 */
class GuruPersonalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_customize_and_public_page_renders_content(): void
    {
        $guru = User::factory()->create([
            'role' => 'guru',
            'position' => 'Guru Produktif TKJ',
            'nip' => '197501012003121001',
            'bio' => 'Guru TKJ senang berbagi modul.',
        ]);

        // Edit akun sendiri
        $this->actingAs($guru)->get(route('guru.pribadi.edit'))->assertOk();

        $this->actingAs($guru)->patch(route('guru.pribadi.update'), [
            'headline' => 'Ahli Jaringan & Mikrotik',
            'cv_content' => "Riwayat pendidikan:\n- S1 Pendidikan Teknik Elektronika\n\nSertifikasi:\n- MTCNA (2022)",
            'prestasi' => "Juara 1 LKS Nasional IT Network\nSertifikasi MTCNA",
        ])->assertRedirect(route('guru.pribadi.edit'));

        // Tampilkan publik
        $pub = $this->get(route('guru.public.show', $guru));
        $pub->assertOk();
        $pub->assertSee($guru->name);
        $pub->assertSee('Ahli Jaringan & Mikrotik');
        $pub->assertSee('Juara 1 LKS Nasional IT Network');
        $pub->assertSee('Guru TKJ senang berbagi modul.');
        // CV tampil sebagai konten (bukan file unduhan)
        $pub->assertSee('Curriculum Vitae');
        $pub->assertSee('S1 Pendidikan Teknik Elektronika');
        $pub->assertSee('MTCNA (2022)');
        $pub->assertDontSee('Unduh CV');
        // NIP tidak pernah tampil
        $pub->assertDontSee('197501012003121001');
    }

    public function test_public_page_lists_published_articles_by_guru(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $other = User::factory()->create(['role' => 'guru']);

        News::create([
            'title' => 'Modul Konfigurasi VLAN TKJ',
            'content' => 'Isi modul VLAN.',
            'category' => 'news',
            'author_id' => $guru->id,
            'status' => 'published',
        ]);
        News::create([
            'title' => 'Draft Rahasia Ori',
            'content' => 'Draft not published.',
            'category' => 'news',
            'author_id' => $guru->id,
            'status' => 'draft',
        ]);
        News::create([
            'title' => 'Artikel Doktor Lain',
            'content' => 'Karya guru lain.',
            'category' => 'news',
            'author_id' => $other->id,
            'status' => 'published',
        ]);

        $pub = $this->get(route('guru.public.show', $guru));
        $pub->assertOk();
        $pub->assertSee('Modul Konfigurasi VLAN TKJ');
        $pub->assertDontSee('Draft Rahasia Ori');
        $pub->assertDontSee('Artikel Doktor Lain');
    }

    public function test_guru_can_edit_bio_from_profile_form(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'bio' => null]);

        // Input "Tentang Guru" tampil di form profil
        $view = $this->actingAs($guru)->get(route('profile.edit'));
        $view->assertOk();
        $view->assertSee('Tentang Guru');

        $this->actingAs($guru)->patch(route('profile.update'), [
            'name' => $guru->name,
            'email' => $guru->email,
            'bio' => 'Tentang versi guru sendiri.',
        ])->assertRedirect(route('profile.edit'));

        $this->assertSame('Tentang versi guru sendiri.', $guru->refresh()->bio);

        // Bio tampil di halaman publik
        $this->get(route('guru.public.show', $guru))->assertSee('Tentang versi guru sendiri.');
    }

    public function test_non_guru_page_is_404(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->get(route('guru.public.show', $admin))->assertNotFound();

        // route /guru/pribadi tidak tertangkap route publik (whereNumber)
        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get(route('guru.pribadi.edit'))->assertOk();
    }

    public function test_school_profile_teacher_card_shows_visit_button(): void
    {
        $guru = User::factory()->create([
            'role' => 'guru',
            'position' => 'Guru Bahasa',
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'position' => 'Waka Sarpras']);

        $page = $this->get(route('profil'));
        $page->assertOk();

        $content = $page->getContent();
        // Tombol hanya tampil untuk kartu guru, bukan admin
        $this->assertStringContainsString(route('guru.public.show', $guru), $content);
        $this->assertStringContainsString('Kunjungi Halaman', $content);
        $this->assertStringNotContainsString(route('guru.public.show', $admin), $content);
    }
}
