<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Rule tidak dipakai langsung (category free-form) — tetap dukung validasi sederhana di validated().

class GalleryController extends Controller
{
    /**
     * Prefix route & view yang dipakai; guru meng-override menjadi 'guru'.
     */
    protected string $routePrefix = 'admin';

    protected string $viewPrefix = 'admin.gallery';

    public function index(Request $request): View
    {
        $photos = Gallery::query()
            ->with('uploadedBy')
            ->category($request->query('category'))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Gallery::query()
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $stats = [
            'total' => Gallery::count(),
            'admin_upload' => Gallery::query()->whereNotLike('image_path', 'http%')->count(),
        ];

        $prefix = $this->routePrefix;

        return view($this->viewPrefix.'.index', compact('photos', 'categories', 'stats', 'prefix'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $photos = [];

        // Multi-file: 'images' (create), 'image' single (edit).
        foreach ($request->file('images', []) as $file) {
            $photos[] = $file->store('galeri', 'public');
        }

        if ($request->hasFile('image')) {
            $photos[] = $request->file('image')->store('galeri', 'public');
        }

        if ($photos === []) {
            return back()->with('error', 'Pilih minimal satu gambar untuk diunggah.');
        }

        foreach ($photos as $path) {
            Gallery::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'image_path' => $path,
                'category' => $validated['category'] ?? null,
                'uploaded_by' => $request->user()->id,
            ]);
        }

        $count = count($photos);

        return redirect()->route($this->routePrefix.'.gallery.index')->with('status', $count.' foto berhasil diunggah.');
    }

    public function create(): View
    {
        return view($this->viewPrefix.'.create', [
            'categories' => Gallery::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'prefix' => $this->routePrefix,
        ]);
    }

    public function edit(Gallery $gallery): View
    {
        return view($this->viewPrefix.'.edit', [
            'photo' => $gallery,
            'categories' => Gallery::query()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'prefix' => $this->routePrefix,
        ]);
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], [
            'title.required' => 'Judul foto wajib diisi.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $gallery->fill($validated);

        if ($request->hasFile('image')) {
            $this->deleteImage($gallery);
            $gallery->image_path = $request->file('image')->store('galeri', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($gallery);

            return redirect()->route($this->routePrefix.'.gallery.index')->with('error', 'Foto utama tidak boleh kosong — unggah gambar pengganti.');
        }

        $gallery->save();

        return redirect()->route($this->routePrefix.'.gallery.index')->with('status', 'Foto "'.$gallery->title.'" berhasil diperbarui.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $this->deleteImage($gallery);
        $title = $gallery->title;
        $gallery->delete();

        return redirect()->route($this->routePrefix.'.gallery.index')->with('status', 'Foto "'.$title.'" berhasil dihapus.');
    }

    /**
     * Shared validation rules.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['nullable', 'string', 'max:50'],
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:2048'],
            'as_separate' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul foto wajib diisi.',
            'images.required' => 'Pilih minimal satu gambar.',
            'images.*.image' => 'Setiap berkas harus berupa gambar.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 2 MB.',
        ]);
    }

    private function deleteImage(Gallery $gallery): void
    {
        if ($gallery->image_path && ! str_starts_with($gallery->image_path, 'http')) {
            Storage::disk('public')->delete($gallery->image_path);
        }
    }
}
