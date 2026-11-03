<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_access_siswa_dashboard(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa', 'name' => 'Ahmad Siswa']);

        $response = $this->actingAs($siswa)->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('Ahmad Siswa');
        $response->assertSee('Portal Siswa');
    }

    public function test_non_siswa_gets_404_on_siswa_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->get(route('siswa.dashboard'))->assertNotFound();
        $this->actingAs($guru)->get(route('siswa.dashboard'))->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('siswa.dashboard'))->assertRedirect(route('login'));
    }

    public function test_siswa_login_redirects_to_siswa_dashboard(): void
    {
        $siswa = User::factory()->create([
            'email' => 'siswa@example.test',
            'password' => 'password123',
            'role' => 'siswa',
        ]);

        $response = $this->post('/login', [
            'email' => $siswa->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('siswa.dashboard', absolute: false));
    }

    public function test_siswa_can_see_announcements_on_dashboard(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        News::factory()->create([
            'title' => 'Pengumuman Ujian Semester',
            'category' => 'announcement',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($siswa)->get(route('siswa.dashboard'));

        $response->assertOk();
        $response->assertSee('Pengumuman Ujian Semester');
    }

    public function test_generic_dashboard_route_redirects_to_appropriate_role_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($guru)->get(route('dashboard'))
            ->assertRedirect(route('guru.dashboard'));

        $this->actingAs($siswa)->get(route('dashboard'))
            ->assertRedirect(route('siswa.dashboard'));
    }
}
