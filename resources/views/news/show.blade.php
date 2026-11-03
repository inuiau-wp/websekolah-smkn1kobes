<x-app-layout>
    <section class="py-12 lg:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-6">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Beranda</a>
                <span class="mx-1">/</span>
                <a href="{{ route('news.index') }}" class="hover:text-gray-900">Berita & Pengumuman</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">{{ Str::limit($news->title, 30) }}</span>
            </nav>

            <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Cover -->
                @if ($news->cover_image)
                    <img src="{{ $news->cover_url }}" alt="{{ $news->title }}" class="w-full h-64 md:h-80 object-cover">
                @endif

                <div class="p-8 lg:p-10">
                    <!-- Badge kategori + jurusan -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                                     {{ match ($news->category) {
                                         'announcement' => 'bg-amber-100 text-amber-800',
                                         'agenda' => 'bg-purple-100 text-purple-800',
                                         'materi' => 'bg-indigo-100 text-indigo-800',
                                         'tutorial' => 'bg-cyan-100 text-cyan-800',
                                         default => 'bg-emerald-100 text-emerald-800',
                                     } }}">
                            {{ $news->categoryLabel() }}
                        </span>
                        @if ($news->jurusan)
                            <a href="{{ route('jurusan.show', $news->jurusan->slug) }}"
                               class="px-3 py-1 rounded-full text-sm font-semibold text-white hover:opacity-90"
                               style="background-color: {{ $news->jurusan->color }}">
                                {{ $news->jurusan->code }} - {{ $news->jurusan->name }}
                            </a>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $news->title }}</h1>

                    <!-- Meta -->
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 border-b border-gray-100 pb-6 mb-6">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $news->published_at ? $news->published_at->translatedFormat('d F Y') : '-' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $news->author?->name ?? 'Admin Sekolah' }}
                        </span>
                    </div>

                    <!-- Konten -->
                    @if ($news->excerpt)
                        <p class="text-gray-700 leading-relaxed font-medium mb-4">{{ $news->excerpt }}</p>
                    @endif

                    @if ($news->file_type === 'pdf')
                        <!-- Lampiran PDF: tampil ter-embed -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Dokumen PDF</h2>
                                <a href="{{ asset('storage/'.$news->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                    Unduh PDF
                                </a>
                            </div>
                            <iframe src="{{ asset('storage/'.$news->file_path) }}#view=FitH"
                                    title="{{ $news->file_original_name }}"
                                    class="w-full h-[80vh] rounded-xl border border-gray-200"></iframe>
                        </div>
                    @elseif ($news->file_type === 'docx')
                        <!-- Lampiran DOCX: tombol unduh -->
                        <div class="mb-8 flex items-center gap-4 p-5 rounded-xl border border-blue-100 bg-blue-50">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-xs">DOCX</div>
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm truncate">{{ $news->file_original_name }}</p>
                                <p class="text-xs text-gray-500">Dokumen siap diunduh (Word)</p>
                            </div>
                            <a href="{{ asset('storage/'.$news->file_path) }}" download
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                                Unduh DOCX
                            </a>
                        </div>
                    @endif

                    @if ($news->content)
                        {{-- Rich text editor (TinyMCE) menghasilkan HTML; konten dibersihkan saat disimpan --}}
                        <div class="news-content">{!! $news->content !!}</div>

                        @if (str_contains($news->content, 'instagram-media'))
                            {{-- Embed post Instagram di-render oleh embed.js resmi --}}
                            <script async src="https://www.instagram.com/embed.js"></script>
                        @endif
                    @else
                        <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <p class="text-gray-400 text-sm">Konten lengkap belum diisi.</p>
                            <p class="text-gray-400 text-xs mt-1">(Konten akan dimasukkan melalui dashboard admin)</p>
                        </div>
                    @endif

                    <!-- Bagikan -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex gap-3">
                            <a href="https://wa.me/?text={{ rawurlencode($news->title.' - '.route('news.show', $news->slug)) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                                Bagikan via WhatsApp
                            </a>
                            <a href="{{ route('news.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                ← Kembali ke daftar
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Berita terkait -->
            @if ($related->isNotEmpty())
                <div class="mt-12">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Berita Terkait</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($related as $item)
                            <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-all">
                                @if ($item->cover_image)
                                    <a href="{{ route('news.show', $item->slug) }}">
                                        <img src="{{ $item->cover_url }}" alt="{{ $item->title }}" class="w-full h-36 object-cover">
                                    </a>
                                @else
                                    <a href="{{ route('news.show', $item->slug) }}" class="block w-full h-36 bg-gradient-to-br from-gray-100 to-gray-200"></a>
                                @endif
                                <div class="p-5">
                                    <h3 class="text-sm font-bold text-gray-900 mb-2 leading-snug">
                                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-gray-700">{{ $item->title }}</a>
                                    </h3>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->published_at ? $item->published_at->translatedFormat('d F Y') : 'Belum tayang' }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
