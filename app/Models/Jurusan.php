<?php

namespace App\Models;

use Database\Factories\JurusanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Jurusan extends Model
{
    /** @use HasFactory<JurusanFactory> */
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'competencies',
        'vision',
        'mission',
        'head_of_department',
        'head_of_department_id',
        'color',
        'image_path',
    ];

    public function headOfDepartmentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_of_department_id');
    }

    /**
     * Mengambil User kepala jurusan dari relasi langsung, pencocokan jabatan, atau pencocokan nama.
     */
    public function resolvedHeadOfDepartmentUser(): ?User
    {
        if ($this->head_of_department_id) {
            $user = $this->relationLoaded('headOfDepartmentUser')
                ? $this->headOfDepartmentUser
                : $this->headOfDepartmentUser()->first();

            if ($user) {
                return $user;
            }
        }

        // Deteksi otomatis jika ada akun guru yang memiliki jabatan Kepala Program / Kepala Jurusan terkait
        $code = $this->code;
        $matchedUser = User::whereIn('role', ['guru', 'admin'])
            ->where(function ($q) use ($code) {
                $q->where('position', 'Kepala Program '.$code)
                    ->orWhere('position', 'Kepala Jurusan '.$code)
                    ->orWhere('position', 'like', '%Kaprog '.$code.'%')
                    ->orWhere('position', 'like', '%Kepala Program Keahlian '.$code.'%');
            })
            ->first();

        if ($matchedUser) {
            return $matchedUser;
        }

        // Jika kolom head_of_department diisi nama, cari user yang namanya cocok
        if (! empty($this->head_of_department)) {
            $userByName = User::whereIn('role', ['guru', 'admin'])
                ->where('name', $this->head_of_department)
                ->first();

            if ($userByName) {
                return $userByName;
            }
        }

        return null;
    }

    /**
     * Nama Kepala Jurusan (dari akun guru atau teks fallback).
     */
    public function getHeadOfDepartmentName(): ?string
    {
        return $this->resolvedHeadOfDepartmentUser()?->name ?? $this->head_of_department;
    }

    /**
     * Foto Kepala Jurusan (dari akun guru jika ada).
     */
    public function getHeadOfDepartmentPhotoUrl(): ?string
    {
        return $this->resolvedHeadOfDepartmentUser()?->photo_url;
    }

    protected static function booted(): void
    {
        // Slug dibuat otomatis dari nama (bukan kode) bila kosong
        static::saving(function (Jurusan $jurusan) {
            if (! $jurusan->slug) {
                $jurusan->slug = static::uniqueSlug($jurusan->name);
            }
        });
    }

    private static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'jurusan';
        $slug = $base;
        $i = 1;

        while (static::query()->where('slug', $slug)->when($ignoreId, fn (Builder $q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    /**
     * URL siap pakai: path disk public atau URL eksternal.
     */
    public function imageSrc(): string
    {
        return ! empty($this->image_path) && str_starts_with($this->image_path, 'http')
            ? $this->image_path
            : asset('storage/'.($this->image_path ?? 'images/placeholder-jurusan.jpg'));
    }
}
