<?php

namespace App\Models;

use Database\Factories\SchoolProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    /** @use HasFactory<SchoolProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'school_name',
        'tagline',
        'address',
        'phone',
        'email',
        'wa_number',
        'logo_path',
        'cover_path',
        'map_latitude',
        'map_longitude',
        'vision',
        'mission',
        'history',
        'accreditation',
        'headmaster',
    ];

    /**
     * Get the full URL for the school logo.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        if (str_starts_with($this->logo_path, 'http')) {
            return $this->logo_path;
        }

        return asset('storage/'.$this->logo_path);
    }

    /**
     * Get the full URL for the school cover image (hero).
     */
    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        if (str_starts_with($this->cover_path, 'http')) {
            return $this->cover_path;
        }

        return asset('storage/'.$this->cover_path);
    }
}
