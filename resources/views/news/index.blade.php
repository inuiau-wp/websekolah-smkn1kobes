<x-app-layout>
    <!-- Header halaman -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">Berita & Pengumuman</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Berita & Pengumuman</h1>
            <p class="text-gray-600 mt-1">Informasi, agenda, dan kegiatan terbaru dari sekolah</p>
        </div>
    </header>

    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Filter kategori -->
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach (array_merge(['' => 'Semua'], \App\Models\News::categories())
                    as $value => $label)
                    <a href="{{ $value === '' ? route('news.index') : route('news.index', ['category' => $value]) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors
                              {{ ($category ?? '') === $value
                                  ? 'bg-gray-900 text-white'
                                  : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($news->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada konten pada kategori ini.</p>
                    <p class="text-gray-400 text-sm mt-1">(Konten akan dimasukkan melalui dashboard admin)</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($news as $item)
                        <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-all flex flex-col">
                            <!-- Gambar cover -->
                            @if ($item->cover_image)
                                <a href="{{ route('news.show', $item->slug) }}">
                                    <img src="{{ $item->cover_url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                </a>
                            @else
                                <a href="{{ route('news.show', $item->slug) }}"
                                   class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <div class="text-center px-4">
                                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6"></path></svg>
                                        <p class="text-xs text-gray-400">Tanpa gambar<br>(placeholder)</p>
                                    </div>
                                </a>
                            @endif

                            <div class="p-6 flex flex-col flex-1">
                                <!-- Badge kategori + jurusan -->
                                <div class="flex flex-wrap gap-2 mb-3">
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
                                    @if ($item->jurusan)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold text-white"
                                              style="background-color: {{ $item->jurusan->color }}">
                                            {{ $item->jurusan->code }}
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-lg font-bold text-gray-900 mb-2 leading-snug">
                                    <a href="{{ route('news.show', $item->slug) }}" class="hover:text-gray-700">
                                        {{ $item->title }}
                                    </a>
                                </h2>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-1">
                                    {{ $item->excerpt ?? 'Ringkasan akan diperbarui oleh admin.' }}
                                </p>

                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <span>
                                        {{ $item->published_at ? $item->published_at->translatedFormat('d F Y') : 'Belum tayang' }}
                                    </span>
                                    <a href="{{ route('news.show', $item->slug) }}" class="font-medium text-gray-900 hover:text-gray-600 flex items-center gap-1">
                                        Baca
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $news->links() }}
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
