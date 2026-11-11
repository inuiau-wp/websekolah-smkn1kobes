<x-admin-layout>
    @php
        $today = now()->translatedFormat('l, d F Y');
        $adaPerhatian = $pesanBelumDibaca > 0 || $beritaDraf > 0 || ($raporTotal - $raporDipublikasikan) > 0;
    @endphp

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ringkasan</h1>
        <p class="text-gray-600 mt-1">Selamat datang, {{ auth()->user()->name }}. {{ $today }}.</p>
    </div>

    {{-- Perlu perhatian: hanya tampil kalau benar-benar ada isinya --}}
    @if ($adaPerhatian)
        <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-semibold text-amber-900 mb-2">Perlu perhatian</p>
            <ul class="flex flex-wrap gap-2 text-sm">
                @if ($pesanBelumDibaca > 0)
                    <li>
                        <a href="{{ route('admin.kontak.index') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-amber-900 font-medium hover:bg-amber-100 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-red-500" aria-hidden="true"></span>
                            {{ $pesanBelumDibaca }} pesan belum dibaca
                        </a>
                    </li>
                @endif
                @if ($beritaDraf > 0)
                    <li>
                        <a href="{{ route('admin.news.index') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-amber-900 font-medium hover:bg-amber-100 transition-colors">
                            {{ $beritaDraf }} berita masih draf
                        </a>
                    </li>
                @endif
                @if ($raporTotal - $raporDipublikasikan > 0)
                    <li>
                        <a href="{{ route('admin.rapor-pendidikan.index') }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-amber-900 font-medium hover:bg-amber-100 transition-colors">
                            {{ $raporTotal - $raporDipublikasikan }} tahun rapor belum dipublikasikan
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    @endif

    {{-- Kartu statistik: seluruh kartu bertaut ke modulnya --}}
    @php
        // Angka kartu: count-up halus 400ms sekali saat halaman dibuka; reduced-motion langsung angka akhir.
        $countUp = '{
            count: 0,
            target: TARGET,
            start() {
                if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                    this.count = this.target;
                    return;
                }
                const t0 = performance.now();
                const dur = 400;
                const step = (t) => {
                    const p = Math.min((t - t0) / dur, 1);
                    this.count = Math.round(this.target * (1 - Math.pow(1 - p, 3)));
                    if (p < 1) requestAnimationFrame(step);
                };
                requestAnimationFrame(step);
            }
        }';
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.news.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13h-2a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m0 13h5a1 1 0 001-1v-5a1 1 0 00-1-1h-5m0 6v-6"></path></svg>
                </span>
                @if ($beritaDraf > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900">{{ $beritaDraf }} draf</span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums"
               x-data="{{ str_replace('TARGET', (string) $totalBerita, $countUp) }}"
               x-init="start()"
               x-text="count.toLocaleString('id-ID')">{{ $totalBerita }}</p>
            <p class="text-sm text-gray-500 mt-1">Berita &amp; Pengumuman</p>
        </a>

        <a href="{{ route('admin.gallery.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums"
               x-data="{{ str_replace('TARGET', (string) $totalGaleri, $countUp) }}"
               x-init="start()"
               x-text="count.toLocaleString('id-ID')">{{ $totalGaleri }}</p>
            <p class="text-sm text-gray-500 mt-1">Foto Galeri</p>
        </a>

        <a href="{{ route('admin.kontak.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </span>
                @if ($pesanBelumDibaca > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">{{ $pesanBelumDibaca }} belum dibaca</span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums"
               x-data="{{ str_replace('TARGET', (string) $totalPesan, $countUp) }}"
               x-init="start()"
               x-text="count.toLocaleString('id-ID')">{{ $totalPesan }}</p>
            <p class="text-sm text-gray-500 mt-1">Pesan Kontak</p>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums"
               x-data="{{ str_replace('TARGET', (string) ($guruCount + $siswaCount), $countUp) }}"
               x-init="start()"
               x-text="count.toLocaleString('id-ID')">{{ $guruCount + $siswaCount }}</p>
            <p class="text-sm text-gray-500 mt-1">Akun terdaftar</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $guruCount }} guru · {{ $siswaCount }} siswa</p>
        </a>

        <a href="{{ route('admin.jurusan.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums">{{ $totalJurusan }}</p>
            <p class="text-sm text-gray-500 mt-1">Jurusan</p>
            <p class="text-xs text-gray-400 mt-0.5">ATP · TKJ · AKL</p>
        </a>

        <a href="{{ route('admin.rapor-pendidikan.index') }}"
           class="block bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:border-teal-300 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </span>
                @if ($raporTotal - $raporDipublikasikan > 0)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-900">{{ $raporTotal - $raporDipublikasikan }} draf</span>
                @endif
            </div>
            <p class="text-3xl font-bold text-gray-900 tabular-nums">{{ $raporDipublikasikan }}<span class="text-lg text-gray-400 font-semibold"> / {{ $raporTotal }}</span></p>
            <p class="text-sm text-gray-500 mt-1">Rapor Pendidikan terpublikasi</p>
        </a>
    </div>

    {{-- Aktivitas terbaru --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Berita terbaru</h2>
                <a href="{{ route('admin.news.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800 hover:underline underline-offset-4">Semua berita</a>
            </div>
            @forelse ($beritaTerbaru as $n)
                <a href="{{ route('admin.news.edit', $n->id) }}"
                   class="flex items-start justify-between gap-3 py-2.5 border-b border-gray-100 last:border-0 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $n->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $n->author?->name ?? 'Tanpa penulis' }} · {{ $n->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $n->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ $n->status === 'published' ? 'Terbit' : 'Draf' }}
                    </span>
                </a>
            @empty
                <p class="text-sm text-gray-400">Belum ada berita.</p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Pesan terbaru</h2>
                <a href="{{ route('admin.kontak.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800 hover:underline underline-offset-4">Semua pesan</a>
            </div>
            @forelse ($pesanTerbaru as $m)
                <a href="{{ route('admin.kontak.index') }}"
                   class="flex items-start justify-between gap-3 py-2.5 border-b border-gray-100 last:border-0 hover:bg-gray-50 rounded-lg px-2 -mx-2 transition-colors">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">{{ $m->subject }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $m->name }} · {{ $m->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $m->is_read ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-700' }}">
                        {{ $m->is_read ? 'Dibaca' : 'Baru' }}
                    </span>
                </a>
            @empty
                <p class="text-sm text-gray-400">Belum ada pesan.</p>
            @endforelse
        </div>
    </div>

    {{-- Konten beranda + grafik kategori berita --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Konten beranda</h2>
                <a href="{{ route('admin.home.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-800 hover:underline underline-offset-4">Kelola</a>
            </div>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                    <dt class="text-gray-500 text-xs">Slide carousel</dt>
                    <dd class="text-xl font-bold text-gray-900 mt-1 tabular-nums">{{ $slideCount }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                    <dt class="text-gray-500 text-xs">Statistik</dt>
                    <dd class="text-xl font-bold text-gray-900 mt-1 tabular-nums">{{ $statistikCount }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                    <dt class="text-gray-500 text-xs">Testimoni</dt>
                    <dd class="text-xl font-bold text-gray-900 mt-1 tabular-nums">{{ $testimoniCount }}</dd>
                </div>
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-3">
                    <dt class="text-gray-500 text-xs">Mitra</dt>
                    <dd class="text-xl font-bold text-gray-900 mt-1 tabular-nums">{{ $partnerCount }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Berita per kategori</h2>
            <div class="relative h-56">
                <canvas id="chart-kategori" role="img"
                        aria-label="Grafik batang jumlah berita per kategori"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Chart) return;
            const el = document.getElementById('chart-kategori');
            if (!el) return;

            const labels = @json($beritaKategoriLabels->values());
            const data = @json($beritaKategoriData);

            // Data yang dibaca: tanpa animasi enter.
            new Chart(el, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: 'rgba(13, 148, 136, 0.75)',
                        hoverBackgroundColor: 'rgba(13, 148, 136, 1)',
                        borderRadius: 6,
                        maxBarThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { rtl: false },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(0,0,0,0.05)' },
                        },
                        x: { grid: { display: false } },
                    },
                }
            });
        });
    </script>
</x-admin-layout>
