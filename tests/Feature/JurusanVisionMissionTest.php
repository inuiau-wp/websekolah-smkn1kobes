<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8b — Visi & Misi jurusan:
 * - Admin dapat mengisi visi/misi dari form admin jurusan.
 * - Kepala Program / Kepala Jurusan dapat mengubah visi/misi jurusan miliknya.
 * - Halaman publik /jurusan/{slug} menampilkan section "Visi & Misi" tersendiri,
 *   dengan setiap baris misi menjadi satu poin.
 */
class JurusanVisionMissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_vision_and_mission(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $jurusan = Jurusan::create([
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'color' => '#1c398e',
            'slug' => 'tkj',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.jurusan.update', $jurusan), [
                'name' => $jurusan->name,
                'slug' => $jurusan->slug,
                'code' => 'TKJ',
                'color' => $jurusan->color,
                'description' => 'Deskripsi TKJ.',
                'competencies' => 'Jaringan, Server',
                'vision' => 'Menjadi jurusan TI unggulan.',
                'mission' => "1. Mengembangkan kompetensi siswa\n2. Membangun budaya inovasi\n3. Mempererat kerja sama industri",
            ])
            ->assertRedirect(route('admin.jurusan.index'));

        $jurusan->refresh();
        $this->assertSame('Menjadi jurusan TI unggulan.', $jurusan->vision);
        $this->assertStringContainsString('Membangun budaya inovasi', $jurusan->mission);
    }

    public function test_kepala_program_can_update_vision_and_mission_of_own_jurusan(): void
    {
        $jurusanAkl = Jurusan::create([
            'name' => 'Akuntansi Keuangan Lembaga',
            'code' => 'AKL',
            'color' => '#e17100',
            'slug' => 'akl',
        ]);

        $kepala = User::factory()->create([
            'role' => 'guru',
            'position' => 'Kepala Program AKL',
        ]);

        $this->actingAs($kepala)
            ->patch(route('guru.jurusan.update', $jurusanAkl), [
                'description' => 'Deskripsi AKL versi kaprog.',
                'competencies' => 'Akuntansi, Perpajakan',
                'vision' => 'Berakhlak, berintegritas, berprestasi.',
                'mission' => "1.pendidikan berkarakter\n2. praktik industri nyata",
            ])
            ->assertRedirect(route('guru.jurusan.edit', $jurusanAkl));

        $jurusanAkl->refresh();
        $this->assertSame('Berakhlak, berintegritas, berprestasi.', $jurusanAkl->vision);
        $this->assertStringContainsString('pendidikan berkarakter', $jurusanAkl->mission);
        $this->assertStringContainsString('praktik industri nyata', $jurusanAkl->mission);
    }

    public function test_public_jurusan_page_shows_dedicated_vision_mission_section_with_mission_lines(): void
    {
        $jurusan = Jurusan::create([
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'color' => '#1c398e',
            'slug' => 'tkj',
            'vision' => 'Menjadi jurusan jaringan komputer unggulan dan berdaya saing.',
            'mission' => "1. Pembelajaran berbasis praktik.\n\n2. Sertifikasi kompetensi siswa.",
        ]);

        $response = $this->get(route('jurusan.show', $jurusan->slug));
        $response->assertOk();

        $response->assertSee('Visi & Misi Jurusan');
        $response->assertSee('Visi');
        $response->assertSee('Misi');
        $response->assertSee('Menjadi jurusan jaringan komputer unggulan dan berdaya saing.');
        $response->assertSee('Pembelajaran berbasis praktik.');
        $response->assertSee('Sertifikasi kompetensi siswa.');

        // Baris kosong tidak dirender sebagai poin
        $content = $response->getContent();
        $this->assertLessThanOrEqual(2, substr_count($content, '>1. Pembelajaran berbasis praktik.<'));
    }
}
