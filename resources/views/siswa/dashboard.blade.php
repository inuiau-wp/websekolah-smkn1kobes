<x-siswa-layout>
    <!-- Welcome Banner Siswa -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-800 to-teal-900 text-white p-6 sm:p-8 mb-8 shadow-sm">
        <div class="relative z-10 max-w-3xl">
            <span class="inline-block px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold mb-3">
                Siswa Aktif SMKN 1 Kotabesi
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold leading-tight mb-2">
                Halo, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-emerald-100 text-sm sm:text-base leading-relaxed">
                Selamat datang di Portal Siswa SMKN 1 Kotabesi. Dapatkan informasi terbaru terkait pengumuman sekolah, agenda kegiatan, dan program keahlian di sini.
            </p>
            <div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-emerald-200">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    {{ auth()->user()->email }}
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    SMKN 1 Kotabesi
                </span>
            </div>
        </div>
        <div class="absolute -right-8 -bottom-8 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri & Tengah: Pengumuman & Agenda Terbaru -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Pengumuman & Agenda Sekolah</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Informasi resmi dari pihak sekolah untuk siswa</p>
                </div>
                <a href="{{ route('news.index', ['category' => 'announcement']) }}"
                   class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if ($announcements->isEmpty())
                <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="font-medium text-gray-600">Belum ada pengumuman terbaru.</p>
                    <p class="text-xs mt-1">Pengumuman dan agenda akan tampil di sini saat dipublikasikan pihak sekolah.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($announcements as $ann)
                        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    {{ $ann->category === 'agenda' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $ann->category === 'agenda' ? 'Agenda Kegiatan' : 'Pengumuman' }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $ann->published_at ? $ann->published_at->format('d M Y') : $ann->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <h3 class="font-bold text-gray-900 text-base leading-snug mb-1">
                                <a href="{{ route('news.show', $ann->slug) }}" class="hover:text-emerald-700 transition">
                                    {{ $ann->title }}
                                </a>
                            </h3>
                            @if ($ann->excerpt)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $ann->excerpt }}</p>
                            @endif
                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 text-xs">
                                <span class="text-gray-400">
                                    {{ $ann->jurusan ? 'Jurusan: '.$ann->jurusan->code : 'Umum (Semua Jurusan)' }}
                                </span>
                                <a href="{{ route('news.show', $ann->slug) }}" class="font-semibold text-emerald-700 hover:text-emerald-900">
                                    Baca Selengkapnya &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Program Keahlian / Jurusan -->
            <div class="pt-4">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Program Keahlian di SMKN 1 Kotabesi</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach ($jurusanList as $jur)
                        <a href="{{ route('jurusan.show', $jur->slug) }}"
                           class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:border-emerald-500 hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <span class="inline-block px-2 py-1 rounded text-xs font-bold text-white mb-2"
                                      style="background-color: {{ $jur->color ?? '#0d542b' }}">
                                    {{ $jur->code }}
                                </span>
                                <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $jur->name }}</h3>
                            </div>
                            <span class="mt-3 text-xs font-semibold text-emerald-700">Pelajari kejuruan &rarr;</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Tautan Cepat & Info Kontak -->
        <div class="space-y-6">
            <!-- Tautan Cepat Portal -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-4 text-sm uppercase tracking-wider">Akses Cepat Siswa</h3>
                <div class="space-y-2">
                    <a href="{{ route('news.index') }}"
                       class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 text-sm font-medium transition">
                        <span>📰 Semua Berita & Pengumuman</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('gallery.index') }}"
                       class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 text-sm font-medium transition">
                        <span>📷 Galeri Foto Kegiatan</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('profil') }}"
                       class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 text-sm font-medium transition">
                        <span>🏫 Profil & Visi Misi Sekolah</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('contact.index') }}"
                       class="flex items-center justify-between p-3 rounded-lg bg-gray-50 hover:bg-emerald-50 text-gray-700 hover:text-emerald-900 text-sm font-medium transition">
                        <span>📍 Kontak & Lokasi Sekolah</span>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Bantuan / Kontak Sekolah -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-bold text-gray-900 mb-2">Butuh Bantuan?</h3>
                <p class="text-xs text-gray-600 mb-4">
                    Jika ada pertanyaan terkait materi, jadwal, atau administrasi sekolah, hubungi layanan informasi SMKN 1 Kotabesi.
                </p>
                @if ($profil?->wa_number)
                    <a href="https://wa.me/{{ $profil->wa_number }}?text={{ rawurlencode('Halo SMKN 1 Kotabesi, saya siswa ingin bertanya mengenai:') }}"
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        Hubungi WhatsApp Sekolah
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-siswa-layout>
