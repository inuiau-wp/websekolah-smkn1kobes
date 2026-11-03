{{-- Form bersama create/edit berita --}}
@php($news ??= null)

<form method="POST" action="{{ $news ? route('admin.news.update', $news) : route('admin.news.store') }}"
      enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($news)
        @method('PATCH')
    @endif

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
        <input id="title" name="title" type="text" required value="{{ old('title', $news?->title) }}"
               placeholder="Contoh: Penerimaan Peserta Didik Baru 2026"
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan (cuplikan untuk kartu)</label>
        <textarea id="excerpt" name="excerpt" rows="2" maxlength="300"
                  placeholder="Satu-dua kalimat yang tampil di daftar beranda/daftar berita"
                  class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('excerpt', $news?->excerpt) }}</textarea>
        @error('excerpt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Isi Lengkap</label>
        <textarea id="content" name="content" rows="14"
                  placeholder="Tulis isi berita, modul, atau tutorial secara rinci..."
                  class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('content', $news?->content) }}</textarea>
        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
            <select id="category" name="category" required
                    class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                @foreach (\App\Models\News::categories() as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', $news?->category ?? 'news') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-1">Relasi Jurusan</label>
            <select id="jurusan" name="jurusan_id"
                    class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                <option value="">Umum (semua jurusan)</option>
                @foreach ($jurusanList as $j)
                    <option value="{{ $j->id }}" @selected(old('jurusan_id', $news?->jurusan_id) == $j->id)>
                        {{ $j->code }} - {{ $j->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select id="status" name="status" required
                    class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                <option value="draft" @selected(old('status', $news?->status ?? 'draft') === 'draft')>Draft (tidak tampil)</option>
                <option value="published" @selected(old('status', $news?->status ?? 'draft') === 'published')>Published (tayang)</option>
            </select>
            <p class="mt-1 text-xs text-gray-400">Simpan published → tanggal tayang otomatis diisi.</p>
        </div>
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $news?->slug) }}"
               placeholder="Kosongkan untuk dibuat otomatis dari judul"
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 font-mono text-sm">
        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="border border-dashed border-gray-300 rounded-lg p-4">
        <label for="cover" class="block text-sm font-medium text-gray-700 mb-2">Cover Image (maks 2 MB)</label>
        @if ($news?->cover_image)
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ $news->cover_url }}" alt="Cover saat ini" class="h-16 w-24 object-cover rounded-lg border">
                <div class="text-xs text-gray-500">
                    <p class="font-medium text-gray-700">Cover saat ini</p>
                    <label class="mt-1 inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-red-600">Hapus cover saat ini</span>
                    </label>
                </div>
            </div>
        @endif
        <input id="cover" name="cover" type="file" accept="image/*"
               class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
        @error('cover') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="border border-dashed border-gray-300 rounded-lg p-4">
        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Lampiran Materi (opsional — .md / .pdf / .docx, maks 10 MB)</label>
        @if ($news?->file_path)
            <div class="flex items-center gap-4 mb-3">
                <a href="{{ asset('storage/'.$news->file_path) }}" target="_blank" class="text-sm text-blue-600 underline">
                    {{ $news->file_original_name }} ({{ strtoupper($news->file_type) }}) — lampiran saat ini
                </a>
                <label class="inline-flex items-center gap-1.5 cursor-pointer text-red-600 hover:text-red-800 text-sm">
                    <input type="checkbox" name="remove_attachment" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span>Hapus lampiran</span>
                </label>
            </div>
        @endif
        <p class="mb-2 text-xs text-gray-500">
            <span class="font-semibold text-gray-700">.md</span> → isinya dikonversi otomatis menjadi isi artikel (tidak disimpan sebagai file) •
            <span class="font-semibold text-gray-700">.pdf</span> → tampil ter-embed di halaman berita •
            <span class="font-semibold text-gray-700">.docx</span> → tampil tombol unduh
        </p>
        <input id="attachment" name="attachment" type="file" accept=".md,.pdf,.docx"
               class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
        <p id="md-preview-note" class="mt-2 text-xs text-emerald-700 hidden"></p>
        @error('attachment') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            {{ $news ? 'Simpan Perubahan' : 'Buat Berita' }}
        </button>
        <a href="{{ route('admin.news.index') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
    </div>
</form>

<x-news-editor />
