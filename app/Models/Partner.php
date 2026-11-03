<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'url',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * URL logo siap pakai; null bila tidak ada (tampil teks nama).
     */
    public function logoSrc(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }

        return str_starts_with($this->logo_path, 'http')
            ? $this->logo_path
            : asset('storage/'.$this->logo_path);
    }
}
