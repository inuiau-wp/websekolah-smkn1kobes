<?php

namespace Tests\Feature;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_edit_profil(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        SchoolProfile::create(['id' => 1, 'school_name' => 'SMKN 1 Kotabesi', 'tagline' => 'Lama']);

        $response = $this->actingAs($admin)->get(route('admin.profil.edit'));

        $response->assertOk();
        $response->assertSee('SMKN 1 Kotabesi');
    }

    public function test_admin_can_update_profil_and_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        SchoolProfile::create(['id' => 1, 'school_name' => 'SMKN 1 Kotabesi', 'logo_path' => 'profil/lama.png']);
        Storage::disk('public')->put('profil/lama.png', 'x');

        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi Baru',
            'tagline' => 'Tagline Baru',
            'address' => 'Jl. Contoh No. 1',
            'phone' => '(0123) 456789',
            'email' => 'info@contoh.sch.id',
            'wa_number' => '628123456789',
            'vision' => 'Visi baru sekolah.',
            'mission' => "Misi satu.\nMisi dua.",
            'history' => 'Sejarah singkat.',
            'accreditation' => 'A',
            'headmaster' => 'Budi Rektor',
            'logo' => UploadedFile::fake()->image('logo.png', 200, 100),
        ])->assertRedirect(route('admin.profil.edit'));

        $profil = SchoolProfile::query()->first();

        $this->assertSame('SMKN 1 Kotabesi Baru', $profil->school_name);
        $this->assertSame('Tagline Baru', $profil->tagline);
        $this->assertSame('A', $profil->accreditation);
        $this->assertSame('Budi Rektor', $profil->headmaster);
        $this->assertNotSame('profil/lama.png', $profil->logo_path);
        Storage::disk('public')->assertMissing('profil/lama.png');
        Storage::disk('public')->assertExists($profil->logo_path);
    }

    public function test_profil_validation_requires_school_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => '',
        ])->assertSessionHasErrors('school_name');
    }

    public function test_non_admin_cannot_access_profil(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('admin.profil.edit'))->assertNotFound();
        $this->actingAs($guru)->put(route('admin.profil.update'), ['school_name' => 'X'])->assertNotFound();
    }
}
