<?php

namespace App\Http\Controllers;

use App\Models\RaporPendidikan;

class RaporController extends Controller
{
    /**
     * Halaman publik Rapor Pendidikan per tahun rilis.
     */
    public function show(int $year)
    {
        $rapor = RaporPendidikan::published()->where('year', $year)->firstOrFail();

        return view('rapor.show', compact('rapor'));
    }

    /**
     * Halaman publik perbandingan Rapor Pendidikan antar tahun (grafik garis).
     * Dataset dibangun dinamis: gabungan semua KPI/kartu yang pernah muncul,
     * dengan satu titik per tahun (null bila tahun tersebut tidak memilikinya).
     */
    public function perbandingan()
    {
        $rapors = RaporPendidikan::published()->orderBy('year')->get();

        $years = $rapors->pluck('year')->values()->all();

        $kpiSeries = [];
        foreach ($rapors as $rapor) {
            foreach ($rapor->data['kpi'] ?? [] as $kpi) {
                $label = trim((string) ($kpi['label'] ?? ''));
                if ($label === '') {
                    continue;
                }

                $kpiSeries[$label] ??= ['label' => $label, 'suffix' => '', 'values' => array_fill(0, count($years), null)];
                $index = array_search($rapor->year, $years, true);
                $kpiSeries[$label]['values'][$index] = self::numericOrNull($kpi['value'] ?? null);
                if (($kpi['suffix'] ?? '') !== '') {
                    $kpiSeries[$label]['suffix'] = (string) $kpi['suffix'];
                }
            }
        }
        $kpiSeries = array_values($kpiSeries);

        $sectionSeries = [];
        foreach (['akademik', 'vokasi', 'kelola'] as $dimensi) {
            $series = [];
            foreach ($rapors as $rapor) {
                foreach ($rapor->data['sections'][$dimensi] ?? [] as $item) {
                    $title = trim((string) ($item['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }

                    $series[$title] ??= ['title' => $title, 'values' => array_fill(0, count($years), null)];
                    $index = array_search($rapor->year, $years, true);
                    $series[$title]['values'][$index] = self::numericOrNull($item['skor'] ?? null);
                }
            }
            $sectionSeries[$dimensi] = array_values($series);
        }

        return view('rapor.perbandingan', [
            'rapors' => $rapors,
            'years' => $years,
            'kpiSeries' => $kpiSeries,
            'sectionSeries' => $sectionSeries,
        ]);
    }

    /**
     * Konversi nilai skor ("75,53", "93,33%", 51.0) menjadi float, atau null.
     */
    private static function numericOrNull(mixed $value): ?float
    {
        $raw = trim((string) $value);
        $clean = str_replace(',', '.', preg_replace('/[^0-9,.\-]/', '', $raw) ?? '');

        return is_numeric($clean) ? (float) $clean : null;
    }
}
