<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminJurusanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_jurusan_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Jurusan::create([
            'name' => 'Agribisnis Tanaman Perkebunan',
            'code' => 'ATP',
            'color' => '#0d542b',
            'slug' => 'atp',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.jurusan.index'));

        $response->assertOk();
        $response->assertSee('Agribisnis Tanaman Perkebunan');
    }

    public function test_admin_can_create_jurusan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.jurusan.store'), [
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'color' => '#1c398e',
            'description' => 'Jurusan jaringan komputer.',
            'competencies' => 'Jaringan, Sistem Operasi, Keamanan Jaringan',
            'head_of_department' => 'Budi Guru',
        ]);

        $response->assertRedirect(route('admin.jurusan.index'));

        $this->assertDatabaseHas('jurusan', [
            'code' => 'TKJ',
            'slug' => 'teknik-komputer-dan-jaringan', // slug otomatis dari nama
            'head_of_department' => 'Budi Guru',
        ]);
    }

    public function test_admin_can_upload_photo_for_jurusan(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.jurusan.store'), [
            'name' => 'Akuntansi Keuangan Lembaga',
            'code' => 'AKL',
            'color' => '#e17100',
            'image' => UploadedFile::fake()->image('foto-akl.jpg', 600, 400),
        ]);

        $jurusan = Jurusan::query()->where('code', 'AKL')->first();

        Storage::disk('public')->assertExists($jurusan->image_path);
        $this->assertSame('Akuntansi Keuangan Lembaga', $jurusan->name);
    }

    public function test_admin_can_update_jurusan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $jurusan = Jurusan::create([
            'name' => 'Agribisnis Tanaman Perkebunan',
            'code' => 'ATP',
            'color' => '#0d542b',
            'slug' => 'atp',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.jurusan.update', $jurusan), [
            'name' => 'Agribisnis Tanaman Perkebunan',
            'code' => 'ATP',
            'color' => '#1a6b38',
            'head_of_department' => 'Ketua Baru',
        ]);

        $response->assertRedirect(route('admin.jurusan.index'));

        $this->assertDatabaseHas('jurusan', [
            'id' => $jurusan->id,
            'color' => '#1a6b38',
            'head_of_department' => 'Ketua Baru',
        ]);
    }

    public function test_admin_can_delete_jurusan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $jurusan = Jurusan::create([
            'name' => 'Agribisnis Tanaman Perkebunan',
            'code' => 'ATP',
            'color' => '#0d542b',
            'slug' => 'atp',
        ]);

        $this->actingAs($admin)->delete(route('admin.jurusan.destroy', $jurusan))
            ->assertRedirect(route('admin.jurusan.index'));

        $this->assertDatabaseMissing('jurusan', ['id' => $jurusan->id]);
    }

    public function test_validation_rejects_duplicate_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Jurusan::create(['name' => 'Agribisnis Tanaman Perkebunan', 'code' => 'ATP', 'color' => '#0d542b', 'slug' => 'atp']);

        $response = $this->actingAs($admin)->post(route('admin.jurusan.store'), [
            'name' => 'Jurusan Lain',
            'code' => 'ATP',
            'color' => '#000000',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_non_admin_cannot_manage_jurusan(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get(route('admin.jurusan.index'))->assertNotFound();
        $this->actingAs($guru)->post(route('admin.jurusan.store'), ['name' => 'X', 'code' => 'X', 'color' => '#000000'])->assertNotFound();
        $this->actingAs($siswa)->get(route('admin.jurusan.index'))->assertNotFound();
    }

    public function test_admin_can_link_head_of_department_user_to_jurusan(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create([
            'name' => 'Ahmad Hidayat, M.Kom.',
            'role' => 'guru',
            'position' => 'Kepala Program TKJ',
            'photo' => UploadedFile::fake()->image('ahmad.jpg')->store('users', 'public'),
        ]);

        $jurusan = Jurusan::create([
            'name' => 'Teknik Komputer dan Jaringan',
            'code' => 'TKJ',
            'color' => '#1c398e',
            'slug' => 'tkj',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.jurusan.update', $jurusan), [
            'name' => $jurusan->name,
            'code' => $jurusan->code,
            'color' => $jurusan->color,
            'head_of_department_id' => $guru->id,
        ]);

        $response->assertRedirect(route('admin.jurusan.index'));
        $this->assertDatabaseHas('jurusan', [
            'id' => $jurusan->id,
            'head_of_department_id' => $guru->id,
        ]);

        // Uji tampilan publik detail jurusan
        $publicResponse = $this->get(route('jurusan.show', $jurusan->slug));
        $publicResponse->assertOk();
        $publicResponse->assertSee('Kepala Jurusan');
        $publicResponse->assertDontSee('Ketua Jurusan');
        $publicResponse->assertSee('Ahmad Hidayat, M.Kom.');
        $publicResponse->assertSee($guru->photo_url);
    }
}
