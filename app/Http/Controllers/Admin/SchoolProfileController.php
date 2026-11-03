<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    /**
     * Form edit profil sekolah (satu baris, id=1).
     */
    public function edit(): View
    {
        $profil = SchoolProfile::query()->firstOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi']
        );

        return view('admin.profil.edit', compact('profil'));
    }

    /**
     * Update profil sekolah & logo.
     */
    public function update(Request $request): RedirectResponse
    {
        $profil = SchoolProfile::query()->firstOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi']
        );

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:200'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'wa_number' => ['nullable', 'string', 'max:20'],
            'vision' => ['nullable', 'string', 'max:2000'],
            'mission' => ['nullable', 'string', 'max:2000'],
            'history' => ['nullable', 'string', 'max:5000'],
            'accreditation' => ['nullable', 'string', 'max:20'],
            'headmaster' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'cover' => ['nullable', 'image', 'max:4096'],
            'map_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'map_longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'logo.image' => 'Berkas harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
            'cover.image' => 'Berkas harus berupa gambar.',
            'cover.max' => 'Ukuran gambar cover maksimal 4 MB.',
            'map_latitude.numeric' => 'Latitude harus berupa angka.',
            'map_latitude.between' => 'Latitude harus di antara -90 sampai 90.',
            'map_longitude.numeric' => 'Longitude harus berupa angka.',
            'map_longitude.between' => 'Longitude harus di antara -180 sampai 180.',
        ]);

        if ($request->hasFile('logo')) {
            $old = $profil->logo_path;
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $validated['logo_path'] = $request->file('logo')->store('profil', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $old = $profil->logo_path;
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $validated['logo_path'] = null;
        }

        if ($request->hasFile('cover')) {
            $this->deleteOldFile($profil->cover_path);
            $validated['cover_path'] = $request->file('cover')->store('profil', 'public');
        } elseif ($request->boolean('remove_cover')) {
            $this->deleteOldFile($profil->cover_path);
            $validated['cover_path'] = null;
        }

        $profil->update($validated);

        return redirect()->route('admin.profil.edit')->with('status', 'Profil sekolah "'.$profil->school_name.'" berhasil diperbarui.');
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
