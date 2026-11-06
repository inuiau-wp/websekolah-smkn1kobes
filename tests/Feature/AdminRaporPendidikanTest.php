<?php

namespace Tests\Feature;

use App\Models\RaporPendidikan;
use App\Models\User;
use Database\Seeders\RaporPendidikanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8i — Kelola Rapor Pendidikan (dashboard admin):
 * - Hanya admin yang bisa mengakses /admin/rapor-pendidikan.
 * - CRUD lengkap: buat tahun baru, edit, hapus; tahun terpublikasi muncul di navbar publik.
 */
class AdminRaporPendidikanTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'year' => 2026,
            'title' => 'Laporan Transparansi Mutu 2026',
            'intro' => 'Komitmen transparansi publik sesuai UU KIP.',
            'disclaimer' => 'Data agregasi Level 2 sesuai UU PDP.',
            'is_published' => '1',
            'kpi' => [
                ['label' => 'Kemampuan Literasi', 'value' => '78.10', 'suffix' => '', 'kategori' => 'Baik', 'tren' => '+2,57', 'tren_note' => 'vs 2025'],
            ],
            'sections' => [
                'akademik' => [
                    ['title' => 'Kemampuan Literasi Peserta Didik', 'skor' => '78,10', 'kategori' => 'Baik', 'catatan' => 'Capaian meningkat.', 'poin' => "Penguatan literasi digital.\nProgram membaca rutin."],
                ],
                'vokasi' => [
                    ['title' => 'Kepuasan Dunia Kerja (DUDI)', 'skor' => '95,00', 'kategori' => 'Sangat Baik', 'catatan' => 'Mitra industri puas.', 'poin' => ''],
                ],
                'kelola' => [
                    ['title' => 'Iklim Keamanan Sekolah', 'skor' => '80,00', 'kategori' => 'Aman', 'catatan' => 'Lingkungan aman.', 'poin' => ''],
                ],
            ],
            'bar_label_sebelum' => '2025',
            'bar_label_sesudah' => '2026',
            'bar_labels' => 'Literasi, Numerasi',
            'bar_sebelum' => '75.53, 58.57',
            'bar_sesudah' => '78.10, 60.00',
            'radar_labels' => 'Kepuasan DUDI, Link & Match',
            'radar_values' => '95.00, 62.00',
            'indicators' => [
                ['kode' => 'A.1', 'indikator' => 'Kemampuan Literasi Peserta Didik', 'capaian' => '78,10', 'status' => 'Baik', 'narasi' => 'Penguatan literasi digital.'],
            ],
        ], $overrides);
    }

    public function test_non_admin_cannot_access_rapor_management(): void
    {
        // Guest dicek dulu (sebelum actingAs) — redirect ke login
        $this->get(route('admin.rapor-pendidikan.index'))->assertRedirect(route('login'));

        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get(route('admin.rapor-pendidikan.index'))->assertNotFound();
    }

    public function test_admin_can_view_index_with_seeded_year(): void
    {
        RaporPendidikan::create([
            'year' => 2025,
            'title' => 'Rapor 2025',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => true,
            'data' => ['kpi' => [], 'sections' => [], 'charts' => [], 'indicators' => []],
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $page = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.index'));

        $page->assertOk();
        $page->assertSee('Rapor 2025');
        $page->assertSee('Terpublikasi');
    }

    public function test_admin_can_create_and_publish_new_year(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.rapor-pendidikan.store'), $this->validPayload());
        $response->assertRedirect(route('admin.rapor-pendidikan.index'));

        $rapor = RaporPendidikan::where('year', 2026)->firstOrFail();
        $this->assertTrue($rapor->is_published);
        $this->assertSame(78.10, $rapor->data['kpi'][0]['value']);
        $this->assertStringStartsWith('kpi_', $rapor->data['kpi'][0]['key']); // key kosong -> otomatis
        $this->assertSame(['Literasi', 'Numerasi'], $rapor->data['charts']['bar']['labels']);
        $this->assertSame([75.53, 58.57], $rapor->data['charts']['bar']['sebelum']);
        $this->assertSame(['Penguatan literasi digital.', 'Program membaca rutin.'], $rapor->data['sections']['akademik'][0]['poin']);

        // Halaman publik & navbar otomatis menampilkan tahun baru
        $pub = $this->get(route('rapor.show', 2026));
        $pub->assertOk();
        $pub->assertSee('Laporan Transparansi Mutu 2026');
        $pub->assertSee('78,10');
        $this->get(route('home'))->assertSee('Tahun Rapor 2026');
    }

    public function test_create_validates_duplicate_year(): void
    {
        RaporPendidikan::create([
            'year' => 2025,
            'title' => 'Rapor 2025',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => true,
            'data' => ['kpi' => [], 'sections' => [], 'charts' => [], 'indicators' => []],
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = $this->validPayload(['year' => 2025]);

        $this->actingAs($admin)->post(route('admin.rapor-pendidikan.store'), $payload)
            ->assertSessionHasErrors('year');

        $this->assertSame(1, RaporPendidikan::count());
    }

    public function test_admin_can_update_rapor(): void
    {
        $rapor = RaporPendidikan::create([
            'year' => 2025,
            'title' => 'Rapor 2025',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => true,
            'data' => ['kpi' => [], 'sections' => [], 'charts' => [], 'indicators' => []],
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = $this->validPayload(['year' => 2025, 'title' => 'Rapor 2025 Revisi', 'is_published' => null]);

        $this->actingAs($admin)->patch(route('admin.rapor-pendidikan.update', $rapor), $payload)
            ->assertRedirect(route('admin.rapor-pendidikan.index'));

        $rapor->refresh();
        $this->assertSame('Rapor 2025 Revisi', $rapor->title);
        $this->assertFalse($rapor->is_published); // tanpa checkbox = draf

        // Draf tidak tampil di publik
        $this->get(route('rapor.show', 2025))->assertNotFound();
    }

    public function test_admin_can_delete_rapor(): void
    {
        $rapor = RaporPendidikan::create([
            'year' => 2025,
            'title' => 'Rapor 2025',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => true,
            'data' => ['kpi' => [], 'sections' => [], 'charts' => [], 'indicators' => []],
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->delete(route('admin.rapor-pendidikan.destroy', $rapor))
            ->assertRedirect(route('admin.rapor-pendidikan.index'));

        $this->assertModelMissing($rapor);
        $this->get(route('rapor.show', 2025))->assertNotFound();
    }

    public function test_admin_can_open_create_and_edit_forms(): void
    {
        $rapor = RaporPendidikan::create([
            'year' => 2025,
            'title' => 'Rapor 2025',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => true,
            'data' => [
                'kpi' => [['key' => 'literasi', 'label' => 'Literasi', 'value' => 75.53, 'suffix' => '', 'kategori' => 'Baik', 'tren' => null, 'tren_note' => null]],
                'sections' => ['akademik' => [], 'vokasi' => [], 'kelola' => []],
                'charts' => ['bar' => [], 'radar' => []],
                'indicators' => [],
            ],
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.rapor-pendidikan.create'))->assertOk();
        $edit = $this->actingAs($admin)->get(route('admin.rapor-pendidikan.edit', $rapor));
        $edit->assertOk();
        $edit->assertSee('Literasi'); // data lama terisi di form

        // Struktur form mengikuti halaman publik rapor/show.blade.php
        $edit->assertSee('Mutu Akademik');
        $edit->assertSee('Vokasi & Industri');
        $edit->assertSee('Iklim Sekolah & Tata Kelola');
        $edit->assertSee('Kata Pengantar & Komitmen Transparansi');
        $edit->assertSee('Katalog Indikator yang Dipublikasikan');
        $edit->assertSee('Penafian Hukum');
    }

    /**
     * Susun payload persis seperti yang dikirim form admin dari struktur `data` yang ada.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function payloadFromData(array $data): array
    {
        return [
            'kpi' => collect($data['kpi'])->map(fn (array $k): array => [
                'key' => $k['key'] ?? '',
                'label' => $k['label'],
                'value' => (string) $k['value'],
                'suffix' => $k['suffix'] ?? '',
                'kategori' => $k['kategori'],
                'tren' => $k['tren'] ?? '',
                'tren_note' => $k['tren_note'] ?? '',
            ])->all(),
            'sections' => collect($data['sections'])->map(fn (array $items): array => collect($items)->map(fn (array $i): array => [
                'title' => $i['title'],
                'skor' => $i['skor'],
                'kategori' => $i['kategori'],
                'catatan' => $i['catatan'],
                'poin' => implode("\n", $i['poin'] ?? []),
            ])->all())->all(),
            'bar_labels' => implode(', ', $data['charts']['bar']['labels']),
            'bar_label_sebelum' => $data['charts']['bar']['label_sebelum'],
            'bar_label_sesudah' => $data['charts']['bar']['label_sesudah'],
            'bar_sebelum' => implode(', ', $data['charts']['bar']['sebelum']),
            'bar_sesudah' => implode(', ', $data['charts']['bar']['sesudah']),
            'radar_labels' => implode(', ', $data['charts']['radar']['labels']),
            'radar_values' => implode(', ', $data['charts']['radar']['values']),
            'indicators' => collect($data['indicators'])->map(fn (array $i): array => [
                'kode' => $i['kode'],
                'indikator' => $i['indikator'],
                'capaian' => $i['capaian'],
                'status' => $i['status'],
                'narasi' => $i['narasi'],
            ])->all(),
        ];
    }

    public function test_edit_round_trips_seeded_data_without_loss(): void
    {
        $this->seed(RaporPendidikanSeeder::class);
        $rapor = RaporPendidikan::where('year', 2025)->firstOrFail();
        $original = $rapor->data;

        $admin = User::factory()->create(['role' => 'admin']);
        $payload = array_merge([
            'year' => $rapor->year,
            'title' => $rapor->title,
            'intro' => $rapor->intro,
            'disclaimer' => $rapor->disclaimer,
            'is_published' => '1',
        ], $this->payloadFromData($original));

        $this->actingAs($admin)->patch(route('admin.rapor-pendidikan.update', $rapor), $payload)
            ->assertRedirect(route('admin.rapor-pendidikan.index'));

        $rapor->refresh();
        // Key KPI asli (literasi, numerasi, ...) harus bertahan — bukan diganti md5 otomatis
        $this->assertSame(['literasi', 'numerasi', 'dudi', 'keamanan'], array_column($rapor->data['kpi'], 'key'));
        // Simpan ulang via form tidak boleh mengubah apa pun di halaman publik
        $this->assertSame($original, $rapor->data);
    }
}
