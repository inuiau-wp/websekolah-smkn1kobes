<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuruAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_access_guru_dashboard(): void
    {
        $guru = User::factory()->create(['role' => 'guru', 'name' => 'Budi Guru']);

        $response = $this->actingAs($guru)->get(route('guru.dashboard'));

        $response->assertOk();
        $response->assertSee('Budi Guru');
        $response->assertSee('Dashboard Guru');
    }

    public function test_non_guru_gets_404_on_guru_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->actingAs($admin)->get(route('guru.dashboard'))->assertNotFound();
        $this->actingAs($siswa)->get(route('guru.dashboard'))->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('guru.dashboard'))->assertRedirect(route('login'));
        $this->get(route('guru.news.index'))->assertRedirect(route('login'));
    }

    public function test_guru_login_redirects_to_guru_dashboard(): void
    {
        $guru = User::factory()->create([
            'email' => 'guru@example.test',
            'password' => 'password123',
            'role' => 'guru',
        ]);

        $response = $this->post('/login', [
            'email' => $guru->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('guru.dashboard', absolute: false));
    }

    public function test_guru_can_see_only_own_news(): void
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);

        News::factory()->create([
            'author_id' => $guru1->id,
            'title' => 'Berita Milik Guru 1',
        ]);

        News::factory()->create([
            'author_id' => $guru2->id,
            'title' => 'Berita Milik Guru 2',
        ]);

        $response = $this->actingAs($guru1)->get(route('guru.news.index'));

        $response->assertOk();
        $response->assertSee('Berita Milik Guru 1');
        $response->assertDontSee('Berita Milik Guru 2');
    }

    public function test_guru_can_create_news(): void
    {
        Storage::fake('public');
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)->post(route('guru.news.store'), [
            'title' => 'Kegiatan Baru Guru',
            'excerpt' => 'Rangkuman cuplikan',
            'content' => 'Isi lengkap kegiatan',
            'category' => 'news',
            'status' => 'published',
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $response->assertRedirect(route('guru.news.index'));

        $this->assertDatabaseHas('news', [
            'title' => 'Kegiatan Baru Guru',
            'author_id' => $guru->id,
            'status' => 'published',
        ]);
    }

    public function test_guru_can_edit_and_update_own_news(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $news = News::factory()->create([
            'author_id' => $guru->id,
            'title' => 'Judul Sebelum Edit',
        ]);

        $editResponse = $this->actingAs($guru)->get(route('guru.news.edit', $news));
        $editResponse->assertOk();
        $editResponse->assertSee('Judul Sebelum Edit');

        $updateResponse = $this->actingAs($guru)->patch(route('guru.news.update', $news), [
            'title' => 'Judul Setelah Edit',
            'category' => 'announcement',
            'status' => 'draft',
        ]);

        $updateResponse->assertRedirect(route('guru.news.index'));

        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Judul Setelah Edit',
            'category' => 'announcement',
            'status' => 'draft',
        ]);
    }

    public function test_guru_cannot_edit_or_delete_other_user_news(): void
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);

        $newsGuru2 = News::factory()->create([
            'author_id' => $guru2->id,
            'title' => 'Berita Guru 2',
        ]);

        // Guru 1 coba edit berita Guru 2 -> 404
        $this->actingAs($guru1)->get(route('guru.news.edit', $newsGuru2))->assertNotFound();

        // Guru 1 coba update berita Guru 2 -> 404
        $this->actingAs($guru1)->patch(route('guru.news.update', $newsGuru2), [
            'title' => 'Coba Retas',
            'category' => 'news',
            'status' => 'published',
        ])->assertNotFound();

        // Guru 1 coba hapus berita Guru 2 -> 404
        $this->actingAs($guru1)->delete(route('guru.news.destroy', $newsGuru2))->assertNotFound();

        $this->assertDatabaseHas('news', [
            'id' => $newsGuru2->id,
            'title' => 'Berita Guru 2',
        ]);
    }

    public function test_guru_can_delete_own_news(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $news = News::factory()->create([
            'author_id' => $guru->id,
            'title' => 'Berita Mau Dihapus',
        ]);

        $response = $this->actingAs($guru)->delete(route('guru.news.destroy', $news));
        $response->assertRedirect(route('guru.news.index'));

        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }
}
