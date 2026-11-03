<?php

namespace App\Http\Controllers\Admin;

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
     * Display a listing of the news for management.
     */
    public function index(Request $request): View
    {
        $news = News::query()
            ->with('jurusan')
            ->category($request->query('category'))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'published' => News::published()->count(),
            'draft' => News::query()->where('status', 'draft')->count(),
            'total' => News::count(),
        ];

        return view('admin.news.index', compact('news', 'stats'));
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create(): View
    {
        return view('admin.news.create', [
            'jurusanList' => Jurusan::orderBy('code')->get(),
        ]);
    }

    /**
     * Store a newly created news item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $news = new News($validated);
        $news->author_id = $request->user()->id; // penulis = yang membuat
        $news->cover_image = $this->storeImage($request);
        $this->applyAttachment($request, $news);
        $news->save();

        return redirect()->route('admin.news.index')->with('status', 'Berita "'.$news->title.'" berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified news item.
     */
    public function edit(News $news): View
    {
        $news->load('jurusan');

        return view('admin.news.edit', [
            'news' => $news,
            'jurusanList' => Jurusan::orderBy('code')->get(),
        ]);
    }

    /**
     * Update the specified news item.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $this->validated($request, $news);

        $news->fill($validated);
        $news->author_id ??= $request->user()->id; // fallback penulis (berita tanpa penulis)

        if ($request->hasFile('cover')) {
            $this->deleteImage($news);
            $news->cover_image = $request->file('cover')->store('berita', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($news);
            $news->cover_image = null;
        }

        $this->applyAttachment($request, $news);

        $news->save();

        return redirect()->route('admin.news.index')->with('status', 'Berita "'.$news->title.'" berhasil diperbarui.');
    }

    /**
     * Remove the specified news item.
     */
    public function destroy(News $news): RedirectResponse
    {
        $this->deleteImage($news);
        $this->deleteAttachment($news);
        $title = $news->title;
        $news->delete(); // softDeletes — bisa dipulihkan lewat database/admin nanti

        return redirect()->route('admin.news.index')->with('status', 'Berita "'.$title.'" berhasil dihapus.');
    }

    /**
     * Shared validation rules.
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
