<?php

namespace Tests\Feature;

use App\Models\RaporPendidikan;
use App\Models\User;
use Database\Seeders\RaporPendidikanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Fase 8i-lanjutan — Template & Impor Rapor Pendidikan:
 * - Unduh template input (.md / .csv), termasuk ekspor data tahun tertentu.
 * - Unggah template -> hasil parse mengisi form Tambah/Edit untuk direviu.
 * - Round-trip penuh: ekspor -> impor -> simpan -> data identik.
 */
class RaporTemplateImportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admin_cannot_access_template_and_import(): void
    {
        $this->get(route('admin.rapor-pendidikan.template', ['format' => 'md']))->assertRedirect(route('login'));

        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get(route('admin.rapor-pendidikan.template', ['format' => 'md']))->assertNotFound();
        $this->actingAs($guru)->post(route('admin.rapor-pendidikan.import'), [])->assertNotFound();
    }

    public function test_admin_can_download_md_and_csv_templates(): void
    {
        $admin = $this->admin();

        $md = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'md']));
        $md->assertOk();
        $md->assertHeader('Content-Disposition', 'attachment; filename="template-rapor-pendidikan.md"');
        $this->assertStringContainsString('## KPI', $md->getContent());
        $this->assertStringContainsString('## Mutu Akademik', $md->getContent());
        $this->assertStringContainsString('## Vokasi & Industri', $md->getContent());
        $this->assertStringContainsString('## Iklim Sekolah & Tata Kelola', $md->getContent());
        $this->assertStringContainsString('## Grafik Batang', $md->getContent());
        $this->assertStringContainsString('## Grafik Radar', $md->getContent());
        $this->assertStringContainsString('## Katalog Indikator', $md->getContent());

        $csv = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'csv']));
        $csv->assertOk();
        $csv->assertHeader('Content-Disposition', 'attachment; filename="template-rapor-pendidikan.csv"');
        $this->assertStringContainsString('bagian,tahun,judul,status_publikasi', $csv->getContent());
    }

    public function test_template_can_be_exported_from_existing_year(): void
    {
        $this->seed(RaporPendidikanSeeder::class);
        $admin = $this->admin();

        $md = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'md', 'year' => 2025]));
        $md->assertOk();
        $md->assertHeader('Content-Disposition', 'attachment; filename="template-rapor-pendidikan-2025.md"');
        $this->assertStringContainsString('- tahun: 2025', $md->getContent());
        $this->assertStringContainsString('| literasi | Kemampuan Literasi | 75.53', $md->getContent());
    }

    public function test_import_md_prefills_create_form(): void
    {
        $admin = $this->admin();
        $md = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'md']))->getContent();

        $upload = UploadedFile::fake()->createWithContent('template.md', $md);
        $this->post(route('admin.rapor-pendidikan.import'), ['file' => $upload, 'context' => 'create'])
            ->assertRedirect(route('admin.rapor-pendidikan.create'));

        $page = $this->get(route('admin.rapor-pendidikan.create'));
        $page->assertOk();
        $page->assertSee('Data dari template berhasil dimuat');
        $this->assertStringContainsString('"value":"75.53"', $page->getContent()); // nilai KPI contoh terisi di state Alpine x-data
    }

    public function test_md_export_import_save_round_trips_seeded_data(): void
    {
        $this->seed(RaporPendidikanSeeder::class);
        $rapor = RaporPendidikan::where('year', 2025)->firstOrFail();
        $original = $rapor->data;
        $admin = $this->admin();

        $md = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'md', 'year' => 2025]))->getContent();

        $upload = UploadedFile::fake()->createWithContent('rapor-2025.md', $md);
        $this->post(route('admin.rapor-pendidikan.import'), ['file' => $upload, 'context' => 'edit', 'rapor_id' => $rapor->id])
            ->assertRedirect(route('admin.rapor-pendidikan.edit', $rapor));

        $import = session('rapor_import');
        $this->assertIsArray($import);

        // Simpan lewat form: payload = hasil impor (bentuknya sama dengan isian form)
        $payload = array_merge($import, ['is_published' => '1']);
        $this->patch(route('admin.rapor-pendidikan.update', $rapor), $payload)
            ->assertRedirect(route('admin.rapor-pendidikan.index'));

        $rapor->refresh();
        $this->assertSame($original, $rapor->data);
        $this->assertNull(session('rapor_import')); // dibersihkan setelah simpan
    }

    public function test_csv_export_import_save_round_trips_seeded_data(): void
    {
        $this->seed(RaporPendidikanSeeder::class);
        $rapor = RaporPendidikan::where('year', 2025)->firstOrFail();
        $original = $rapor->data;
        $admin = $this->admin();

        $csv = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.template', ['format' => 'csv', 'year' => 2025]))->getContent();

        $upload = UploadedFile::fake()->createWithContent('rapor-2025.csv', $csv);
        $this->post(route('admin.rapor-pendidikan.import'), ['file' => $upload, 'context' => 'edit', 'rapor_id' => $rapor->id])
            ->assertRedirect(route('admin.rapor-pendidikan.edit', $rapor));

        $import = session('rapor_import');
        $this->assertIsArray($import);

        $payload = array_merge($import, ['is_published' => '1']);
        $this->patch(route('admin.rapor-pendidikan.update', $rapor), $payload)
            ->assertRedirect(route('admin.rapor-pendidikan.index'));

        $rapor->refresh();
        $this->assertSame($original, $rapor->data);
    }

    public function test_import_rejects_unreadable_file(): void
    {
        $admin = $this->admin();

        $upload = UploadedFile::fake()->createWithContent('acak.txt', 'ini bukan template yang valid');
        $this->actingAs($admin)->post(route('admin.rapor-pendidikan.import'), ['file' => $upload])
            ->assertSessionHasErrors('file');

        $this->assertNull(session('rapor_import'));
    }
}
