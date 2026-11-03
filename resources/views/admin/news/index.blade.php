<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Berita</h1>
            <p class="text-gray-600 mt-1">Berita, pengumuman, dan agenda yang tampil di website.</p>
        </div>
        <a href="{{ route('admin.news.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tulis Berita
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Statistik + filter kategori -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.news.index') }}"
           class="px-4 py-2 rounded-full text-xs font-semibold {{ $stats['total'] ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-200' }}">
            Semua ({{ $stats['total'] }})
        </a>
        <span class="px-4 py-2 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Published: {{ $stats['published'] }}</span>
        <span class="px-4 py-2 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Draft: {{ $stats['draft'] }}</span>

        @foreach (\App\Models\News::categories() as $value => $label)
            <a href="{{ request()->query('category') === $value ? route('admin.news.index') : route('admin.news.index', ['category' => $value]) }}"
               class="px-4 py-2 rounded-full text-xs font-medium {{ request()->query('category') === $value ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Jurusan</th>
                    <th class="px-4 py-3">Tayang</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($news as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($item->cover_image)
                                    <img src="{{ $item->cover_url }}" alt="" class="h-10 w-16 object-cover rounded-lg border">
                                @else
                                    <div class="h-10 w-16 rounded-lg border bg-gradient-to-br from-gray-100 to-gray-200"></div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate max-w-xs">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-400">/berita/{{ $item->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         {{ match ($item->category) {
                                             'announcement' => 'bg-amber-100 text-amber-800',
                                             'agenda' => 'bg-purple-100 text-purple-800',
                                             'materi' => 'bg-indigo-100 text-indigo-800',
                                             'tutorial' => 'bg-cyan-100 text-cyan-800',
                                             default => 'bg-emerald-100 text-emerald-800',
                                         } }}">
                                {{ $item->categoryLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                                {{ $item->status === 'published' ? 'Tayang' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->jurusan?->code ?? 'Umum' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $item->published_at?->translatedFormat('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if ($item->status === 'published')
                                <a href="{{ route('news.show', $item->slug) }}" target="_blank"
                                   class="text-xs font-medium text-gray-500 hover:text-gray-900 mr-3">Lihat</a>
                            @endif
                            <a href="{{ route('admin.news.edit', $item) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                            <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Hapus berita {{ $item->title }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada konten pada filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $news->links() }}
    </div>
</x-admin-layout>
