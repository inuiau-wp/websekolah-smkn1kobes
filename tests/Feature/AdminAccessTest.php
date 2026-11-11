<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\News;
use App\Models\RaporPendidikan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Ringkasan');
    }

    public function test_admin_dashboard_shows_full_content_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['role' => 'guru', 'name' => 'Pak Budi']);

        $berita = News::factory()->create([
            'title' => 'Juara LKS Tingkat Provinsi',
            'category' => 'news',
            'status' => 'published',
            'author_id' => $author->id,
        ]);
        News::factory()->create(['title' => 'Modul Jaringan Dasar', 'category' => 'materi', 'status' => 'draft']);
        ContactMessage::factory()->create([
            'name' => 'Ibu Sari',
            'subject' => 'Pendaftaran PPDB 2026',
            'is_read' => false,
        ]);
        RaporPendidikan::create([
            'year' => 2026,
            'is_published' => false,
            'data' => ['kpi' => [], 'sections' => [], 'charts' => [], 'indicators' => []],
        ]);
        User::factory()->count(3)->create(['role' => 'guru']);
        User::factory()->count(2)->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            // Perlu perhatian
            ->assertSee('1 pesan belum dibaca')
            ->assertSee('1 berita masih draf')
            ->assertSee('1 tahun rapor belum dipublikasikan')
            // Berita terbaru + chip status
            ->assertSee('Juara LKS Tingkat Provinsi')
            ->assertSee('Pak Budi')
            ->assertSee('Modul Jaringan Dasar')
            // Pesan terbaru
            ->assertSee('Pendaftaran PPDB 2026')
            ->assertSee('Ibu Sari')
            // Kartu akun & rapor
            ->assertSee('Akun terdaftar')
            ->assertSee('Rapor Pendidikan terpublikasi')
            // Grafik kategori: label + data JSON
            ->assertSee('chart-kategori')
            ->assertSee('Modul Materi');
    }

    public function test_non_admin_gets_404_on_admin_dashboard(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get(route('admin.dashboard'))->assertNotFound();
        $this->actingAs($siswa)->get(route('admin.dashboard'))->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_public_registration_is_closed(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Penyusup',
            'email' => 'infiltrator@example.com',
            'password' => 'password-example-123',
            'password_confirmation' => 'password-example-123',
        ])->assertNotFound();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password-example-123',
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password-example-123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
