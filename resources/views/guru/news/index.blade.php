<x-guru-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Berita & Pengumuman Saya</h1>
            <p class="text-gray-600 mt-1">Daftar konten yang Anda tulis untuk website sekolah.</p>
        </div>
        <a href="{{ route('guru.news.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tulis Berita Baru
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Filter Kategori & Stat -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('guru.news.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-semibold {{ ! request('category') ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Semua ({{ $stats['total'] }})
        </a>
        @foreach (\App\Models\News::categories() as $value => $label)
            <a href="{{ route('guru.news.index', ['category' => $value]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-semibold {{ request('category') === $value ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
        <span class="text-xs text-gray-400 ml-auto">Tayang: {{ $stats['published'] }} • Draf: {{ $stats['draft'] }}</span>
    </div>

    <!-- Tabel Berita -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul Berita</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Jurusan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal Buat</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($news as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($item->cover_image)
                                    <img src="{{ $item->cover_url }}" alt="" class="h-10 w-14 object-cover rounded border shrink-0">
                                @else
                                    <div class="h-10 w-14 bg-gray-100 rounded border shrink-0 flex items-center justify-center text-[10px] text-gray-400">No Img</div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 leading-snug">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $item->slug }}</p>
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
                                    default => 'bg-blue-100 text-blue-800',
                                } }}">
                                {{ $item->categoryLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $item->jurusan?->code ?? 'Umum' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $item->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if ($item->status === 'published')
                                <a href="{{ route('news.show', $item->slug) }}" target="_blank"
                                   class="text-xs font-medium text-gray-500 hover:text-gray-800 mr-2">Lihat</a>
                            @endif
                            <a href="{{ route('guru.news.edit', $item) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                            <form action="{{ route('guru.news.destroy', $item) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Hapus berita ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                            Belum ada berita yang Anda tulis.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $news->links() }}
    </div>
</x-guru-layout>
