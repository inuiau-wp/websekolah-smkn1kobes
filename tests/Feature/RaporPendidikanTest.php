<?php

namespace Tests\Feature;

use App\Models\RaporPendidikan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8i — Halaman publik Rapor Pendidikan:
 * - /rapor-pendidikan/{tahun} hanya untuk data is_published.
 * - Navbar menampilkan dropdown "Rapor Pendidikan" berisi daftar tahun.
 */
class RaporPendidikanTest extends TestCase
{
    use RefreshDatabase;

    private function makeRapor(int $year = 2025, bool $published = true): RaporPendidikan
    {
        return RaporPendidikan::create([
            'year' => $year,
            'title' => "Laporan Transparansi Mutu & Relevansi Vokasi {$year}",
            'intro' => 'Komitmen transparansi publik sesuai UU KIP.',
            'disclaimer' => 'Data agregasi Level 2 sesuai UU PDP.',
            'is_published' => $published,
            'data' => [
                'kpi' => [
                    ['key' => 'literasi', 'label' => 'Kemampuan Literasi', 'value' => 75.53, 'suffix' => '', 'kategori' => 'Baik', 'tren' => '+2,30', 'tren_note' => 'vs 2024'],
                ],
                'sections' => [
                    'akademik' => [
                        ['title' => 'Kemampuan Literasi Peserta Didik', 'skor' => '75,53', 'kategori' => 'Baik', 'catatan' => 'Capaian literasi meningkat.', 'poin' => ['Penguatan literasi digital.']],
                    ],
                    'vokasi' => [],
                    'kelola' => [],
                ],
                'charts' => [
                    'bar' => ['labels' => ['Literasi'], 'label_sebelum' => '2024', 'label_sesudah' => '2025', 'sebelum' => [73.23], 'sesudah' => [75.53]],
                    'radar' => ['labels' => ['Kepuasan DUDI'], 'values' => [93.33]],
                ],
                'indicators' => [
                    ['kode' => 'A.1', 'indikator' => 'Kemampuan Literasi Peserta Didik', 'capaian' => '75,53', 'status' => 'Baik', 'narasi' => 'Penguatan literasi digital.'],
                ],
            ],
        ]);
    }

    public function test_published_rapor_page_renders_content(): void
    {
        $rapor = $this->makeRapor();

        $page = $this->get(route('rapor.show', $rapor->year));
        $page->assertOk();
        $page->assertSee('Rapor Pendidikan 2025');
        $page->assertSee('Transparansi Mutu');
        $page->assertSee('75,53');
        $page->assertSee('Kemampuan Literasi Peserta Didik');
        $page->assertSee('Data agregasi Level 2 sesuai UU PDP.');
        $page->assertSee('Penafian Hukum');
        // Grafik Chart.js dirender dalam canvas
        $page->assertSee('chart-bar');
        $page->assertSee('chart-radar');
    }

    public function test_navigation_dropdown_lists_published_years(): void
    {
        $this->makeRapor(year: 2025);

        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee('Rapor Pendidikan');
        $home->assertSee('Tahun Rapor 2025');
        $home->assertSee(route('rapor.show', 2025));
    }

    public function test_unknown_year_is_404(): void
    {
        $this->get(route('rapor.show', 1999))->assertNotFound();

        // Tahun non-angka tidak tertangkap route (whereNumber)
        $this->get('/rapor-pendidikan/abc')->assertNotFound();
    }

    public function test_unpublished_rapor_is_404(): void
    {
        $this->makeRapor(year: 2024, published: false);

        $this->get(route('rapor.show', 2024))->assertNotFound();
    }
}
