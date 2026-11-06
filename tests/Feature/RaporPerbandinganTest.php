<?php

namespace Tests\Feature;

use App\Models\RaporPendidikan;
use Database\Seeders\RaporPendidikanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase 8i-perbandingan — Halaman publik perbandingan Rapor Pendidikan antar tahun:
 * - Grafik garis tren dari tahun terkecil ke terbaru (dinamis dari database).
 * - Hanya rapor terpublikasi yang dihitung.
 * - Tahun baru yang diinput otomatis ikut dibandingkan.
 */
class RaporPerbandinganTest extends TestCase
{
    use RefreshDatabase;

    public function test_perbandingan_page_shows_seeded_year_with_charts(): void
    {
        $this->seed(RaporPendidikanSeeder::class);

        $page = $this->get(route('rapor.perbandingan'));
        $page->assertOk();
        $page->assertSee('Perbandingan Rapor Pendidikan Antar Tahun');
        $page->assertSee('Kemampuan Literasi');
        $page->assertSee('canvas id="chart-kpi"', false);
        $page->assertSee('canvas id="chart-akademik"', false);

        // Dataset grafik memuat tahun & nilai KPI seed
        $content = $page->getContent();
        $this->assertStringContainsString('const years = [2025]', $content);
        $this->assertStringContainsString('"Kemampuan Literasi"', $content);
    }

    public function test_chart_years_are_ascending_and_dynamic_for_new_year(): void
    {
        $this->seed(RaporPendidikanSeeder::class);

        // Tahun baru diinput (mis. 2023) — otomatis ikut dibandingkan
        RaporPendidikan::create([
            'year' => 2023,
            'title' => 'Laporan Rapor Pendidikan 2023',
            'intro' => 'Intro 2023.',
            'disclaimer' => 'Disclaimer 2023.',
            'is_published' => true,
            'data' => [
                'kpi' => [
                    ['key' => 'literasi', 'label' => 'Kemampuan Literasi', 'value' => 70.1, 'suffix' => '', 'kategori' => 'Baik', 'tren' => null, 'tren_note' => null],
                    ['key' => 'baru', 'label' => 'Indikator Baru 2023', 'value' => 60.5, 'suffix' => '', 'kategori' => 'Baik', 'tren' => null, 'tren_note' => null],
                ],
                'sections' => ['akademik' => [['title' => 'Kartu 2023', 'skor' => '60,5', 'kategori' => 'Baik', 'catatan' => '', 'poin' => []]]],
            ],
        ]);

        $page = $this->get(route('rapor.perbandingan'));
        $page->assertOk();

        $content = $page->getContent();
        $this->assertStringContainsString('const years = [2023,2025]', $content); // sumbu X dinamis, urut naik
        $this->assertStringContainsString('"Indikator Baru 2023"', $content); // KPI unik dari 2023 ikut
        $this->assertStringContainsString('"Kartu 2023"', $content); // kartu dimensi dari 2023 ikut

        // Seri literasi: titik 2023 = 70.1, titik 2025 = 75.53
        $this->assertStringContainsString('"label":"Kemampuan Literasi","suffix":"","values":[70.1,75.53]', $content);
    }

    public function test_only_published_years_are_compared(): void
    {
        $this->seed(RaporPendidikanSeeder::class);

        RaporPendidikan::create([
            'year' => 2024,
            'title' => 'Draf Rapor 2024',
            'intro' => 'Intro.',
            'disclaimer' => 'Disclaimer.',
            'is_published' => false,
            'data' => ['kpi' => [['key' => 'x', 'label' => 'KPI Draf', 'value' => 10, 'suffix' => '', 'kategori' => 'Baik', 'tren' => null, 'tren_note' => null]]],
        ]);

        $page = $this->get(route('rapor.perbandingan'));
        $page->assertOk();
        $page->assertSee('1 tahun terdata');
        $this->assertStringNotContainsString('KPI Draf', $page->getContent());
        $this->assertStringNotContainsString('Rapor Pendidikan 2024', $page->getContent());
    }

    public function test_empty_state_when_no_published_rapor(): void
    {
        $page = $this->get(route('rapor.perbandingan'));
        $page->assertOk();
        $page->assertSee('Belum ada data Rapor Pendidikan');
        $this->assertStringNotContainsString('chart-kpi', $page->getContent());
    }

    public function test_navbar_dropdown_links_to_perbandingan(): void
    {
        $this->seed(RaporPendidikanSeeder::class);

        $page = $this->get(route('home'));
        $page->assertOk();
        $page->assertSee(route('rapor.perbandingan'), false);
        $page->assertSee('Perbandingan Antar Tahun');
    }
}
