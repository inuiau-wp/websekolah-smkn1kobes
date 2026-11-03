<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if ($schoolProfile->logo_url)
                        <img src="{{ $schoolProfile->logo_url }}" alt="{{ $schoolProfile->school_name }}" class="h-10 w-auto object-contain">
                    @else
                        <img src="{{ asset('images/logo-sekolah.svg') }}" alt="{{ $schoolProfile->school_name }}" class="h-10 w-auto" onerror="this.style.display='none'">
                    @endif
                    <span class="font-bold text-xl text-gray-900 tracking-tight">{{ $schoolProfile->school_name }}</span>
                </a>
            </div>

            <!-- Desktop menu -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-900 hover:text-gray-600 font-medium transition-colors">Beranda</a>

                <a href="{{ route('profil') }}" class="text-gray-900 hover:text-gray-600 font-medium transition-colors">Profil Sekolah</a>

                <!-- Jurusan dropdown -->
                <div class="relative group">
                    <button class="text-gray-900 hover:text-gray-600 font-medium flex items-center gap-1 transition-colors">
                        Jurusan
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Wrapper nempel ke tombol (pt-2 = jembatan hover, bukan gap) -->
                    <div class="absolute left-0 top-full pt-2">
                        <div class="w-56 bg-white rounded-md shadow-lg border border-gray-200 py-2 hidden group-hover:block group-focus-within:block">
                        @foreach($jurusan as $j)
                            <a href="{{ route('jurusan.show', $j->slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                {{ $j->code }} - {{ $j->name }}
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('news.index') }}" class="text-gray-900 hover:text-gray-600 font-medium transition-colors">Berita & Pengumuman</a>
                <a href="{{ route('gallery.index') }}" class="text-gray-900 hover:text-gray-600 font-medium transition-colors">Galeri</a>
                <a href="{{ route('contact.index') }}" class="text-gray-900 hover:text-gray-600 font-medium transition-colors">Kontak & Lokasi</a>
            </div>

            <!-- Right side: Auth -->
            <div class="flex items-center gap-4">
                @auth
                    @php
                        $userDashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'guru' => route('guru.dashboard'),
                            'siswa' => route('siswa.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $userDashboardRoute }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-900 hover:text-gray-600 font-medium text-sm transition-colors">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">Daftar</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden border-t border-gray-200 bg-white">
        <div class="flex items-center justify-between px-4 py-3">
            <button id="mobile-menu-button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
        <div id="mobile-menu" class="hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Beranda</a>
                <a href="{{ route('profil') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Profil Sekolah</a>
                <div class="relative">
                    <button id="mobile-jurusan-button" class="flex w-full items-center justify-between px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">
                        Jurusan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="mobile-jurusan-list" class="hidden pl-4 space-y-1">
                        @foreach($jurusan as $j)
                            <a href="{{ route('jurusan.show', $j->slug) }}" class="block px-3 py-1.5 rounded-md text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                {{ $j->code }} - {{ $j->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Berita & Pengumuman</a>
                <a href="{{ route('gallery.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Galeri</a>
                <a href="{{ route('contact.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Kontak & Lokasi</a>
                @auth
                    @php
                        $userDashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'guru' => route('guru.dashboard'),
                            'siswa' => route('siswa.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $userDashboardRoute }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900">Daftar</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button')?.addEventListener('click', function () {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });

    // Mobile jurusan dropdown toggle
    document.getElementById('mobile-jurusan-button')?.addEventListener('click', function () {
        document.getElementById('mobile-jurusan-list')?.classList.toggle('hidden');
    });
</script>
