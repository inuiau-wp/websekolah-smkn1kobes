<x-app-layout>
    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-teal-700 via-teal-600 to-emerald-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <nav class="text-sm text-white/70 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-white font-medium">Perbandingan Rapor Pendidikan</span>
            </nav>

            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white">
                    Tren Antar Tahun
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-white/90">
                    {{ count($years) }} tahun terdata
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold mb-4 leading-tight">
                Perbandingan Rapor Pendidikan Antar Tahun
            </h1>
            <p class="text-white/85 max-w-3xl leading-relaxed">
                Grafik garis di bawah menampilkan tren capaian dari tahun {{ $years[0] ?? '-' }} hingga
                {{ $years[count($years) - 1] ?? '-' }}. Data bersumber dari setiap laporan Rapor Pendidikan yang
                dipublikasikan dan bertambah otomatis setiap tahun rilis baru.
            </p>
        </div>
    </section>

    @if ($years === [])
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-10 text-center">
                <i class="fa-solid fa-chart-line text-4xl text-gray-300 mb-4"></i>
                <h2 class="text-lg font-bold text-gray-900 mb-2">Belum ada data Rapor Pendidikan</h2>
                <p class="text-gray-500 text-sm">Halaman perbandingan akan terisi otomatis begitu ada laporan tahun yang dipublikasikan.</p>
            </div>
        </section>
    @else
        <!-- Tabel Ringkasan KPI -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Ringkasan KPI Utama</h2>
            <p class="text-sm text-gray-500 mb-6">Nilai capaian per tahun rilis. Selisih dihitung dari dua tahun terakhir yang memiliki data.</p>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                            @foreach ($years as $year)
                                <th class="px-4 py-3 text-right font-semibold text-gray-700 whitespace-nowrap">{{ $year }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right font-semibold text-gray-700">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($kpiSeries as $serie)
                            @php
                                $terisi = array_values(array_filter($serie['values'], fn ($v) => $v !== null));
                                $delta = count($terisi) >= 2 ? round(end($terisi) - $terisi[count($terisi) - 2], 2) : null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $serie['label'] }}</td>
                                @foreach ($serie['values'] as $nilai)
                                    <td class="px-4 py-3 text-right text-gray-700 whitespace-nowrap">
                                        {{ $nilai === null ? '—' : number_format($nilai, 2, ',', '.').$serie['suffix'] }}
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-right font-semibold whitespace-nowrap {{ $delta === null ? 'text-gray-400' : ($delta < 0 ? 'text-rose-600' : 'text-emerald-600') }}">
                                    @if ($delta === null)
                                        —
                                    @else
                                        {{ ($delta >= 0 ? '+' : '').number_format($delta, 2, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ count($years) + 2 }}" class="px-4 py-6 text-center text-gray-500">Belum ada KPI yang dipublikasikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Grafik Garis -->
        <section class="bg-white border-y border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Grafik Tren Antar Tahun</h2>

                <!-- Grafik KPI Utama -->
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">KPI Utama</h3>
                    <div class="relative h-80">
                        <canvas id="chart-kpi"></canvas>
                    </div>
                </div>

                <!-- Grafik per dimensi -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach (['akademik' => 'Mutu Akademik', 'vokasi' => 'Vokasi & Industri', 'kelola' => 'Iklim Sekolah & Tata Kelola'] as $dimensi => $judul)
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 {{ $loop->last ? 'lg:col-span-2' : '' }}">
                            <h3 class="font-semibold text-gray-900 mb-4">{{ $judul }}</h3>
                            <div class="relative h-72">
                                <canvas id="chart-{{ $dimensi }}"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Tautan per tahun -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Lihat Laporan Lengkap per Tahun</h2>
            <div class="flex flex-wrap gap-3">
                @foreach ($rapors as $rapor)
                    <a href="{{ route('rapor.show', $rapor->year) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-teal-200 bg-teal-50 text-sm font-semibold text-teal-800 hover:bg-teal-100 transition">
                        <i class="fa-solid fa-chart-column"></i> Rapor Pendidikan {{ $rapor->year }}
                    </a>
                @endforeach
            </div>
        </section>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof Chart === 'undefined' || {{ count($years) }} === 0) { return; }

                    const PALETTE = [
                        'rgb(13, 148, 136)', 'rgb(59, 130, 246)', 'rgb(245, 158, 11)',
                        'rgb(244, 63, 94)', 'rgb(139, 92, 246)', 'rgb(16, 185, 129)',
                        'rgb(236, 72, 153)', 'rgb(100, 116, 139)',
                    ];

                    const years = @json($years);
                    const kpiSeries = @json($kpiSeries);
                    const sectionSeries = @json($sectionSeries);

                    const lineDataset = (label, values, index) => ({
                        label: label,
                        data: values,
                        borderColor: PALETTE[index % PALETTE.length],
                        backgroundColor: PALETTE[index % PALETTE.length],
                        pointBackgroundColor: PALETTE[index % PALETTE.length],
                        tension: 0.3,
                        spanGaps: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    });

                    const lineOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, boxHeight: 12 } } },
                        scales: {
                            y: { beginAtZero: true, suggestedMax: 100 },
                            x: { title: { display: true, text: 'Tahun Rapor' } },
                        },
                    };

                    const buatGrafik = (id, datasets) => {
                        const el = document.getElementById(id);
                        if (! el || datasets.length === 0) { return; }
                        new Chart(el, {
                            type: 'line',
                            data: { labels: years, datasets: datasets },
                            options: lineOptions,
                        });
                    };

                    buatGrafik('chart-kpi', kpiSeries.map((s, i) => lineDataset(s.label, s.values, i)));

                    for (const dimensi of Object.keys(sectionSeries)) {
                        buatGrafik('chart-' + dimensi, sectionSeries[dimensi].map((s, i) => lineDataset(s.title, s.values, i)));
                    }
                });
            </script>
        @endpush
    @endif
</x-app-layout>
