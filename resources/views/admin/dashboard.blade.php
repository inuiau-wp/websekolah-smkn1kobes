<x-admin-layout>
    <!-- Judul -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ringkasan</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}! Berikut kondisi konten website.</p>
    </div>

    <!-- Kartu statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6"></path></svg>
                </span>
                <span class="text-xs text-gray-400">{{ $beritaDraf }} draft</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalBerita }}</p>
            <p class="text-sm text-gray-500 mt-1">Berita & Pengumuman</p>
            <p class="mt-3 text-xs text-gray-400">(Menu kelola hadir di Fase 3)</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalGaleri }}</p>
            <p class="text-sm text-gray-500 mt-1">Foto Galeri</p>
            <p class="mt-3 text-xs text-gray-400">(Menu kelola hadir di Fase 4)</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </span>
                <span class="text-xs font-semibold text-red-600">{{ $pesanBelumDibaca }} belum dibaca</span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalPesan }}</p>
            <p class="text-sm text-gray-500 mt-1">Pesan Kontak</p>
            <p class="mt-3 text-xs text-gray-400">(Menu daftar pesan hadir di Fase 6)</p>
        </div>
    </div>

    <!-- Blok fokus -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Fokus berikutnya</h2>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex gap-2"><span class="font-semibold text-gray-900 shrink-0">Fase 2</span> Kelola Jurusan (CRUD)</li>
                <li class="flex gap-2"><span class="font-semibold text-gray-900 shrink-0">Fase 3</span> Kelola Berita + upload cover</li>
                <li class="flex gap-2"><span class="font-semibold text-gray-900 shrink-0">Fase 5</span> Konten beranda (carousel, statistik, testimoni, partner) berpindah ke database</li>
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-2">Jejak modul aktif</h2>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700">Jurusan: {{ $totalJurusan }} terdaftar</span>
                <span class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700">Berita: {{ $totalBerita }}</span>
                <span class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700">Galeri: {{ $totalGaleri }}</span>
                <span class="px-3 py-1.5 rounded-lg text-sm font-medium bg-gray-100 text-gray-700">Pesan: {{ $totalPesan }}</span>
            </div>
        </div>
    </div>
</x-admin-layout>
