<?php

namespace App\Services;

use App\Models\RaporPendidikan;
use InvalidArgumentException;

/**
 * Generator & parser template input Rapor Pendidikan (.md / .csv).
 *
 * Alur kerja: admin mengunduh template (kosong + contoh, atau terisi dari
 * data tahun tertentu), mengeditnya di luar aplikasi, lalu mengunggahnya
 * kembali — hasil parse mengisi form Tambah/Edit untuk direviu sebelum disimpan.
 */
class RaporTemplate
{
    /** @var list<string> */
    private const GRUP = ['akademik', 'vokasi', 'kelola'];

    /** @var array<string, string> */
    private const GRUP_HEADING = [
        'akademik' => 'Mutu Akademik',
        'vokasi' => 'Vokasi & Industri',
        'kelola' => 'Iklim Sekolah & Tata Kelola',
    ];

    /** @var list<string> */
    private const KOLOM_TABEL = ['key', 'label', 'nilai', 'satuan', 'kategori', 'tren', 'catatan_tren', 'judul', 'skor', 'catatan', 'poin', 'kode', 'indikator', 'capaian', 'status', 'narasi'];

    /**
     * Sumber data template: dari record yang ada, atau contoh generik.
     *
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private static function source(?RaporPendidikan $rapor): array
    {
        if ($rapor !== null) {
            return [
                [
                    'year' => (string) $rapor->year,
                    'title' => (string) $rapor->title,
                    'status' => $rapor->is_published ? 'terpublikasi' : 'draf',
                    'intro' => (string) $rapor->intro,
                    'disclaimer' => (string) $rapor->disclaimer,
                ],
                $rapor->data ?? [],
            ];
        }

        return [
            [
                'year' => (string) ((int) date('Y')),
                'title' => 'Laporan Transparansi Mutu & Relevansi Vokasi SMKN 1 Kota Besi (Capaian Rapor Pendidikan '.date('Y').')',
                'status' => 'draf',
                'intro' => 'Tuliskan kata pengantar & komitmen transparansi di sini (UU KIP No. 14/2008 dan UU PDP No. 27/2022).',
                'disclaimer' => 'Tuliskan penafian hukum di sini (agregasi Level 2, perlindungan data pribadi).',
            ],
            self::contoh(),
        ];
    }

    /**
     * Contoh data minimal untuk template kosong.
     *
     * @return array<string, mixed>
     */
    private static function contoh(): array
    {
        return [
            'kpi' => [
                ['key' => 'literasi', 'label' => 'Kemampuan Literasi', 'value' => 75.53, 'suffix' => '', 'kategori' => 'Baik', 'tren' => '+2,30', 'tren_note' => 'vs 2024 (73,23)'],
            ],
            'sections' => [
                'akademik' => [
                    ['title' => 'Kemampuan Literasi Peserta Didik', 'skor' => '75,53', 'kategori' => 'Baik', 'catatan' => 'Tuliskan catatan capaian di sini.', 'poin' => ['Tren meningkat +2,30 dibanding tahun 2024.']],
                ],
                'vokasi' => [
                    ['title' => 'Kepuasan Dunia Kerja (Mitra DUDI)', 'skor' => '93,33%', 'kategori' => 'Sangat Baik', 'catatan' => 'Tuliskan catatan capaian di sini.', 'poin' => []],
                ],
                'kelola' => [
                    ['title' => 'Iklim Keamanan Sekolah', 'skor' => '74,41', 'kategori' => 'Aman', 'catatan' => 'Tuliskan catatan capaian di sini.', 'poin' => []],
                ],
            ],
            'charts' => [
                'bar' => [
                    'labels' => ['Literasi', 'Numerasi'],
                    'label_sebelum' => '2024',
                    'label_sesudah' => '2025',
                    'sebelum' => [73.23, 58.14],
                    'sesudah' => [75.53, 58.57],
                ],
                'radar' => [
                    'labels' => ['Kepuasan DUDI', 'Link & Match'],
                    'values' => [93.33, 59.96],
                ],
            ],
            'indicators' => [
                ['kode' => 'A.1', 'indikator' => 'Kemampuan Literasi Peserta Didik', 'capaian' => '75,53', 'status' => 'Baik', 'narasi' => 'Tuliskan narasi tindak lanjut RKT.'],
            ],
        ];
    }

    /**
     * Template Markdown (contoh generik bila $rapor null, data tahun bila diisi).
     */
    public static function md(?RaporPendidikan $rapor = null): string
    {
        [$id, $data] = self::source($rapor);

        $lines = [
            '# Template Rapor Pendidikan — SMKN 1 Kota Besi',
            '',
            '<!-- Petunjuk: ubah nilai setelah tanda ":" dan isi sel tabel, lalu unggah kembali di form Tambah/Edit Rapor Pendidikan. -->',
            '<!-- Jangan mengubah judul bagian (## ...) dan header tabel. Poin rincian kartu dipisah tanda ";" dalam satu sel. -->',
            '<!-- Gunakan titik (bukan koma) untuk desimal pada field "nilai" angka (mis. 75.53). -->',
            '',
            '## Identitas',
            '- tahun: '.$id['year'],
            '- judul: '.self::mdEscape($id['title']),
            '- status: '.$id['status'],
            '- intro: '.self::mdEscape($id['intro']),
            '- disclaimer: '.self::mdEscape($id['disclaimer']),
            '',
            '## KPI',
            '| key | label | nilai | satuan | kategori | tren | catatan_tren |',
            '| --- | --- | --- | --- | --- | --- | --- |',
        ];

        foreach ($data['kpi'] ?? [] as $k) {
            $lines[] = '| '.implode(' | ', [
                self::mdEscape((string) ($k['key'] ?? '')),
                self::mdEscape((string) $k['label']),
                (string) $k['value'],
                self::mdEscape((string) ($k['suffix'] ?? '')),
                self::mdEscape((string) $k['kategori']),
                self::mdEscape((string) ($k['tren'] ?? '')),
                self::mdEscape((string) ($k['tren_note'] ?? '')),
            ]).' |';
        }

        foreach (self::GRUP as $grup) {
            $lines[] = '';
            $lines[] = '## '.self::GRUP_HEADING[$grup];
            $lines[] = '| judul | skor | kategori | catatan | poin |';
            $lines[] = '| --- | --- | --- | --- | --- |';
            foreach ($data['sections'][$grup] ?? [] as $item) {
                $lines[] = '| '.implode(' | ', [
                    self::mdEscape((string) $item['title']),
                    self::mdEscape((string) $item['skor']),
                    self::mdEscape((string) $item['kategori']),
                    self::mdEscape((string) $item['catatan']),
                    self::mdEscape(implode('; ', $item['poin'] ?? [])),
                ]).' |';
            }
        }

        $bar = $data['charts']['bar'] ?? [];
        $radar = $data['charts']['radar'] ?? [];

        $lines[] = '';
        $lines[] = '## Grafik Batang';
        $lines[] = '- label_sebelum: '.self::mdEscape((string) ($bar['label_sebelum'] ?? ''));
        $lines[] = '- label_sesudah: '.self::mdEscape((string) ($bar['label_sesudah'] ?? ''));
        $lines[] = '- label: '.implode(', ', $bar['labels'] ?? []);
        $lines[] = '- nilai_sebelum: '.implode(', ', $bar['sebelum'] ?? []);
        $lines[] = '- nilai_sesudah: '.implode(', ', $bar['sesudah'] ?? []);

        $lines[] = '';
        $lines[] = '## Grafik Radar';
        $lines[] = '- label: '.implode(', ', $radar['labels'] ?? []);
        $lines[] = '- nilai: '.implode(', ', $radar['values'] ?? []);

        $lines[] = '';
        $lines[] = '## Katalog Indikator';
        $lines[] = '| kode | indikator | capaian | status | narasi |';
        $lines[] = '| --- | --- | --- | --- | --- |';
        foreach ($data['indicators'] ?? [] as $ind) {
            $lines[] = '| '.implode(' | ', [
                self::mdEscape((string) $ind['kode']),
                self::mdEscape((string) $ind['indikator']),
                self::mdEscape((string) $ind['capaian']),
                self::mdEscape((string) $ind['status']),
                self::mdEscape((string) $ind['narasi']),
            ]).' |';
        }

        return implode("\n", $lines)."\n";
    }

    private static function mdEscape(string $value): string
    {
        return str_replace('|', '/', $value);
    }

    /**
     * Template CSV (flat, satu tipe baris per kolom "bagian").
     */
    public static function csv(?RaporPendidikan $rapor = null): string
    {
        [$id, $data] = self::source($rapor);

        $header = ['bagian', 'tahun', 'judul', 'status_publikasi', 'intro', 'disclaimer', 'key', 'label', 'nilai', 'satuan', 'kategori', 'tren', 'catatan_tren', 'grup', 'judul_kartu', 'skor', 'catatan_kartu', 'poin', 'kode', 'indikator', 'capaian', 'status', 'narasi', 'grafik_label', 'grafik_nilai_1', 'grafik_nilai_2', 'grafik_tahun_1', 'grafik_tahun_2'];

        $rows = [];

        $row = array_fill_keys($header, '');
        $row['bagian'] = 'identitas';
        $row['tahun'] = (string) $id['year'];
        $row['judul'] = (string) $id['title'];
        $row['status_publikasi'] = (string) $id['status'];
        $row['intro'] = (string) $id['intro'];
        $row['disclaimer'] = (string) $id['disclaimer'];
        $rows[] = $row;

        foreach ($data['kpi'] ?? [] as $k) {
            $row = array_fill_keys($header, '');
            $row['bagian'] = 'kpi';
            $row['key'] = (string) ($k['key'] ?? '');
            $row['label'] = (string) $k['label'];
            $row['nilai'] = (string) $k['value'];
            $row['satuan'] = (string) ($k['suffix'] ?? '');
            $row['kategori'] = (string) $k['kategori'];
            $row['tren'] = (string) ($k['tren'] ?? '');
            $row['catatan_tren'] = (string) ($k['tren_note'] ?? '');
            $rows[] = $row;
        }

        foreach (self::GRUP as $grup) {
            foreach ($data['sections'][$grup] ?? [] as $item) {
                $row = array_fill_keys($header, '');
                $row['bagian'] = 'kartu';
                $row['grup'] = $grup;
                $row['judul_kartu'] = (string) $item['title'];
                $row['skor'] = (string) $item['skor'];
                $row['kategori'] = (string) $item['kategori'];
                $row['catatan_kartu'] = (string) $item['catatan'];
                $row['poin'] = implode('; ', $item['poin'] ?? []);
                $rows[] = $row;
            }
        }

        foreach ($data['indicators'] ?? [] as $ind) {
            $row = array_fill_keys($header, '');
            $row['bagian'] = 'indikator';
            $row['kode'] = (string) $ind['kode'];
            $row['indikator'] = (string) $ind['indikator'];
            $row['capaian'] = (string) $ind['capaian'];
            $row['status'] = (string) $ind['status'];
            $row['narasi'] = (string) $ind['narasi'];
            $rows[] = $row;
        }

        $bar = $data['charts']['bar'] ?? [];
        $row = array_fill_keys($header, '');
        $row['bagian'] = 'grafik_batang';
        $row['grafik_tahun_1'] = (string) ($bar['label_sebelum'] ?? '');
        $row['grafik_tahun_2'] = (string) ($bar['label_sesudah'] ?? '');
        $row['grafik_label'] = implode(', ', $bar['labels'] ?? []);
        $row['grafik_nilai_1'] = implode(', ', $bar['sebelum'] ?? []);
        $row['grafik_nilai_2'] = implode(', ', $bar['sesudah'] ?? []);
        $rows[] = $row;

        $radar = $data['charts']['radar'] ?? [];
        $row = array_fill_keys($header, '');
        $row['bagian'] = 'grafik_radar';
        $row['grafik_label'] = implode(', ', $radar['labels'] ?? []);
        $row['grafik_nilai_1'] = implode(', ', $radar['values'] ?? []);
        $rows[] = $row;

        $lines = [implode(',', array_map(self::csvField(...), $header))];
        foreach ($rows as $r) {
            $lines[] = implode(',', array_map(self::csvField(...), array_values($r)));
        }

        return "\xEF\xBB\xBF".implode("\r\n", $lines)."\r\n";
    }

    private static function csvField(mixed $value): string
    {
        $v = (string) $value;

        return strpbrk($v, ",\"\r\n") !== false ? '"'.str_replace('"', '""', $v).'"' : $v;
    }

    /**
     * Parse file template menjadi struktur siap-pakai untuk mengisi form.
     * Hanya bagian yang ditemukan yang diisi — bagian kosong tidak menimpa data lama.
     *
     * @return array<string, mixed>
     */
    public static function parse(string $content, string $extension): array
    {
        $content = (string) preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $parsed = strtolower($extension) === 'csv' ? self::parseCsv($content) : self::parseMd($content);

        if ($parsed === []) {
            throw new InvalidArgumentException('Tidak ada data yang bisa dibaca dari file. Pastikan format mengikuti template yang diunduh.');
        }

        return $parsed;
    }

    /**
     * @return array<string, mixed>
     */
    private static function parseMd(string $content): array
    {
        $out = [];
        $section = null;
        $kv = [];
        $header = [];

        foreach (preg_split('/\r\n|\r|\n/', $content) ?: [] as $line) {
            $trim = trim($line);

            if ($trim === '' || str_starts_with($trim, '<!--') || (str_starts_with($trim, '#') && ! str_starts_with($trim, '##'))) {
                continue; // baris kosong, komentar, atau judul dokumen
            }

            if (str_starts_with($trim, '## ')) {
                self::flushKv($out, $section, $kv);
                $section = self::sectionOf(mb_strtolower($trim));
                $kv = [];
                $header = [];

                continue;
            }

            if ($section === null) {
                continue;
            }

            if (in_array($section, ['identitas', 'grafik_batang', 'grafik_radar'], true)) {
                if (str_starts_with($trim, '- ')) {
                    if (preg_match('/^-\s*([a-z_]+)\s*:\s*(.*)$/u', $trim, $m) === 1) {
                        $kv[$m[1]] = $m[2];
                    }
                } elseif ($kv !== [] && $trim !== '') {
                    $last = array_key_last($kv);
                    $kv[$last] .= "\n".$trim; // baris lanjutan nilai multi-baris
                }

                continue;
            }

            // Bagian tabel (kpi / kartu / indikator)
            if (! str_starts_with($trim, '|')) {
                continue;
            }

            $cells = self::tableCells($trim);
            if ($cells === null) {
                continue; // baris pemisah | --- |
            }

            if ($header === []) {
                $header = array_map(fn (string $cell): string => self::fieldName($cell), $cells);

                continue;
            }

            $row = [];
            foreach ($header as $i => $name) {
                if ($name !== '') {
                    $row[$name] = (string) ($cells[$i] ?? '');
                }
            }
            self::mergeTableRow($out, $section, $row);
        }

        self::flushKv($out, $section, $kv);

        return $out;
    }

    private static function flushKv(array &$out, ?string $section, array $kv): void
    {
        if ($kv === []) {
            return;
        }

        if ($section === 'identitas') {
            if (isset($kv['tahun'])) {
                $out['year'] = (int) preg_replace('/\D/', '', $kv['tahun']);
            }
            if (isset($kv['judul'])) {
                $out['title'] = trim($kv['judul']);
            }
            if (isset($kv['status'])) {
                $out['is_published'] = str_contains(mb_strtolower($kv['status']), 'terpublikasi');
            }
            if (isset($kv['intro'])) {
                $out['intro'] = trim($kv['intro']);
            }
            if (isset($kv['disclaimer'])) {
                $out['disclaimer'] = trim($kv['disclaimer']);
            }
        } elseif ($section === 'grafik_batang') {
            foreach (['label_sebelum' => 'bar_label_sebelum', 'label_sesudah' => 'bar_label_sesudah', 'label' => 'bar_labels', 'nilai_sebelum' => 'bar_sebelum', 'nilai_sesudah' => 'bar_sesudah'] as $dari => $ke) {
                if (isset($kv[$dari])) {
                    $out[$ke] = trim($kv[$dari]);
                }
            }
        } elseif ($section === 'grafik_radar') {
            if (isset($kv['label'])) {
                $out['radar_labels'] = trim($kv['label']);
            }
            if (isset($kv['nilai'])) {
                $out['radar_values'] = trim($kv['nilai']);
            }
        }
    }

    private static function sectionOf(string $heading): ?string
    {
        return match (true) {
            str_contains($heading, 'identitas') => 'identitas',
            str_contains($heading, 'kpi') => 'kpi',
            str_contains($heading, 'akademik') => 'akademik',
            str_contains($heading, 'vokasi') => 'vokasi',
            str_contains($heading, 'kelola') || str_contains($heading, 'iklim') => 'kelola',
            str_contains($heading, 'batang') => 'grafik_batang',
            str_contains($heading, 'radar') => 'grafik_radar',
            str_contains($heading, 'indikator') => 'indikator',
            default => null,
        };
    }

    /**
     * @return list<string>|null
     */
    private static function tableCells(string $line): ?array
    {
        $trimmed = trim($line);
        if (! str_starts_with($trimmed, '|')) {
            return null;
        }

        $cells = array_map(static fn (string $cell): string => trim($cell), explode('|', trim($trimmed, '|')));

        // Baris pemisah tabel: | --- | --- |
        if (str_replace(['-', ':', ' '], '', (string) ($cells[0] ?? '')) === '') {
            return null;
        }

        return $cells;
    }

    private static function fieldName(string $cell): string
    {
        $c = str_replace([' ', '-'], '_', mb_strtolower(trim($cell)));

        return in_array($c, self::KOLOM_TABEL, true) ? $c : '';
    }

    /**
     * @param  array<string, string>  $row
     * @param  array<string, mixed>  $out
     */
    private static function mergeTableRow(array &$out, string $section, array $row): void
    {
        if ($section === 'kpi') {
            if (trim($row['label'] ?? '') === '' && trim($row['nilai'] ?? '') === '') {
                return;
            }
            $out['kpi'][] = [
                'key' => trim($row['key'] ?? ''),
                'label' => trim($row['label'] ?? ''),
                'value' => self::numeric($row['nilai'] ?? ''),
                'suffix' => trim($row['satuan'] ?? ''),
                'kategori' => self::kategori($row['kategori'] ?? null),
                'tren' => self::nulled($row['tren'] ?? null),
                'tren_note' => self::nulled($row['catatan_tren'] ?? null),
            ];

            return;
        }

        if (in_array($section, self::GRUP, true)) {
            if (trim($row['judul'] ?? '') === '' && trim($row['skor'] ?? '') === '') {
                return;
            }
            $out['sections'][$section][] = [
                'title' => trim($row['judul'] ?? ''),
                'skor' => trim($row['skor'] ?? ''),
                'kategori' => self::kategori($row['kategori'] ?? null),
                'catatan' => trim($row['catatan'] ?? ''),
                'poin' => self::poin($row['poin'] ?? ''),
            ];

            return;
        }

        if ($section === 'indikator') {
            if (trim($row['kode'] ?? '') === '' && trim($row['indikator'] ?? '') === '') {
                return;
            }
            $out['indicators'][] = [
                'kode' => trim($row['kode'] ?? ''),
                'indikator' => trim($row['indikator'] ?? ''),
                'capaian' => trim($row['capaian'] ?? ''),
                'status' => self::kategori($row['status'] ?? null),
                'narasi' => trim($row['narasi'] ?? ''),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private static function parseCsv(string $content): array
    {
        $out = [];
        $fp = fopen('php://temp', 'r+');
        if ($fp === false) {
            throw new InvalidArgumentException('File tidak dapat dibaca.');
        }

        fwrite($fp, $content);
        rewind($fp);

        $header = null;
        while (($row = fgetcsv($fp, 0, ',', '"', '\\')) !== false) {
            if (count($row) === 1 && $row[0] === null) {
                continue; // baris kosong
            }
            if (trim(implode('', array_map(strval(...), $row))) === '') {
                continue;
            }

            if ($header === null) {
                $header = array_map(static fn ($c): string => mb_strtolower(trim((string) $c)), $row);

                continue;
            }

            $map = [];
            foreach ($header as $i => $name) {
                if ($name !== '') {
                    $map[$name] = isset($row[$i]) ? trim((string) $row[$i]) : '';
                }
            }

            self::mergeCsvRow($out, mb_strtolower(trim((string) ($map['bagian'] ?? ''))), $map);
        }

        fclose($fp);

        return $out;
    }

    /**
     * @param  array<string, string>  $c
     * @param  array<string, mixed>  $out
     */
    private static function mergeCsvRow(array &$out, string $bagian, array $c): void
    {
        switch ($bagian) {
            case 'identitas':
                if (($c['tahun'] ?? '') !== '') {
                    $out['year'] = (int) preg_replace('/\D/', '', $c['tahun']);
                }
                if (($c['judul'] ?? '') !== '') {
                    $out['title'] = $c['judul'];
                }
                if (($c['status_publikasi'] ?? '') !== '') {
                    $out['is_published'] = str_contains(mb_strtolower($c['status_publikasi']), 'terpublikasi');
                }
                if (($c['intro'] ?? '') !== '') {
                    $out['intro'] = $c['intro'];
                }
                if (($c['disclaimer'] ?? '') !== '') {
                    $out['disclaimer'] = $c['disclaimer'];
                }
                break;

            case 'kpi':
                if (($c['label'] ?? '') === '' && ($c['nilai'] ?? '') === '') {
                    break;
                }
                $out['kpi'][] = [
                    'key' => $c['key'] ?? '',
                    'label' => $c['label'] ?? '',
                    'value' => self::numeric($c['nilai'] ?? ''),
                    'suffix' => $c['satuan'] ?? '',
                    'kategori' => self::kategori($c['kategori'] ?? null),
                    'tren' => self::nulled($c['tren'] ?? null),
                    'tren_note' => self::nulled($c['catatan_tren'] ?? null),
                ];
                break;

            case 'kartu':
                if (($c['judul_kartu'] ?? '') === '' && ($c['skor'] ?? '') === '') {
                    break;
                }
                $grup = in_array(($c['grup'] ?? ''), self::GRUP, true) ? $c['grup'] : 'akademik';
                $out['sections'][$grup][] = [
                    'title' => $c['judul_kartu'] ?? '',
                    'skor' => $c['skor'] ?? '',
                    'kategori' => self::kategori($c['kategori'] ?? null),
                    'catatan' => $c['catatan_kartu'] ?? '',
                    'poin' => self::poin($c['poin'] ?? ''),
                ];
                break;

            case 'indikator':
                if (($c['kode'] ?? '') === '' && ($c['indikator'] ?? '') === '') {
                    break;
                }
                $out['indicators'][] = [
                    'kode' => $c['kode'] ?? '',
                    'indikator' => $c['indikator'] ?? '',
                    'capaian' => $c['capaian'] ?? '',
                    'status' => self::kategori($c['status'] ?? null),
                    'narasi' => $c['narasi'] ?? '',
                ];
                break;

            case 'grafik_batang':
                if (($c['grafik_tahun_1'] ?? '') !== '') {
                    $out['bar_label_sebelum'] = $c['grafik_tahun_1'];
                }
                if (($c['grafik_tahun_2'] ?? '') !== '') {
                    $out['bar_label_sesudah'] = $c['grafik_tahun_2'];
                }
                if (($c['grafik_label'] ?? '') !== '') {
                    $out['bar_labels'] = $c['grafik_label'];
                }
                if (($c['grafik_nilai_1'] ?? '') !== '') {
                    $out['bar_sebelum'] = $c['grafik_nilai_1'];
                }
                if (($c['grafik_nilai_2'] ?? '') !== '') {
                    $out['bar_sesudah'] = $c['grafik_nilai_2'];
                }
                break;

            case 'grafik_radar':
                if (($c['grafik_label'] ?? '') !== '') {
                    $out['radar_labels'] = $c['grafik_label'];
                }
                if (($c['grafik_nilai_1'] ?? '') !== '') {
                    $out['radar_values'] = $c['grafik_nilai_1'];
                }
                break;
        }
    }

    /**
     * Normalisasi angka: "75,53" -> "75.53" (desimal koma Indonesia).
     */
    private static function numeric(string $value): string
    {
        $v = trim($value);

        return preg_match('/^-?\d+,\d+$/', $v) === 1 ? str_replace(',', '.', $v) : $v;
    }

    private static function nulled(mixed $value): ?string
    {
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    /**
     * Poin rincian: pisah ";" atau baris baru -> teks multi-baris untuk textarea form.
     */
    private static function poin(string $cell): string
    {
        $items = array_values(array_filter(
            array_map('trim', preg_split('/[;\r\n]+/', $cell) ?: []),
            fn (string $s): bool => $s !== '',
        ));

        return implode("\n", $items);
    }

    private static function kategori(mixed $value): string
    {
        $s = mb_strtolower(trim((string) $value));

        return match (true) {
            $s === '' => 'Baik',
            str_contains($s, 'sangat baik') => 'Sangat Baik',
            str_contains($s, 'perlu perhatian') => 'Perlu Perhatian',
            str_contains($s, 'baik') => 'Baik',
            str_contains($s, 'aman') => 'Aman',
            str_contains($s, 'sedang') => 'Sedang',
            str_contains($s, 'cukup') => 'Cukup',
            str_contains($s, 'rendah') => 'Rendah',
            default => 'Baik',
        };
    }
}
