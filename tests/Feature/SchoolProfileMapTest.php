<?php

namespace Tests\Feature;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8e — Peta kontak presisi via koordinat:
 * - Admin dapat mengisi koordinat (lat/lng) di Kelola Profil Sekolah.
 * - Peta halaman /kontak memakai koordinat bila tersedia (dan tidak memakai alamat).
 * - Tanpa koordinat, peta jatuh ke nama sekolah — alamat bebas tidak dipakai sebagai query.
 */
class SchoolProfileMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_set_and_clear_map_coordinates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'address' => 'Jl. Pendidikan No. 1, Kotabesi',
            'map_latitude' => '-2.416326',
            'map_longitude' => '112.9857232',
        ])->assertRedirect(route('admin.profil.edit'));

        $profil = SchoolProfile::find(1);
        $this->assertSame(-2.416326, (float) $profil->map_latitude);
        $this->assertSame(112.9857232, (float) $profil->map_longitude);

        // Coordinates cleared
        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'map_latitude' => '',
            'map_longitude' => '',
        ])->assertRedirect(route('admin.profil.edit'));

        $this->assertNull(SchoolProfile::find(1)->map_latitude);
        $this->assertNull(SchoolProfile::find(1)->map_longitude);
    }

    public function test_admin_coordinate_validation_bounds(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.profil.update'), [
            'school_name' => 'SMKN 1 Kotabesi',
            'map_latitude' => '99',
            'map_longitude' => '999',
        ])->assertSessionHasErrors(['map_latitude', 'map_longitude']);

        $this->assertNull(SchoolProfile::find(1)->map_latitude);
    }

    public function test_contact_map_uses_coordinates_when_set(): void
    {
        // Ensure profile exists
        $profil = SchoolProfile::updateOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi']
        );
        $profil->update([
            'address' => 'Jl. Pendidikan No. 1, Kotabesi',
            'map_latitude' => '-2.416326',
            'map_longitude' => '112.9857232',
        ]);

        $response = $this->get(route('contact.index'));
        $response->assertOk();
        $response->assertSee('-2.416326', false);
        $response->assertSee('112.985723', false);
        // Alamat tidak dipakai sebagai query peta
        $this->assertStringNotContainsString('Jl.%20Pendidikan', (string) $response->getContent());
        $this->assertStringNotContainsString('Pendidikan%20No', (string) $response->getContent());
    }

    public function test_contact_map_falls_back_to_school_name_not_address_when_no_coordinates(): void
    {
        $profil = SchoolProfile::updateOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi']
        );
        $profil->update([
            'address' => 'Jl. Pendidikan No. 1, Kotabesi',
            'map_latitude' => null,
            'map_longitude' => null,
        ]);

        $response = $this->get(route('contact.index'));
        $response->assertOk();
        // Nama sekolah dipakai sebagai query
        $this->assertStringContainsString('SMKN%201%20Kotabesi', (string) $response->getContent());
        // Alamat bebas tidak dipakai sebagai query peta
        $this->assertStringNotContainsString('Pendidikan%20No', (string) $response->getContent());
    }
}
