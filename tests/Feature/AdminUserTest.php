<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['name' => 'Guru Satu', 'email' => 'guru@contoh.sch.id', 'role' => 'guru']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
        $response->assertSee('Guru Satu');
        $response->assertSee('Guru');
    }

    public function test_admin_can_create_guru_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Guru Baru',
            'email' => 'baru@contoh.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'name' => 'Guru Baru',
            'email' => 'baru@contoh.sch.id',
            'role' => 'guru',
        ]);
    }

    public function test_admin_can_update_user_role_and_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Guru', 'email' => 'g@c.sch.id', 'role' => 'guru']);

        $this->actingAs($admin)->patch(route('admin.users.update', $user), [
            'name' => 'Guru Edit',
            'email' => 'g@c.sch.id',
            'role' => 'admin',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertRedirect(route('admin.users.index'));

        $user->refresh();
        $this->assertSame('Guru Edit', $user->name);
        $this->assertSame('admin', $user->role);
        $this->assertTrue(password_verify('newpass123', $user->password));
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'Hapus', 'email' => 'h@c.sch.id', 'role' => 'siswa']);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_change_own_role_from_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'guru',
        ])->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('error');

        $this->assertSame('admin', $admin->refresh()->role);
    }

    public function test_guru_can_be_deleted_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['name' => 'Guru', 'email' => 'g@c.sch.id', 'role' => 'guru']);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $guru))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $guru->id]);
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($guru)->get(route('admin.users.index'))->assertNotFound();
        $this->actingAs($siswa)->post(route('admin.users.store'), ['name' => 'X', 'email' => 'x@c.sch.id', 'password' => 'pw123456', 'password_confirmation' => 'pw123456', 'role' => 'guru'])->assertNotFound();
    }

    public function test_admin_can_create_guru_account_with_position_nip_and_photo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Dr. Budi Kepala Sekolah',
            'email' => 'kepsek@smkn1kotabesi.sch.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'guru',
            'position' => 'Kepala Sekolah',
            'nip' => '197501012000011001',
            'bio' => 'Sambutan singkat kepala sekolah.',
            'photo' => UploadedFile::fake()->image('kepsek.jpg'),
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'kepsek@smkn1kotabesi.sch.id')->first();
        $this->assertNotNull($user);
        $this->assertSame('Kepala Sekolah', $user->position);
        $this->assertSame('197501012000011001', $user->nip);
        $this->assertNotNull($user->photo);
        Storage::disk('public')->assertExists($user->photo);
    }

    public function test_headmaster_account_is_displayed_on_public_profile_page(): void
    {
        User::factory()->create([
            'name' => 'Drs. H. Ahmad Fauzi, M.Pd.',
            'role' => 'guru',
            'position' => 'Kepala Sekolah',
            'nip' => '197001011995011001',
            'bio' => 'Selamat datang di SMKN 1 Kotabesi',
        ]);

        $response = $this->get(route('profil'));

        $response->assertOk();
        $response->assertSee('Drs. H. Ahmad Fauzi, M.Pd.');
        $response->assertSee('Selamat datang di SMKN 1 Kotabesi');
        $response->assertSee('Kepala Sekolah');
        // NIP disembunyikan dari halaman profil publik demi privasi dan keamanan
        $response->assertDontSee('197001011995011001');
    }

    public function test_guru_can_upload_own_photo_via_profile_update(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->patch(route('profile.update'), [
            'name' => $guru->name,
            'email' => $guru->email,
            'photo' => UploadedFile::fake()->image('my_photo.jpg'),
        ]);

        $response->assertRedirect(route('profile.edit'));

        $guru->refresh();
        $this->assertNotNull($guru->photo);
        Storage::disk('public')->assertExists($guru->photo);
    }

    public function test_guru_profile_page_shows_guru_sidebar(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->get(route('profile.edit'));

        $response->assertOk();
        // Konten dashboard guru tetap tersedia di sisi kiri halaman profil
        $response->assertSee('Dashboard Guru');
        $response->assertSee('Berita Saya');
        $response->assertSee('Foto Profil / Pas Foto');
    }

    public function test_siswa_profile_page_shows_siswa_sidebar(): void
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($siswa)->get(route('profile.edit'));

        $response->assertOk();
        $response->assertSee('Portal Siswa');
        $response->assertSee('Profil Akun');
    }
}
