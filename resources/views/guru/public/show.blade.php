<x-app-layout>
    <!-- Hero -->
    <section class="relative bg-gray-900 text-white py-14 overflow-hidden">
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #1c398e 0%, #0d542b 60%, #e17100 115%);"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-white/70 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-1">/</span>
                <a href="{{ route('profil') }}" class="hover:text-white">Profil Sekolah</a>
                <span class="mx-1">/</span>
                <span class="text-white font-medium">{{ $guru->name }}</span>
            </nav>

            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                @if ($guru->photo_url)
                    <img src="{{ $guru->photo_url }}" alt="{{ $guru->name }}"
                         class="h-28 w-28 rounded-full object-cover border-4 border-white/25 shadow-lg">
                @else
                    <div class="h-28 w-28 rounded-full bg-white/15 border-4 border-white/25 flex items-center justify-center font-bold text-4xl">
                        {{ strtoupper(substr($guru->name, 0, 1)) }}
                    </div>
                @endif
                <div class="text-center md:text-left">
                    <h1 class="text-3xl md:text-4xl font-bold">{{ $guru->name }}</h1>
                    @if ($guru->position)
                        <p class="text-white/80 mt-1">{{ $guru->position }}</p>
                    @endif
                    @if ($guruProfile->headline)
                        <p class="mt-2 inline-block px-4 py-1 bg-white/15 backdrop-blur rounded-full text-sm font-medium">
                            {{ $guruProfile->headline }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Kolom kiri -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Tentang -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tentang Guru</h2>
                    @if ($guru->bio)
                        <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $guru->bio }}</p>
                    @else
                        <p class="text-gray-400 italic">Profil singkat akan diperbarui oleh guru bersangkutan.</p>
                    @endif
                </div>

                <!-- Prestasi -->
                @if ($guruProfile->prestasiList() !== [])
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Prestasi</h2>
                        <ul class="space-y-3">
                            @foreach ($guruProfile->prestasiList() as $i => $poin)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">{{ $i + 1 }}</span>
                                    <span class="text-gray-600 leading-relaxed">{{ $poin }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Curriculum Vitae (isi guru sendiri) -->
                <div id="cv" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden scroll-mt-24">
                    <div class="px-8 py-5 border-b-3 text-white" style="background: linear-gradient(135deg, #1c398e 0%, #0d542b 100%); border-bottom: 3px solid #e17100;">
                        <h2 class="text-xl font-bold">Curriculum Vitae</h2>
                        <p class="text-white/70 text-sm mt-0.5">Riwayat pendidikan, organisasi &amp; sertifikasi guru.</p>
                    </div>
                    <div class="p-8">
                        @if ($guruProfile->cvLines() !== [])
                            <div class="space-y-2.5">
                                @foreach ($guruProfile->cvLines() as $line)
                                    <p class="text-gray-600 leading-relaxed @if (str_ends_with(trim($line), ':') || intval($line) === 0 && preg_match('/^(Riwayat|Organisasi|Sertifikasi|Pengalaman|Pendidikan|Pelatih|Magang)/i', $line)) font-semibold text-gray-800 @endif">{{ $line }}</p>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic">CV belum diisi oleh guru bersangkutan.</p>
                        @endif
                    </div>
                </div>

                <!-- Artikel / modul guru -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Artikel &amp; Modul</h2>
                    <p class="text-sm text-gray-500 mb-5">Karya tulis, modul, dan bahan ajar yang ditulis guru ini.</p>

                    @if ($articles->isNotEmpty())
                        <div class="grid gap-4">
                            @foreach ($articles as $a)
                                <a href="{{ route('news.show', $a->slug) }}"
                                   class="group flex gap-4 p-4 rounded-xl border border-gray-100 hover:border-gray-300 hover:shadow-sm transition">
                                    @if ($a->cover_image)
                                        <img src="{{ $a->cover_url }}" alt="{{ $a->title }}" class="h-16 w-24 rounded-lg object-cover shrink-0">
                                    @else
                                        <div class="h-16 w-24 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-gray-900 text-sm group-hover:text-gray-600 leading-snug">{{ $a->title }}</h3>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $a->excerpt ?? '' }}</p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ $a->categoryLabel() }}
                                            @if ($a->published_at)
                                                • {{ $a->published_at->translatedFormat('d M Y') }}
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="border border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <p class="text-gray-400 text-sm">Belum ada artikel atau modul yang dipublikasikan.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Kolom kanan -->
            <div class="space-y-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Curriculum Vitae</h3>
                    @if ($guruProfile->cv_content)
                        <p class="text-xs text-gray-500 mb-3">CV sudah dipublikasikan — lihat di bagian utama.</p>
                        <a href="#cv" class="text-sm font-semibold text-blue-600 hover:underline">Buka bagian CV ↓</a>
                    @else
                        <p class="text-sm text-gray-400 italic">CV belum diisi guru.</p>
                    @endif
                </div>

                <!-- Media sosial -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Terhubung</h3>
                    @if (!empty($guru->social_links))
                        <div class="flex flex-wrap gap-3">
                            @foreach ($guru->social_links as $key => $val)
                                @continue(empty($val))
                                @if (is_array($val))
                                    @foreach ($val as $c)
                                        <a href="{{ $c['url'] }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656 0l-4-4a4 4 0 010-5.656l2-2m8 8l2-2m-2-2l-2-2"></path></svg>
                                            {{ $c['label'] }}
                                        </a>
                                    @endforeach
                                @else
                                    <a href="{{ $val }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-sm px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656 0l-4-4a4 4 0 010-5.656l2-2m8 8l2-2m-2-2l-2-2"></path></svg>
                                        <span class="capitalize">{{ $key }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Belum ada tautan media sosial.</p>
                    @endif
                </div>

                <!-- CTA balik -->
                <div class="rounded-xl p-6 bg-gray-900 text-white text-center">
                    <p class="text-sm text-white/80 mb-4">Lihat seluruh dewan guru &amp; profil sekolah.</p>
                    <a href="{{ route('profil') }}"
                       class="inline-flex items-center gap-2 bg-white text-gray-900 px-5 py-2.5 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Profil Sekolah
                    </a>
                </div>
            </div>

        </div>
    </section>
</x-app-layout>
