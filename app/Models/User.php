<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'position', 'nip', 'photo', 'bio', 'social_links'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * URL foto profil pengguna atau null jika tidak ada.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                if (! $this->photo) {
                    return null;
                }

                if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                    return $this->photo;
                }

                return Storage::disk('public')->url($this->photo);
            }
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
        ];
    }

    /**
     * Halaman pribadi guru (jika role guru).
     */
    public function guruProfile(): HasOne
    {
        return $this->hasOne(GuruProfile::class);
    }

    /**
     * Jurusan yang dikelola oleh user ini (untuk kepala program / kepala jurusan).
     */
    public function managedJurusan(): ?Jurusan
    {
        $code = collect(['ATP', 'TKJ', 'AKL'])
            ->first(fn ($code) => str_contains($this->position ?? '', 'Kepala Program '.$code)
                || str_contains($this->position ?? '', 'Kepala Jurusan '.$code)
                || str_contains($this->position ?? '', 'Kaprog '.$code));

        return $code ? Jurusan::where('code', $code)->first() : null;
    }

    /**
     * Apakah user ini mengelola jurusan tertentu.
     */
    public function managesJurusan(Jurusan $jurusan): bool
    {
        $own = $this->managedJurusan();

        return $own !== null && $own->is($jurusan);
    }
}
