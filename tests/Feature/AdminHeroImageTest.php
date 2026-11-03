<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * TDD: upload gambar/cover hero pada kelola beranda admin,
 * dan beranda tidak lagi memakai background statis Unsplash saat gambar tersedia.
 */
class AdminHeroImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_hero_image_upload_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.home.index'));

        $response->assertOk();
        $response->assertSee('Foto Hero');
    }

    public function test_admin_can_upload_hero_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_badge' => 'Penerimaan Peserta Didik Baru Terbuka',
            'hero_title' => 'Pendidikan Berkualitas untuk Generasi Emas',
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 1600, 900),
        ]);

        $response->assertRedirect(route('admin.home.index'));

        $path = Setting::get('hero_image');
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_can_replace_hero_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => 'Judul Pertama',
            'hero_image' => UploadedFile::fake()->image('hero-lama.jpg', 1600, 900),
        ]);

        $oldPath = Setting::get('hero_image');
        $this->assertNotNull($oldPath);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => 'Judul Kedua',
            'hero_image' => UploadedFile::fake()->image('hero-baru.jpg', 1600, 900),
        ])->assertRedirect(route('admin.home.index'));

        $newPath = Setting::get('hero_image');
        $this->assertNotSame($oldPath, $newPath);
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_admin_can_remove_hero_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => 'Judul Hero',
            'hero_image' => UploadedFile::fake()->image('hero.jpg', 1600, 900),
        ]);

        $path = Setting::get('hero_image');
        $this->assertNotNull($path);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => 'Judul Hero',
            'remove_hero_image' => '1',
        ])->assertRedirect(route('admin.home.index'));

        $this->assertNull(Setting::get('hero_image'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_public_homepage_renders_uploaded_hero_image(): void
    {
        Storage::fake('public');

        Setting::set('hero_image', UploadedFile::fake()->image('hero.jpg', 1600, 900)->store('beranda', 'public'));

        $response = $this->get(route('home'));

        $response->assertOk();

        $path = Setting::get('hero_image');
        $response->assertSee($path, false);

        // Unsplash statis tidak boleh tampil saat gambar hero tersedia
        $response->assertDontSee('images.unsplash.com', false);
    }
}
