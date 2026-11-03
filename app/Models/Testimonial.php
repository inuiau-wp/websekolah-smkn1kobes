<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'content',
        'photo_path',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * URL foto siap pakai; fallback avatar placeholder bila tidak ada.
     */
    public function photoSrc(): string
    {
        if (empty($this->photo_path)) {
            return 'https://i.pravatar.cc/100?u='.md5($this->name);
        }

        return str_starts_with($this->photo_path, 'http')
            ? $this->photo_path
            : asset('storage/'.$this->photo_path);
    }
}
