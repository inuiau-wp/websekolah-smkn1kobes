<?php

namespace App\Models;

use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'category',
        'uploaded_by',
    ];

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Filter berdasarkan kategori (opsional).
     *
     * @param  Builder<self>  $query
     */
    public function scopeCategory($query, ?string $category): mixed
    {
        return $category ? $query->where('category', $category) : $query;
    }

    /**
     * URL siap pakai: path lokasi lokal ke storage, URL eksternal langsung dipakai.
     */
    public function imageSrc(): string
    {
        return str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : asset('storage/'.$this->image_path);
    }
}
