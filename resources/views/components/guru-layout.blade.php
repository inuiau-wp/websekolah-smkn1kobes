<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dashboard Guru — SMKN 1 Kotabesi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
<div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="guru-sidebar"
           class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 text-white transform -translate-x-full transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static">
        <div class="flex items-center gap-2 px-5 h-16 border-b border-slate-800">
            @if ($schoolProfile?->logo_url)
                <img src="{{ $schoolProfile->logo_url }}" alt="Logo" class="h-8 w-8 object-contain rounded">
            @else
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-sm">G</span>
            @endif
            <div>
                <p class="text-sm font-bold leading-tight">Dashboard Guru</p>
                <p class="text-[10px] text-slate-400 leading-tight">SMKN 1 Kotabesi</p>
            </div>
        </div>

        <nav class="px-3 py-5 space-y-1 text-sm">
            @php
                $managedJurusan = auth()->user()?->managedJurusan();
                $guruMenu = [
                    'guru.dashboard' => ['label' => 'Ringkasan', 'url' => route('guru.dashboard'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    'guru.news.*' => ['label' => 'Berita Saya', 'url' => route('guru.news.index'), 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6'],
                    'guru.gallery.*' => ['label' => 'Kelola Galeri', 'url' => route('guru.gallery.index'), 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    'guru.pribadi.*' => ['label' => 'Halaman Pribadi', 'url' => route('guru.pribadi.edit'), 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                if ($managedJurusan) {
                    $guruMenu['guru.jurusan.index'] = ['label' => 'Program Keahlian Saya', 'url' => route('guru.jurusan.index'), 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'];
                }
                $guruMenu['profile.edit'] = ['label' => 'Profil & Foto', 'url' => route('profile.edit'), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'];
            @endphp
            @foreach ($guruMenu as $key => $item)
                <a href="{{ $item['url'] }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs($key) ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="pt-4 border-t border-slate-800 mt-4">
                <a href="{{ route('guru.news.create') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium bg-slate-800 text-blue-300 hover:bg-slate-700 hover:text-white transition-colors">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tulis Berita Baru
                </a>
            </div>
        </nav>

        <!-- Kembali ke situs + logout -->
        <div class="border-t border-slate-800 px-3 py-4 space-y-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-300 hover:bg-slate-800 hover:text-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div id="guru-sidebar-overlay" class="fixed inset-0 z-30 bg-black/50 hidden lg:hidden"></div>

    <!-- Konten -->
    <div class="flex-1 min-w-0 flex flex-col">
        <!-- Topbar -->
        <header class="sticky top-0 z-20 h-16 bg-white border-b border-gray-200 flex items-center gap-4 px-4 sm:px-6">
            <button id="guru-sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
            <div class="flex-1"></div>
            <div class="flex items-center gap-3">
                @if (auth()->user()?->position)
                    <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        {{ auth()->user()->position }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Guru
                    </span>
                @endif
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()?->name }}</p>
                    <p class="text-[10px] text-gray-400 leading-tight">{{ auth()->user()?->email }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="shrink-0" title="Kelola Profil & Foto">
                    @if (auth()->user()?->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-white font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()?->name ?? 'G', 0, 1)) }}
                        </div>
                    @endif
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{ $slot ?? '' }}
        </main>
    </div>
</div>

<script>
    const sidebar = document.getElementById('guru-sidebar');
    const overlay = document.getElementById('guru-sidebar-overlay');
    document.getElementById('guru-sidebar-toggle')?.addEventListener('click', () => {
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
