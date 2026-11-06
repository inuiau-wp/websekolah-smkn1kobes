@php
    // State menu navbar: hover "menyala" (teal) + menu aktif halaman saat ini
    // (aktif ditandai warna aksen + underline, bukan warna saja — aksesibilitas)
    $navLink = fn (bool $active): string => $active
        ? 'flex items-center gap-2 whitespace-nowrap font-medium text-teal-600 underline decoration-teal-500 decoration-2 underline-offset-8 transition-colors'
        : 'flex items-center gap-2 whitespace-nowrap font-medium text-gray-900 hover:text-teal-600 transition-colors';

    $navMobile = fn (bool $active): string => $active
        ? 'flex items-center gap-2.5 px-3 py-2 rounded-md text-base font-semibold bg-teal-50 text-teal-700'
        : 'flex items-center gap-2.5 px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-teal-600';

    $navMobileIcon = fn (bool $active): string => $active ? 'text-teal-600' : 'text-gray-400';
@endphp
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- Full-width: logo & menu menempel di tepi navbar dengan padding 24px -->
    <div class="px-6">
        <div class="flex justify-between items-center h-16 gap-6 xl:gap-10">
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
            <div class="hidden md:flex items-center space-x-5 xl:space-x-6">
                <a href="{{ route('home') }}" class="{{ $navLink(request()->routeIs('home')) }}">
                    <i class="fa-solid fa-house w-4 text-center"></i>Beranda
                </a>

                <a href="{{ route('profil') }}" class="{{ $navLink(request()->routeIs('profil')) }}">
                    <i class="fa-solid fa-school w-4 text-center"></i>Profil Sekolah
                </a>

                <!-- Jurusan dropdown -->
                <div class="relative group">
                    <button class="{{ $navLink(request()->routeIs('jurusan.*')) }}">
                        <i class="fa-solid fa-graduation-cap w-4 text-center"></i>Jurusan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Wrapper nempel ke tombol (pt-2 = jembatan hover, bukan gap) -->
                    <div class="absolute left-0 top-full pt-2">
                        <div class="w-56 bg-white rounded-md shadow-lg border border-gray-200 py-2 hidden group-hover:block group-focus-within:block">
                        @foreach($jurusan as $j)
                            @php $jActive = request()->routeIs('jurusan.show') && request()->route('jurusan')?->slug === $j->slug; @endphp
                            <a href="{{ route('jurusan.show', $j->slug) }}" class="flex items-center gap-2 px-4 py-2 text-sm transition-colors {{ $jActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-teal-600' }}">
                                <i class="fa-solid {{ match ($j->slug) { 'atp' => 'fa-seedling', 'tkj' => 'fa-network-wired', 'akl' => 'fa-calculator', default => 'fa-sitemap' } }} w-4 text-center {{ $jActive ? 'text-teal-600' : 'text-gray-400' }}"></i>{{ $j->code }} - {{ $j->name }}
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('news.index') }}" class="{{ $navLink(request()->routeIs('news.*')) }}">
                    <i class="fa-solid fa-newspaper w-4 text-center"></i>Berita & Pengumuman
                </a>
                <a href="{{ route('gallery.index') }}" class="{{ $navLink(request()->routeIs('gallery.*')) }}">
                    <i class="fa-solid fa-images w-4 text-center"></i>Galeri
                </a>

                @if ($raporYears->isNotEmpty())
                    <!-- Rapor Pendidikan dropdown (tahun rilis) -->
                    <div class="relative group">
                        <button class="{{ $navLink(request()->routeIs('rapor.*')) }}">
                            <i class="fa-solid fa-ranking-star w-4 text-center"></i>Rapor Pendidikan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute left-0 top-full pt-2">
                            <div class="w-56 bg-white rounded-md shadow-lg border border-gray-200 py-2 hidden group-hover:block group-focus-within:block">
                                @php $pActive = request()->routeIs('rapor.perbandingan'); @endphp
                                <a href="{{ route('rapor.perbandingan') }}" class="flex items-center gap-2 px-4 py-2 text-sm transition-colors {{ $pActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-teal-600' }}">
                                    <i class="fa-solid fa-chart-line w-4 text-center {{ $pActive ? 'text-teal-600' : 'text-gray-400' }}"></i>Perbandingan Antar Tahun
                                </a>
                                <div class="border-t border-gray-100 my-2"></div>
                                @foreach ($raporYears as $raporYear)
                                    @php $rActive = request()->routeIs('rapor.show') && request()->route('year') == $raporYear; @endphp
                                    <a href="{{ route('rapor.show', $raporYear) }}" class="flex items-center gap-2 px-4 py-2 text-sm transition-colors {{ $rActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-700 hover:bg-gray-50 hover:text-teal-600' }}">
                                        <i class="fa-solid fa-chart-column w-4 text-center {{ $rActive ? 'text-teal-600' : 'text-gray-400' }}"></i>Tahun Rapor {{ $raporYear }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('contact.index') }}" class="{{ $navLink(request()->routeIs('contact.*')) }}">
                    <i class="fa-solid fa-location-dot w-4 text-center"></i>Kontak & Lokasi
                </a>
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
                    <a href="{{ $userDashboardRoute }}" class="flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"><i class="fa-solid fa-gauge-high w-4 text-center"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-2 whitespace-nowrap font-medium text-sm transition-colors {{ request()->routeIs('login') ? 'text-teal-600' : 'text-gray-900 hover:text-teal-600' }}"><i class="fa-solid fa-right-to-bracket w-4 text-center"></i>Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"><i class="fa-solid fa-user-plus w-4 text-center"></i>Daftar</a>
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
                <a href="{{ route('home') }}" class="{{ $navMobile(request()->routeIs('home')) }}"><i class="fa-solid fa-house w-5 text-center {{ $navMobileIcon(request()->routeIs('home')) }}"></i>Beranda</a>
                <a href="{{ route('profil') }}" class="{{ $navMobile(request()->routeIs('profil')) }}"><i class="fa-solid fa-school w-5 text-center {{ $navMobileIcon(request()->routeIs('profil')) }}"></i>Profil Sekolah</a>
                <div class="relative">
                    <button id="mobile-jurusan-button" class="flex w-full items-center justify-between px-3 py-2 rounded-md text-base {{ $navMobile(request()->routeIs('jurusan.*')) }}">
                        <span class="flex items-center gap-2.5"><i class="fa-solid fa-graduation-cap w-5 text-center {{ $navMobileIcon(request()->routeIs('jurusan.*')) }}"></i>Jurusan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div id="mobile-jurusan-list" class="hidden pl-4 space-y-1">
                        @foreach($jurusan as $j)
                            @php $jActive = request()->routeIs('jurusan.show') && request()->route('jurusan')?->slug === $j->slug; @endphp
                            <a href="{{ route('jurusan.show', $j->slug) }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-sm {{ $jActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-teal-600' }}">
                                <i class="fa-solid {{ match ($j->slug) { 'atp' => 'fa-seedling', 'tkj' => 'fa-network-wired', 'akl' => 'fa-calculator', default => 'fa-sitemap' } }} w-4 text-center {{ $jActive ? 'text-teal-600' : 'text-gray-400' }}"></i>{{ $j->code }} - {{ $j->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('news.index') }}" class="{{ $navMobile(request()->routeIs('news.*')) }}"><i class="fa-solid fa-newspaper w-5 text-center {{ $navMobileIcon(request()->routeIs('news.*')) }}"></i>Berita & Pengumuman</a>
                <a href="{{ route('gallery.index') }}" class="{{ $navMobile(request()->routeIs('gallery.*')) }}"><i class="fa-solid fa-images w-5 text-center {{ $navMobileIcon(request()->routeIs('gallery.*')) }}"></i>Galeri</a>

                @if ($raporYears->isNotEmpty())
                    <div class="relative">
                        <button id="mobile-rapor-button" class="flex w-full items-center justify-between px-3 py-2 rounded-md text-base {{ $navMobile(request()->routeIs('rapor.*')) }}">
                            <span class="flex items-center gap-2.5"><i class="fa-solid fa-ranking-star w-5 text-center {{ $navMobileIcon(request()->routeIs('rapor.*')) }}"></i>Rapor Pendidikan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="mobile-rapor-list" class="hidden pl-4 space-y-1">
                            @php $pActive = request()->routeIs('rapor.perbandingan'); @endphp
                            <a href="{{ route('rapor.perbandingan') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-sm {{ $pActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-teal-600' }}">
                                <i class="fa-solid fa-chart-line w-4 text-center {{ $pActive ? 'text-teal-600' : 'text-gray-400' }}"></i>Perbandingan Antar Tahun
                            </a>
                            @foreach ($raporYears as $raporYear)
                                @php $rActive = request()->routeIs('rapor.show') && request()->route('year') == $raporYear; @endphp
                                <a href="{{ route('rapor.show', $raporYear) }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-sm {{ $rActive ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-teal-600' }}">
                                    <i class="fa-solid fa-chart-column w-4 text-center {{ $rActive ? 'text-teal-600' : 'text-gray-400' }}"></i>Tahun Rapor {{ $raporYear }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ route('contact.index') }}" class="{{ $navMobile(request()->routeIs('contact.*')) }}"><i class="fa-solid fa-location-dot w-5 text-center {{ $navMobileIcon(request()->routeIs('contact.*')) }}"></i>Kontak & Lokasi</a>
                @auth
                    @php
                        $userDashboardRoute = match(auth()->user()->role) {
                            'admin' => route('admin.dashboard'),
                            'guru' => route('guru.dashboard'),
                            'siswa' => route('siswa.dashboard'),
                            default => route('dashboard'),
                        };
                    @endphp
                    <a href="{{ $userDashboardRoute }}" class="{{ $navMobile(false) }}"><i class="fa-solid fa-gauge-high w-5 text-center {{ $navMobileIcon(false) }}"></i>Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="{{ $navMobile(request()->routeIs('login')) }}"><i class="fa-solid fa-right-to-bracket w-5 text-center {{ $navMobileIcon(request()->routeIs('login')) }}"></i>Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="{{ $navMobile(request()->routeIs('register')) }}"><i class="fa-solid fa-user-plus w-5 text-center {{ $navMobileIcon(request()->routeIs('register')) }}"></i>Daftar</a>
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

    // Mobile rapor pendidikan dropdown toggle
    document.getElementById('mobile-rapor-button')?.addEventListener('click', function () {
        document.getElementById('mobile-rapor-list')?.classList.toggle('hidden');
    });
</script>
