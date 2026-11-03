<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JurusanController extends Controller
{
    /**
     * Display a listing of the study programs for management.
     */
    public function index(): View
    {
        $jurusan = Jurusan::query()->orderBy('code')->get();

        return view('admin.jurusan.index', compact('jurusan'));
    }

    /**
     * Show the form for creating a new study program.
     */
    public function create(): View
    {
        $teachers = User::whereIn('role', ['guru', 'admin'])->orderBy('name')->get();

        return view('admin.jurusan.create', compact('teachers'));
    }

    /**
     * Store a newly created study program.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $jurusan = new Jurusan($validated);
        $jurusan->image_path = $this->storeImage($request);
        $jurusan->save();

        return redirect()->route('admin.jurusan.index')->with('status', 'Jurusan "'.$jurusan->name.'" berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified study program.
     */
    public function edit(Jurusan $jurusan): View
    {
        $teachers = User::whereIn('role', ['guru', 'admin'])->orderBy('name')->get();

        return view('admin.jurusan.edit', compact('jurusan', 'teachers'));
    }

    /**
     * Update the specified study program.
     */
    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $validated = $this->validated($request, $jurusan);

        $jurusan->fill($validated);

        if ($request->hasFile('image')) {
            $this->deleteImage($jurusan);
            $jurusan->image_path = $request->file('image')->store('jurusan', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($jurusan);
            $jurusan->image_path = null;
        }

        $jurusan->save();

        return redirect()->route('admin.jurusan.index')->with('status', 'Jurusan "'.$jurusan->name.'" berhasil diperbarui.');
    }

    /**
     * Remove the specified study program.
     */
    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        $this->deleteImage($jurusan);
        $name = $jurusan->name;
        $jurusan->delete();

        return redirect()->route('admin.jurusan.index')->with('status', 'Jurusan "'.$name.'" berhasil dihapus.');
    }

    /**
     * Shared validation rules.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Jurusan $jurusan = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'unique:jurusan,slug'.($jurusan ? ','.$jurusan->id : '')],
            'code' => ['required', 'string', 'max:10', 'unique:jurusan,code'.($jurusan ? ','.$jurusan->id : '')],
            'color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'description' => ['nullable', 'string', 'max:2000'],
            'competencies' => ['nullable', 'string', 'max:1000'],
            'vision' => ['nullable', 'string', 'max:1000'],
            'mission' => ['nullable', 'string', 'max:2000'],
            'head_of_department_id' => ['nullable', 'integer', 'exists:users,id'],
            'head_of_department' => ['nullable', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'max:2048'], // maks 2 MB
        ], [
            'code.unique' => 'Kode jurusan sudah dipakai jurusan lain.',
            'slug.unique' => 'Slug sudah dipakai jurusan lain.',
            'color.regex' => 'Warna harus dalam format hex, contoh: #0d542b',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);
    }

    /**
     * Simpan gambar ke disk public; kembalikan path relatif (dipakai saat store).
     */
    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('jurusan', 'public');
    }

    private function deleteImage(Jurusan $jurusan): void
    {
        if ($jurusan->image_path && ! str_starts_with($jurusan->image_path, 'http')) {
            Storage::disk('public')->delete($jurusan->image_path);
        }
    }
}
