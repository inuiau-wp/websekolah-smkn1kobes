<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Guru dengan jabatan Kepala Program / Kepala Jurusan / Kaprog dapat
 * mengedit konten jurusan di kewenangannya (deskripsi, kompetensi, foto).
 * Structur data lain (nama, kode, warna) tetap menjadi wewenang admin.
 */
class JurusanController extends Controller
{
    /**
     * Daftar jurusan yang dikelola oleh guru ini.
     */
    public function index(): View
    {
        $managed = auth()->user()->managedJurusan();

        return view('guru.jurusan.index', compact('managed'));
    }

    /**
     * Form edit konten jurusan milik guru (harus satu-satunya yang dikelola).
     */
    public function edit(Jurusan $jurusan): View
    {
        $this->authorizeManaged($jurusan);

        return view('guru.jurusan.edit', compact('jurusan'));
    }

    /**
     * Perbarui konten jurusan yang dikelola.
     */
    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $this->authorizeManaged($jurusan);

        $validated = $request->validate([
            'description' => ['nullable', 'string', 'max:2000'],
            'competencies' => ['nullable', 'string', 'max:1000'],
            'vision' => ['nullable', 'string', 'max:1000'],
            'mission' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ], [
            'image.image' => 'Berkas harus berupa gambar.',
            'image.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $jurusan->fill([
            'description' => $validated['description'] ?? null,
            'competencies' => $validated['competencies'] ?? null,
            'vision' => $validated['vision'] ?? null,
            'mission' => $validated['mission'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($jurusan);
            $jurusan->image_path = $request->file('image')->store('jurusan', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($jurusan);
            $jurusan->image_path = null;
        }

        $jurusan->save();

        return redirect()->route('guru.jurusan.edit', $jurusan)->with('status', 'Konten jurusan '.$jurusan->code.' berhasil diperbarui.');
    }

    /**
     * Pastikan guru ini berhak mengelola jurusan tersebut (hanya miliknya).
     */
    private function authorizeManaged(Jurusan $jurusan): void
    {
        /** @var User $user */
        if (! auth()->user()->managesJurusan($jurusan)) {
            abort(404);
        }
    }

    private function deleteImage(Jurusan $jurusan): void
    {
        if ($jurusan->image_path && ! str_starts_with($jurusan->image_path, 'http')) {
            Storage::disk('public')->delete($jurusan->image_path);
        }
    }
}
