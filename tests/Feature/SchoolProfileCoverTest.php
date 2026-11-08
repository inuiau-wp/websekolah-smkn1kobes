<?php

namespace Tests\Feature;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Fase 8d — Gambar cover hero halaman /profil:
 * - Admin dapat mengunggah / mengganti / menghapus cover dari Kelola Profil Sekolah.
 * - Halaman publik /profil menampilkan cover unggahan; tanpa cover memakai gradient
 *   warna jurusan (bukan Unsplash lagi).
 */
class SchoolProfileCoverTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_cover_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'cover' => UploadedFile::fake()->image('cover.jpg', 1920, 600),
        ]);

        $response->assertRedirect(route('admin.profil.edit'));

        $profil = SchoolProfile::find(1);
        $this->assertNotNull($profil->cover_path);
        Storage::disk('public')->assertExists($profil->cover_path);
    }

    public function test_admin_can_replace_and_remove_cover(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        // Upload pertama
        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'cover' => UploadedFile::fake()->image('cover1.jpg'),
        ]);
        $first = SchoolProfile::find(1)->cover_path;
        Storage::disk('public')->assertExists($first);

        // Upload kedua menggantikan file lama
        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'cover' => UploadedFile::fake()->image('cover2.jpg'),
        ]);
        $second = SchoolProfile::find(1)->cover_path;
        $this->assertNotSame($first, $second);
        Storage::disk('public')->assertMissing($first);
        Storage::disk('public')->assertExists($second);

        // Hapus cover
        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'remove_cover' => '1',
        ]);
        $this->assertNull(SchoolProfile::find(1)->cover_path);
        Storage::disk('public')->assertMissing($second);
    }

    public function test_public_profile_uses_uploaded_cover_instead_of_unsplash(): void
    {
        $profil = SchoolProfile::find(1) ?? SchoolProfile::create(['school_name' => 'SMKN 1 Kotabesi']);
        $profil->update(['cover_path' => 'profil/cover-sekolah.jpg']);

        $response = $this->get(route('profil'));
        $response->assertOk();
        $response->assertSee(asset('storage/profil/cover-sekolah.jpg'), false);
        // Unsplash tidak boleh dipakai lagi di hero
        $response->assertDontSee('images.unsplash.com', false);
    }

    public function test_public_profile_falls_back_to_gradient_without_cover(): void
    {
        SchoolProfile::find(1)?->update(['cover_path' => null]) ?? SchoolProfile::create(['school_name' => 'SMKN 1 Kotabesi']);

        $this->get(route('profil'))
            ->assertOk()
            ->assertDontSee('images.unsplash.com', false)
            ->assertSee('linear-gradient(135deg, #1c398e', false);
    }
}
