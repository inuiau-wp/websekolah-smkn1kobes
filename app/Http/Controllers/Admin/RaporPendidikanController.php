<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RaporPendidikan;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RaporPendidikanController extends Controller
{
    /**
     * Display a listing of the rapor pendidikan records.
     */
    public function index(): View
    {
        $rapor = RaporPendidikan::query()->orderByDesc('year')->get();

        return view('admin.rapor.index', compact('rapor'));
    }

    /**
     * Show the form for creating a new rapor pendidikan record.
     */
    public function create(): View
    {
        return view('admin.rapor.create', ['rapor' => new RaporPendidikan]);
    }

    /**
     * Store a newly created rapor pendidikan record.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $rapor = new RaporPendidikan($validated);
        $rapor->is_published = $request->boolean('is_published');
        $rapor->data = $this->buildData($request);
        $rapor->save();

        return redirect()->route('admin.rapor-pendidikan.index')
            ->with('status', 'Rapor Pendidikan tahun '.$rapor->year.' berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified rapor pendidikan record.
     */
    public function edit(RaporPendidikan $rapor_pendidikan): View
    {
        return view('admin.rapor.edit', ['rapor' => $rapor_pendidikan]);
    }

    /**
     * Update the specified rapor pendidikan record.
     */
    public function update(Request $request, RaporPendidikan $rapor_pendidikan): RedirectResponse
    {
        $validated = $this->validated($request, $rapor_pendidikan);

        $rapor_pendidikan->fill($validated);
        $rapor_pendidikan->is_published = $request->boolean('is_published');
        $rapor_pendidikan->data = $this->buildData($request);
        $rapor_pendidikan->save();

        return redirect()->route('admin.rapor-pendidikan.index')
            ->with('status', 'Rapor Pendidikan tahun '.$rapor_pendidikan->year.' berhasil diperbarui.');
    }

    /**
     * Remove the specified rapor pendidikan record.
     */
    public function destroy(RaporPendidikan $rapor_pendidikan): RedirectResponse
    {
        $year = $rapor_pendidikan->year;
        $rapor_pendidikan->delete();

        return redirect()->route('admin.rapor-pendidikan.index')
            ->with('status', 'Rapor Pendidikan tahun '.$year.' berhasil dihapus.');
    }

    /**
     * Shared validation rules.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?RaporPendidikan $rapor = null): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:rapor_pendidikan,year'.($rapor ? ','.$rapor->id : '')],
            'title' => ['required', 'string', 'max:255'],
            'intro' => ['required', 'string', 'max:5000'],
            'disclaimer' => ['required', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'year.unique' => 'Rapor untuk tahun ini sudah ada.',
        ]);
    }

    /**
     * Susun struktur JSON `data` dari input form terstruktur.
     *
     * @return array<string, mixed>
     */
    private function buildData(Request $request): array
    {
        $validated = $request->validate([
            'kpi' => ['required', 'array', 'min:1', 'max:8'],
            'kpi.*.key' => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/'],
            'kpi.*.label' => ['required', 'string', 'max:100'],
            'kpi.*.value' => ['required', 'numeric', 'min:0', 'max:100'],
            'kpi.*.suffix' => ['nullable', 'string', 'max:10'],
            'kpi.*.kategori' => ['required', 'string', 'max:50'],
            'kpi.*.tren' => ['nullable', 'string', 'max:20'],
            'kpi.*.tren_note' => ['nullable', 'string', 'max:100'],

            'sections' => ['required', 'array', 'size:3'],
            'sections.akademik' => ['required', 'array', 'min:1'],
            'sections.vokasi' => ['required', 'array', 'min:1'],
            'sections.kelola' => ['required', 'array', 'min:1'],
            'sections.*.*.title' => ['required', 'string', 'max:150'],
            'sections.*.*.skor' => ['required', 'string', 'max:50'],
            'sections.*.*.kategori' => ['required', 'string', 'max:50'],
            'sections.*.*.catatan' => ['required', 'string', 'max:1000'],
            'sections.*.*.poin' => ['nullable', 'string', 'max:2000'],

            'bar_labels' => ['required', 'string', 'max:500'],
            'bar_sebelum' => ['required', 'string', 'max:500'],
            'bar_sesudah' => ['required', 'string', 'max:500'],
            'bar_label_sebelum' => ['required', 'string', 'max:20'],
            'bar_label_sesudah' => ['required', 'string', 'max:20'],
            'radar_labels' => ['required', 'string', 'max:500'],
            'radar_values' => ['required', 'string', 'max:500'],

            'indicators' => ['required', 'array', 'min:1', 'max:60'],
            'indicators.*.kode' => ['required', 'string', 'max:20'],
            'indicators.*.indikator' => ['required', 'string', 'max:200'],
            'indicators.*.capaian' => ['required', 'string', 'max:50'],
            'indicators.*.status' => ['required', 'string', 'max:50'],
            'indicators.*.narasi' => ['required', 'string', 'max:1000'],
        ], [
            'kpi.required' => 'Minimal satu KPI harus diisi.',
            'kpi.*.key.regex' => 'Key KPI hanya boleh huruf kecil, angka, dan garis bawah (contoh: literasi).',
            'sections.*.*.title.required' => 'Judul setiap kartu dimensi wajib diisi.',
            'sections.*.*.catatan.required' => 'Catatan setiap kartu dimensi wajib diisi.',
            'indicators.*.indikator.required' => 'Nama indikator wajib diisi.',
        ]);

        $csv = fn (string $value): array => array_values(array_filter(array_map('trim', explode(',', $value)), fn (string $s): bool => $s !== ''));
        $numbers = fn (string $value): array => array_map('floatval', $csv($value));
        $lines = fn (?string $value): array => array_values(array_filter(array_map('trim', explode("\n", (string) $value)), fn (string $s): bool => $s !== ''));

        $sections = [];
        foreach (['akademik', 'vokasi', 'kelola'] as $group) {
            $sections[$group] = collect($validated['sections'][$group])
                ->map(fn (array $item): array => [
                    'title' => $item['title'],
                    'skor' => $item['skor'],
                    'kategori' => $item['kategori'],
                    'catatan' => $item['catatan'],
                    'poin' => $lines($item['poin'] ?? null),
                ])
                ->all();
        }

        return [
            'kpi' => collect($validated['kpi'])
                ->map(fn (array $item): array => [
                    'key' => filled($item['key'] ?? null) ? $item['key'] : 'kpi_'.md5($item['label'].$item['value']),
                    'label' => $item['label'],
                    'value' => (float) $item['value'],
                    'suffix' => $item['suffix'] ?? '',
                    'kategori' => $item['kategori'],
                    'tren' => $item['tren'] ?? null,
                    'tren_note' => $item['tren_note'] ?? null,
                ])
                ->all(),
            'sections' => $sections,
            'charts' => [
                'bar' => [
                    'labels' => $csv($validated['bar_labels']),
                    'label_sebelum' => $validated['bar_label_sebelum'],
                    'label_sesudah' => $validated['bar_label_sesudah'],
                    'sebelum' => $numbers($validated['bar_sebelum']),
                    'sesudah' => $numbers($validated['bar_sesudah']),
                ],
                'radar' => [
                    'labels' => $csv($validated['radar_labels']),
                    'values' => $numbers($validated['radar_values']),
                ],
            ],
            'indicators' => collect($validated['indicators'])->map(fn (array $item): array => [
                'kode' => $item['kode'],
                'indikator' => $item['indikator'],
                'capaian' => $item['capaian'],
                'status' => $item['status'],
                'narasi' => $item['narasi'],
            ])->all(),
        ];
    }
}
