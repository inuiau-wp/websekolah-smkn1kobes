<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Admin — SMKN 1 Kotabesi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-gray-900 text-white transform -translate-x-full transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static">
        <div class="flex items-center gap-2 px-5 h-16 border-b border-gray-800">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-gray-900 font-bold text-sm">A</span>
            <div>
                <p class="text-sm font-bold leading-tight">Dashboard Admin</p>
                <p class="text-[10px] text-gray-400 leading-tight">SMKN 1 Kotabesi</p>
            </div>
        </div>

        <nav class="px-3 py-5 space-y-1 text-sm">
            @php
                $adminMenu = [
                    'admin.dashboard' => ['label' => 'Ringkasan', 'url' => route('admin.dashboard'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    'admin.jurusan.*' => ['label' => 'Kelola Jurusan', 'url' => route('admin.jurusan.index'), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    'admin.news.*' => ['label' => 'Kelola Berita', 'url' => route('admin.news.index'), 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6'],
                    'admin.gallery.*' => ['label' => 'Kelola Galeri', 'url' => route('admin.gallery.index'), 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    'admin.rapor-pendidikan.*' => ['label' => 'Rapor Pendidikan', 'url' => route('admin.rapor-pendidikan.index'), 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    'admin.home.*' => ['label' => 'Konten Beranda', 'url' => route('admin.home.index'), 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h4a1 1 0 001-1V10'],
                    'admin.kontak.index' => ['label' => 'Pesan Kontak', 'url' => route('admin.kontak.index'), 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v10a2 2 0 002 2z'],
                    'admin.profil.edit' => ['label' => 'Profil Sekolah', 'url' => route('admin.profil.edit'), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    'admin.users.index' => ['label' => 'Kelola Akun', 'url' => route('admin.users.index'), 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];
            @endphp
            @foreach ($adminMenu as $key => $item)
                <a href="{{ $item['url'] }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs($key) ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach

            @php $adminSoon = []; @endphp

            @if (count($adminSoon) > 0)
                <p class="px-3 pt-4 pb-1 text-[10px] uppercase tracking-wider text-gray-500">Segera tersedia</p>
            @endif
            @foreach ($adminSoon as $item)
                <span class="flex items-center gap-3 px-3 py-2 text-gray-500 cursor-not-allowed">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    {{ $item['label'] }}
                </span>
            @endforeach
        </nav>

        <!-- Kembali ke situs + logout -->
        <div class="border-t border-gray-800 px-3 py-4 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-300 hover:bg-gray-800 hover:text-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div id="admin-sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden lg:hidden"></div>

    <!-- Konten -->
    <div class="flex-1 min-w-0 flex flex-col">
        <!-- Topbar -->
        <header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200 flex items-center gap-4 px-4 sm:px-6">
            <button id="admin-sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
            <div class="flex-1"></div>
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    {{ auth()->user()?->role ?? 'admin' }}
                </span>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()?->name }}</p>
                    <p class="text-[10px] text-gray-400 leading-tight">{{ auth()->user()?->email }}</p>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot ?? '' }}
        </main>
    </div>
</div>

<script>
    // Sidebar toggle mobile
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    document.getElementById('admin-sidebar-toggle')?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>
</body>
</html>
