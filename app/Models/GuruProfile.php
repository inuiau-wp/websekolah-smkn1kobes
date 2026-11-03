<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruProfile extends Model
{
    protected $fillable = [
        'user_id',
        'headline',
        'cv_content',
        'prestasi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Daftar prestasi siap render (per baris).
     *
     * @return array<int, string>
     */
    public function prestasiList(): array
    {
        if (! $this->prestasi) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->prestasi) ?: [])));
    }

    /**
     * Baris non-kosong CV (untuk render rapi).
     *
     * @return array<int, string>
     */
    public function cvLines(): array
    {
        if (! $this->cv_content) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->cv_content) ?: [])));
    }
}
