<x-guru-layout>
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            @if (auth()->user()->photo_url)
                <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
            @else
                <div class="h-16 w-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold shadow">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">Halo, {{ auth()->user()->name }} 👋</h1>
                <p class="text-gray-600 text-sm mt-0.5">
                    {{ auth()->user()->position ?? 'Tenaga Pendidik' }}
                    @if (auth()->user()->nip) • <span class="font-mono text-xs text-gray-500">NIP: {{ auth()->user()->nip }}</span> @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 border border-gray-200 bg-white text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Edit Foto & Profil
            </a>
            <a href="{{ route('guru.news.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tulis Berita
            </a>
        </div>
    </div>

    @if (! auth()->user()->photo)
        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Lengkapi Pas Foto Anda</p>
                    <p class="text-xs text-blue-700">Foto profil guru akan ditampilkan pada struktur pengajar di halaman profil sekolah.</p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="shrink-0 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                Upload Foto Sekarang &rarr;
            </a>
        </div>
    @endif

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Berita Saya</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6"></path></svg>
                </div>
            </div>
            <a href="{{ route('guru.news.index') }}" class="mt-4 block text-xs font-semibold text-blue-600 hover:text-blue-800">
                Kelola semua berita &rarr;
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Berita Tayang (Published)</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $stats['published'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-400">Tampil di beranda & halaman /berita publik</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Draf (Belum Tayang)</p>
                    <p class="mt-2 text-3xl font-bold text-amber-600">{{ $stats['draft'] }}</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-400">Dapat diedit dan dipublikasikan kapan saja</p>
        </div>
    </div>

    <!-- Berita Terbaru Milik Guru -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Berita Terakhir yang Anda Tulis</h2>
            <a href="{{ route('guru.news.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                Lihat Semua &rarr;
            </a>
        </div>

        @if ($recentNews->isEmpty())
            <div class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6"></path></svg>
                <p class="font-medium text-gray-600">Anda belum menulis berita apapun.</p>
                <p class="text-xs mt-1">Mulai tulis pengumuman atau artikel kegiatan sekolah hari ini.</p>
                <a href="{{ route('guru.news.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700">
                    Tulis Berita Pertama
                </a>
            </div>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($recentNews as $item)
                    <div class="p-4 sm:px-6 hover:bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-start gap-4">
                            @if ($item->cover_image)
                                <img src="{{ $item->cover_url }}" alt="" class="h-14 w-20 object-cover rounded-lg shrink-0 border">
                            @else
                                <div class="h-14 w-20 bg-gray-100 rounded-lg shrink-0 flex items-center justify-center text-gray-400 text-xs">No Cover</div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $item->category === 'announcement' ? 'bg-amber-100 text-amber-800' : ($item->category === 'agenda' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $item->category }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $item->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $item->status }}
                                    </span>
                                    @if ($item->jurusan)
                                        <span class="text-xs text-gray-400">• {{ $item->jurusan->code }}</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 leading-snug">{{ $item->title }}</h3>
                                <p class="text-xs text-gray-400 mt-1">{{ $item->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            @if ($item->status === 'published')
                                <a href="{{ route('news.show', $item->slug) }}" target="_blank"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    Lihat
                                </a>
                            @endif
                            <a href="{{ route('guru.news.edit', $item) }}"
                               class="px-3 py-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg">
                                Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-guru-layout>
