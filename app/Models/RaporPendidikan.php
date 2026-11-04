<?php

namespace App\Models;

use Database\Factories\RaporPendidikanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Rapor Pendidikan per tahun rilis (publikasi etis — agregasi Level 2, UU KIP & UU PDP).
 * Struktur `data` (JSON):
 *  - kpi:        [{key, label, value, suffix, kategori, tren, tren_note}]
 *  - sections:   {akademik|vokasi|kelola: [{title, skor, kategori, catatan, poin[]}]}
 *  - charts:     {bar: {labels, sebelum, sesudah, label_sebelum, label_sesudah},
 *                 radar: {labels, values}}
 *  - indicators: [{kode, indikator, capaian, status, narasi}]
 */
class RaporPendidikan extends Model
{
    /** @use HasFactory<RaporPendidikanFactory> */
    use HasFactory;

    protected $table = 'rapor_pendidikan';

    protected $fillable = [
        'year',
        'title',
        'intro',
        'data',
        'disclaimer',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_published' => 'boolean',
            'data' => 'array',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Ambil nilai KPI berdasarkan key (mis. 'literasi', 'dudi').
     */
    public function kpi(string $key): ?array
    {
        return collect($this->data['kpi'] ?? [])
            ->firstWhere('key', $key);
    }

    /**
     * Kategori → warna badge (Baik hijau, Sedang amber, dst).
     */
    public static function kategoriColor(?string $kategori): string
    {
        return match (mb_strtolower((string) $kategori)) {
            'sangat baik', 'baik', 'aman', 'tinggi' => 'bg-emerald-100 text-emerald-800',
            'sedang', 'cukup' => 'bg-amber-100 text-amber-800',
            'perlu perhatian', 'rendah' => 'bg-rose-100 text-rose-800',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
