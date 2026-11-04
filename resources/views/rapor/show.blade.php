<x-app-layout>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <nav class="text-sm text-white/70 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-white font-medium">Rapor Pendidikan {{ $rapor->year }}</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                    Data Resmi Rapor Pendidikan {{ $rapor->year }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-white/90">
                    Agregasi Level 2 (Institusi)
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-white/90">
                    NPSN 30205228
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
                Transparansi Mutu &amp; Relevansi Vokasi {{ $schoolProfile->school_name }}
            </h1>
            <p class="text-white/85 max-w-3xl leading-relaxed">
                Disajikan sesuai amanat UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik (UU KIP)
                dan UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).
            </p>
        </div>
    </section>

    <!-- Kata Pengantar & Komitmen -->
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h2 class="text-xl font-bold text-gray-900 mb-3">Kata Pengantar &amp; Komitmen Transparansi</h2>
            <p class="text-gray-600 leading-relaxed max-w-4xl">{{ $rapor->intro }}</p>
        </div>
    </section>

    <!-- KPI Highlight Cards -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-0 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($rapor->data['kpi'] ?? [] as $kpi)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-medium text-gray-500">{{ $kpi['label'] }}</p>
                        <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ \App\Models\RaporPendidikan::kategoriColor($kpi['kategori'] ?? null) }}">
                            {{ $kpi['kategori'] }}
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-gray-900" title="Skor agregasi institusi (Level 2)">
                        {{ number_format((float) $kpi['value'], 2, ',', '.') }}{{ $kpi['suffix'] ?? '' }}
                    </p>
                    @if (! empty($kpi['tren']))
                        <p class="mt-2 text-sm">
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                {{ $kpi['tren'] }}
                            </span>
                            <span class="text-gray-500">{{ $kpi['tren_note'] }}</span>
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- Tabs: Dimensi Capaian -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" x-data="{ tab: 'akademik' }">
        <div class="flex flex-wrap gap-2 mb-6" role="tablist">
            <button @click="tab = 'akademik'" :class="tab === 'akademik' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Mutu Akademik</button>
            <button @click="tab = 'vokasi'" :class="tab === 'vokasi' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Vokasi &amp; Industri</button>
            <button @click="tab = 'kelola'" :class="tab === 'kelola' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Iklim Sekolah &amp; Tata Kelola</button>
        </div>

        @foreach (['akademik', 'vokasi', 'kelola'] as $tabKey)
            <div x-show="tab === '{{ $tabKey }}'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($rapor->data['sections'][$tabKey] ?? [] as $item)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h3 class="font-bold text-gray-900">{{ $item['title'] }}</h3>
                            <span class="shrink-0 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ \App\Models\RaporPendidikan::kategoriColor($item['kategori'] ?? null) }}">
                                {{ $item['kategori'] }}
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-teal-700 mb-2">{{ $item['skor'] }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $item['catatan'] }}</p>
                        @if (! empty($item['poin']))
                            <ul class="mt-3 space-y-1.5 text-sm text-gray-600 list-disc list-inside">
                                @foreach ($item['poin'] as $poin)
                                    <li>{{ $poin }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500">Belum ada data untuk dimensi ini.</p>
                @endforelse
            </div>
        @endforeach
    </section>

    <!-- Visualisasi Grafik -->
    <section class="bg-white border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Visualisasi Data</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Bar chart: tren 2024 vs 2025 -->
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Perbandingan Capaian {{ $rapor->data['charts']['bar']['label_sebelum'] ?? '2024' }} vs {{ $rapor->data['charts']['bar']['label_sesudah'] ?? $rapor->year }}</h3>
                    <!-- Responsive container (wajib agar canvas Chart.js terkontrol) -->
                    <div class="relative h-72">
                        <canvas id="chart-bar"></canvas>
                    </div>
                </div>

                <!-- Radar chart: relevansi vokasi -->
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Relevansi Vokasi &amp; Dunia Kerja</h3>
                    <div class="relative h-72">
                        <canvas id="chart-radar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tabel Indikator (pencarian interaktif) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ q: '' }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h2 class="text-xl font-bold text-gray-900">Katalog Indikator yang Dipublikasikan</h2>
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="search" x-model="q" placeholder="Cari indikator..." class="pl-9 pr-4 py-2 w-full sm:w-64 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none">
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Capaian {{ $rapor->year }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Narasi Tindak Lanjut RKT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rapor->data['indicators'] ?? [] as $ind)
                        <tr x-show="q === '' || (('{{ $ind['kode'] }} {{ $ind['indikator'] }} {{ $ind['status'] }}').toLowerCase().includes(q.toLowerCase()))" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-gray-500 whitespace-nowrap">{{ $ind['kode'] }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $ind['indikator'] }}</td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $ind['capaian'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ \App\Models\RaporPendidikan::kategoriColor($ind['status'] ?? null) }}">
                                    {{ $ind['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $ind['narasi'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada indikator.</td></tr>
                    @endforelse
                    <tr x-show="q !== '' && false" hidden></tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Disclaimer -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h3 class="font-bold text-amber-900 mb-1">Penafian Hukum (Disclaimer)</h3>
                    <p class="text-sm text-amber-800 leading-relaxed">{{ $rapor->disclaimer }}</p>
                    <p class="text-sm text-amber-800 mt-3">
                        Permohonan informasi lebih lanjut dapat diajukan melalui PPID {{ $schoolProfile->school_name }} pada halaman
                        <a href="{{ route('contact.index') }}" class="font-semibold underline hover:text-amber-900">Kontak &amp; Lokasi</a>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Chart === 'undefined') { return; }

                const bar = @json($rapor->data['charts']['bar'] ?? []);
                const radar = @json($rapor->data['charts']['radar'] ?? []);

                if (bar.labels) {
                    new Chart(document.getElementById('chart-bar'), {
                        type: 'bar',
                        data: {
                            labels: bar.labels,
                            datasets: [
                                {
                                    label: bar.label_sebelum,
                                    data: bar.sebelum,
                                    backgroundColor: 'rgba(148, 163, 184, 0.7)',
                                },
                                {
                                    label: bar.label_sesudah,
                                    data: bar.sesudah,
                                    backgroundColor: 'rgba(13, 148, 136, 0.8)',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, max: 100 } },
                        },
                    });
                }

                if (radar.labels) {
                    new Chart(document.getElementById('chart-radar'), {
                        type: 'radar',
                        data: {
                            labels: radar.labels,
                            datasets: [{
                                label: 'Skor Relevansi Vokasi',
                                data: radar.values,
                                backgroundColor: 'rgba(13, 148, 136, 0.2)',
                                borderColor: 'rgb(13, 148, 136)',
                                pointBackgroundColor: 'rgb(13, 148, 136)',
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { r: { beginAtZero: true, max: 100 } },
                        },
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
