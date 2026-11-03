<?php

namespace Tests\Feature\Auth;

use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang');
        $response->assertSee('Kembali ke Beranda');
    }

    public function test_login_and_layouts_use_school_logo_as_favicon(): void
    {
        SchoolProfile::query()->updateOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi', 'logo_path' => 'profil/logo-sekolah.png']
        );

        $response = $this->get('/login');
        $response->assertOk();
        // Favicon mengikuti logo yang diunggah
        $response->assertSee(asset('storage/profil/logo-sekolah.png'), false);
        // Logo sekolah tampil di halaman login (bukan logo default Laravel)
        $response->assertSee(asset('storage/profil/logo-sekolah.png'), false);
        $response->assertDontSee('Laravel');
    }

    public function test_favicon_falls_back_to_school_logo_svg_when_no_logo_uploaded(): void
    {
        SchoolProfile::query()->updateOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi', 'logo_path' => null]
        );

        $this->get('/login')
            ->assertOk()
            ->assertSee(asset('images/logo-sekolah.svg'), false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('siswa.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
