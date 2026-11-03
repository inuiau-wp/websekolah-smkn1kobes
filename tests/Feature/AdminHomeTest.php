<?php

namespace Tests\Feature;

use App\Models\CarouselSlide;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_home_content_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.home.index'));

        $response->assertOk();
    }

    public function test_admin_can_store_slide(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.store', 'slides'), [
            'title' => 'Slide Uji Coba',
            'description' => 'Deskripsi slide.',
            'sort' => 1,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('slide.jpg', 800, 450),
        ])->assertRedirect(route('admin.home.index'));

        $slide = CarouselSlide::query()->where('title', 'Slide Uji Coba')->first();

        $this->assertNotNull($slide);
        $this->assertTrue($slide->is_active);
        Storage::disk('public')->assertExists($slide->image_path);
    }

    public function test_admin_can_update_slide_and_replace_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $slide = CarouselSlide::create([
            'title' => 'Lama', 'image_path' => 'beranda/lama.jpg', 'sort' => 1, 'is_active' => true,
        ]);
        Storage::disk('public')->put('beranda/lama.jpg', 'x');

        $this->actingAs($admin)->patch(route('admin.home.update', ['slides', $slide->id]), [
            'title' => 'Baru',
            'image' => UploadedFile::fake()->image('baru.jpg', 800, 450),
        ])->assertRedirect(route('admin.home.index'));

        $slide->refresh();
        $this->assertSame('Baru', $slide->title);
        $this->assertNotSame('beranda/lama.jpg', $slide->image_path);
        Storage::disk('public')->assertMissing('beranda/lama.jpg');
        Storage::disk('public')->assertExists($slide->image_path);
    }

    public function test_admin_can_destroy_slide_and_delete_file(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $slide = CarouselSlide::create(['title' => 'Hapus', 'image_path' => 'beranda/hapus.jpg']);
        Storage::disk('public')->put('beranda/hapus.jpg', 'x');

        $this->actingAs($admin)->delete(route('admin.home.destroy', ['slides', $slide->id]))
            ->assertRedirect(route('admin.home.index'));

        $this->assertDatabaseMissing('carousel_slides', ['id' => $slide->id]);
        Storage::disk('public')->assertMissing('beranda/hapus.jpg');
    }

    public function test_admin_can_store_statistic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.store', 'statistics'), [
            'label' => 'Siswa Aktif',
            'value' => '1000+',
            'sort' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.home.index'));

        $this->assertDatabaseHas('statistics', ['label' => 'Siswa Aktif', 'value' => '1000+', 'is_active' => true]);
    }

    public function test_admin_can_update_statistic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $stat = Statistic::create(['label' => 'Lama', 'value' => '1', 'sort' => 1]);

        $this->actingAs($admin)->patch(route('admin.home.update', ['statistics', $stat->id]), [
            'label' => 'Baru', 'value' => '999', 'is_active' => true,
        ])->assertRedirect(route('admin.home.index'));

        $stat->refresh();
        $this->assertSame('Baru', $stat->label);
        $this->assertSame('999', $stat->value);
    }

    public function test_admin_can_destroy_statistic(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $stat = Statistic::create(['label' => 'Hapus', 'value' => '1']);

        $this->actingAs($admin)->delete(route('admin.home.destroy', ['statistics', $stat->id]))
            ->assertRedirect(route('admin.home.index'));

        $this->assertDatabaseMissing('statistics', ['id' => $stat->id]);
    }

    public function test_admin_can_store_testimonial_with_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.store', 'testimonials'), [
            'name' => 'Budi', 'role' => 'Orang Tua', 'content' => 'Sangat bagus.',
            'sort' => 1, 'is_active' => '1',
            'photo' => UploadedFile::fake()->image('foto.jpg', 400, 400),
        ])->assertRedirect(route('admin.home.index'));

        $testimonial = Testimonial::query()->where('name', 'Budi')->first();

        $this->assertNotNull($testimonial);
        Storage::disk('public')->assertExists($testimonial->photo_path);
    }

    public function test_admin_can_destroy_testimonial_and_delete_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $item = Testimonial::create(['name' => 'Hapus', 'content' => 'coba', 'photo_path' => 'beranda/foto.jpg']);
        Storage::disk('public')->put('beranda/foto.jpg', 'x');

        $this->actingAs($admin)->delete(route('admin.home.destroy', ['testimonials', $item->id]))
            ->assertRedirect(route('admin.home.index'));

        $this->assertDatabaseMissing('testimonials', ['id' => $item->id]);
        Storage::disk('public')->assertMissing('beranda/foto.jpg');
    }

    public function test_admin_can_store_partner_with_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.store', 'partners'), [
            'name' => 'Dinas Pendidikan', 'url' => 'https://dinas.example.com',
            'sort' => 1, 'is_active' => '1',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 100),
        ])->assertRedirect(route('admin.home.index'));

        $partner = Partner::query()->where('name', 'Dinas Pendidikan')->first();

        $this->assertNotNull($partner);
        Storage::disk('public')->assertExists($partner->logo_path);
    }

    public function test_admin_can_update_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => 'Judul Hero Baru',
            'hero_badge' => 'PPDB Dibuka',
            'section_carousel_title' => 'Kegiatan',
        ])->assertRedirect(route('admin.home.index'));

        $this->assertSame('Judul Hero Baru', Setting::get('hero_title'));
        $this->assertSame('PPDB Dibuka', Setting::get('hero_badge'));
        $this->assertSame('Kegiatan', Setting::get('section_carousel_title'));
    }

    public function test_settings_validation_requires_hero_title(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.home.settings'), [
            'hero_title' => '',
        ])->assertSessionHasErrors('hero_title');
    }

    public function test_non_admin_cannot_access_home_content(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get(route('admin.home.index'))->assertNotFound();
        $this->actingAs($siswa)->post(route('admin.home.store', 'statistics'), [
            'label' => 'X', 'value' => '1',
        ])->assertNotFound();
    }
}
