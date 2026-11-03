<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 8:
 * 1. Guru punya akses kelola galeri di /guru/gallery* (tampil dengan sidebar guru).
 * 2. Guru dengan jabatan Kepala Program / Kepala Jurusan / Kaprog hanya
 *    mengelola konten jurusan di kewenangannya (TKJ hanya TKJ, dst).
 */
class GuruGalleryAndJurusanTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_access_gallery_management(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get(route('guru.gallery.index'));
        $response->assertOk();
        $response->assertSee('Kelola Galeri');
        $response->assertSee('Dashboard Guru');

        $this->actingAs($guru)->post(route('guru.gallery.store'), [
            'title' => 'Foto Kegiatan Kelas',
            'category' => 'Kegiatan',
            'images' => [UploadedFile::fake()->image('g1.jpg', 800, 600)],
        ])->assertRedirect(route('guru.gallery.index'));

        $this->assertDatabaseHas('galleries', [
            'title' => 'Foto Kegiatan Kelas',
            'uploaded_by' => $guru->id,
        ]);
    }

    public function test_non_guru_cannot_access_guru_gallery(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('guru.gallery.index'))->assertNotFound();
        $this->actingAs($siswa)->get(route('guru.gallery.index'))->assertNotFound();
    }

    public function test_kepala_program_can_see_and_edit_own_jurusan(): void
    {
        Storage::fake('public');
        Jurusan::create(['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ', 'color' => '#1c398e', 'slug' => 'tkj']);
        Jurusan::create(['name' => 'Agribisnis Tanaman Perkebunan', 'code' => 'ATP', 'color' => '#0d542b', 'slug' => 'atp']);

        $kepalaTkj = User::factory()->create([
            'role' => 'guru',
            'position' => 'Kepala Program TKJ',
        ]);

        $guruBiasa = User::factory()->create([
            'role' => 'guru',
            'position' => 'Guru Mata Pelajaran Umum',
        ]);

        $tkj = Jurusan::where('code', 'TKJ')->first();
        $atp = Jurusan::where('code', 'ATP')->first();

        // Kepala Program melihat halaman daftar dengan jurusan miliknya saja
        $index = $this->actingAs($kepalaTkj)->get(route('guru.jurusan.index'));
        $index->assertOk();
        $index->assertSee('Program Keahlian Saya');
        $index->assertSee('TKJ');

        // Guru biasa tidak diberi menu/halaman jurusan
        $this->actingAs($guruBiasa)->get(route('guru.jurusan.index'))->assertOk();
        // (tidak crash, dikelola null)

        // Kepala Program TKJ dapat mengedit jurusan TKJ
        $edit = $this->actingAs($kepalaTkj)->get(route('guru.jurusan.edit', $tkj));
        $edit->assertOk();
        $edit->assertSee('Edit Konten Jurusan TKJ');

        // Namun tidak dapat mengedit jurusan ATP
        $this->actingAs($kepalaTkj)->get(route('guru.jurusan.edit', $atp))->assertNotFound();

        $update = $this->actingAs($kepalaTkj)->patch(route('guru.jurusan.update', $tkj), [
            'description' => 'Deskripsi TKJ diperbarui kepala program.',
            'competencies' => 'Jaringan Komputer, Keamanan Jaringan',
            'image' => UploadedFile::fake()->image('tkj.jpg', 800, 450),
        ]);
        $update->assertRedirect(route('guru.jurusan.edit', $tkj));

        $tkj->refresh();
        $this->assertSame('Deskripsi TKJ diperbarui kepala program.', $tkj->description);
        $this->assertSame('Jaringan Komputer, Keamanan Jaringan', $tkj->competencies);
        Storage::disk('public')->assertExists($tkj->image_path);
    }

    public function test_kepala_program_wickraft_and_kepala_jurusan_positions_are_recognized(): void
    {
        Jurusan::create(['name' => 'Akuntansi Keuangan Lembaga', 'code' => 'AKL', 'color' => '#e17100', 'slug' => 'akl']);

        $aklByPosition = User::factory()->create(['role' => 'guru', 'position' => 'Kepala Jurusan AKL']);
        $this->assertSame('AKL', $aklByPosition->managedJurusan()?->code);

        $aklByKaprog = User::factory()->create(['role' => 'guru', 'position' => 'Kaprog AKL']);
        $this->assertSame('AKL', $aklByKaprog->managedJurusan()?->code);

        $plainGuru = User::factory()->create(['role' => 'guru', 'position' => 'Waka Kurikulum']);
        $this->assertNull($plainGuru->managedJurusan());
    }

    public function test_kepala_program_cannot_delete_gallery_from_admin_routes(): void
    {
        $kepala = User::factory()->create(['role' => 'guru', 'position' => 'Kepala Program TKJ']);
        $photo = Gallery::factory()->create();

        $this->actingAs($kepala)->delete(route('admin.gallery.destroy', $photo))->assertNotFound();
        $this->assertDatabaseHas('galleries', ['id' => $photo->id]);
    }
}
