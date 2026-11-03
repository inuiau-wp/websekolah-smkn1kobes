<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_gallery_grid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Gallery::factory()->create(['title' => 'Foto Uji Grid']);

        $response = $this->actingAs($admin)->get(route('admin.gallery.index'));

        $response->assertOk();
        $response->assertSee('Foto Uji Grid');
    }

    public function test_admin_can_upload_multiple_photos(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Kegiatan Ekstrakurikuler',
            'category' => 'Kegiatan',
            'images' => [
                UploadedFile::fake()->image('a.jpg', 600, 450),
                UploadedFile::fake()->image('b.jpg', 600, 450),
                UploadedFile::fake()->image('c.jpg', 600, 450),
            ],
        ]);

        $response->assertRedirect(route('admin.gallery.index'));

        $photos = Gallery::query()->where('title', 'Kegiatan Ekstrakurikuler')->get();
        $this->assertCount(3, $photos); // setiap berkas = 1 entri
        $this->assertSame('Kegiatan', $photos->first()->category);
        $this->assertSame($admin->id, $photos->first()->uploaded_by);

        foreach ($photos as $photo) {
            Storage::disk('public')->assertExists($photo->image_path);
        }
    }

    public function test_store_requires_at_least_one_image(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.gallery.store'), [
            'title' => 'Tanpa Foto',
        ]);

        $response->assertSessionHasErrors('images');
        $this->assertDatabaseMissing('galleries', ['title' => 'Tanpa Foto']);
    }

    public function test_admin_can_update_photo_metadata(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $photo = Gallery::factory()->create([
            'title' => 'Judul Awal',
            'category' => 'Kegiatan',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.gallery.update', $photo), [
            'title' => 'Judul Baru',
            'category' => 'Fasilitas',
        ]);

        $response->assertRedirect(route('admin.gallery.index'));

        $photo->refresh();
        $this->assertSame('Judul Baru', $photo->title);
        $this->assertSame('Fasilitas', $photo->category);
    }

    public function test_admin_can_replace_photo_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $photo = Gallery::factory()->create(['image_path' => 'galeri/lama.jpg']);
        Storage::disk('public')->put('galeri/lama.jpg', 'x');

        $this->actingAs($admin)->patch(route('admin.gallery.update', $photo), [
            'title' => 'Judul Awal',
            'image' => UploadedFile::fake()->image('baru.jpg', 600, 450),
        ]);

        $photo->refresh();

        $this->assertNotSame('galeri/lama.jpg', $photo->image_path);
        Storage::disk('public')->assertMissing('galeri/lama.jpg');
        Storage::disk('public')->assertExists($photo->image_path);
    }

    public function test_admin_can_delete_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $photo = Gallery::factory()->create(['image_path' => 'galeri/hapus.jpg']);
        Storage::disk('public')->put('galeri/hapus.jpg', 'x');

        $this->actingAs($admin)->delete(route('admin.gallery.destroy', $photo))
            ->assertRedirect(route('admin.gallery.index'));

        $this->assertDatabaseMissing('galleries', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('galeri/hapus.jpg'); // berkas ikut terhapus
    }

    public function test_non_admin_cannot_manage_gallery(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get(route('admin.gallery.index'))->assertNotFound();
        $this->actingAs($siswa)->post(route('admin.gallery.store'), ['title' => 'X'])->assertNotFound();
    }
}
