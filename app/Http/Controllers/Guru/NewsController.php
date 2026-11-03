<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Concerns\HandlesNewsAttachment;
use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    use HandlesNewsAttachment;

    /**
     * Tampilkan daftar berita milik guru yang sedang login.
     */
    public function index(Request $request): View
    {
        $teacherId = $request->user()->id;

        $news = News::query()
            ->where('author_id', $teacherId)
            ->with('jurusan')
            ->category($request->query('category'))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => News::where('author_id', $teacherId)->count(),
            'published' => News::where('author_id', $teacherId)->published()->count(),
            'draft' => News::where('author_id', $teacherId)->where('status', 'draft')->count(),
        ];

        return view('guru.news.index', compact('news', 'stats'));
    }

    /**
     * Form tambah berita baru.
     */
    public function create(): View
    {
        return view('guru.news.create', [
            'jurusanList' => Jurusan::orderBy('code')->get(),
        ]);
    }

    /**
     * Simpan berita baru (penulis otomatis = guru yang sedang login).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $news = new News($validated);
        $news->author_id = $request->user()->id;
        $news->cover_image = $this->storeImage($request);
        $this->applyAttachment($request, $news);
        $news->save();

        return redirect()->route('guru.news.index')->with('status', 'Berita "'.$news->title.'" berhasil dibuat.');
    }

    /**
     * Form edit berita (hanya berita milik sendiri).
     */
    public function edit(News $news): View
    {
        $this->authorizeAuthor($news);

        $news->load('jurusan');

        return view('guru.news.edit', [
            'news' => $news,
            'jurusanList' => Jurusan::orderBy('code')->get(),
        ]);
    }

    /**
     * Perbarui berita (hanya berita milik sendiri).
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $this->authorizeAuthor($news);

        $validated = $this->validated($request, $news);

        $news->fill($validated);

        if ($request->hasFile('cover')) {
            $this->deleteImage($news);
            $news->cover_image = $request->file('cover')->store('berita', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($news);
            $news->cover_image = null;
        }

        $this->applyAttachment($request, $news);

        $news->save();

        return redirect()->route('guru.news.index')->with('status', 'Berita "'.$news->title.'" berhasil diperbarui.');
    }

    /**
     * Hapus berita (hanya berita milik sendiri).
     */
    public function destroy(News $news): RedirectResponse
    {
        $this->authorizeAuthor($news);

        $this->deleteImage($news);
        $this->deleteAttachment($news);
        $title = $news->title;
        $news->delete();

        return redirect()->route('guru.news.index')->with('status', 'Berita "'.$title.'" berhasil dihapus.');
    }

    /**
     * Pastikan berita milik guru yang sedang login.
     */
    private function authorizeAuthor(News $news): void
    {
        if ($news->author_id !== auth()->id()) {
            abort(404);
        }
    }

    /**
     * Validasi input berita.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?News $news = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:news,slug'.($news ? ','.$news->id : '')],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'content' => ['nullable', 'string', 'max:200000'],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(News::categories()))],
            'jurusan_id' => ['nullable', 'integer', 'exists:jurusan,id'],
            'status' => ['required', 'string', 'in:draft,published'],
            'cover' => ['nullable', 'image', 'max:2048'],
            'attachment' => ['nullable', 'file', 'extensions:md,pdf,docx', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul wajib diisi.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'status.in' => 'Status harus draft atau published.',
            'jurusan_id.exists' => 'Jurusan tidak ditemukan.',
            'cover.image' => 'Berkas harus berupa gambar.',
            'cover.max' => 'Ukuran cover maksimal 2 MB.',
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        return $request->file('cover')->store('berita', 'public');
    }

    private function deleteImage(News $news): void
    {
        if ($news->cover_image && ! str_starts_with($news->cover_image, 'http')) {
            Storage::disk('public')->delete($news->cover_image);
        }
    }
}
