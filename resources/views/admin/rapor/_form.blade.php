{{-- Form bersama create/edit Rapor Pendidikan --}}
{{-- Struktur & label seksi mengikuti halaman publik resources/views/rapor/show.blade.php 1:1,
     sehingga apa yang diinput admin persis apa yang tampil di output publik. --}}
@php
    $data = $rapor->data ?? [];

    $blankKpi = ['key' => '', 'label' => '', 'value' => '', 'suffix' => '', 'kategori' => 'Baik', 'tren' => '', 'tren_note' => ''];
    $initKpi = old('kpi', $data['kpi'] ?? [$blankKpi]);

    $blankItem = ['title' => '', 'skor' => '', 'kategori' => 'Baik', 'catatan' => '', 'poin' => ''];
    $toForm = fn (array $items): array => collect($items)
        ->map(fn (array $i): array => ['title' => $i['title'] ?? '', 'skor' => (string) ($i['skor'] ?? ''), 'kategori' => $i['kategori'] ?? 'Baik', 'catatan' => $i['catatan'] ?? '', 'poin' => is_array($i['poin'] ?? null) ? implode("\n", $i['poin']) : (string) ($i['poin'] ?? '')])
        ->all();

    $initSections = old('sections', [
        'akademik' => $toForm($data['sections']['akademik'] ?? [$blankItem]),
        'vokasi' => $toForm($data['sections']['vokasi'] ?? [$blankItem]),
        'kelola' => $toForm($data['sections']['kelola'] ?? [$blankItem]),
    ]);

    $initBar = [
        'labels' => old('bar_labels', implode(', ', $data['charts']['bar']['labels'] ?? [])),
        'label_sebelum' => old('bar_label_sebelum', $data['charts']['bar']['label_sebelum'] ?? ''),
        'label_sesudah' => old('bar_label_sesudah', $data['charts']['bar']['label_sesudah'] ?? ''),
        'sebelum' => old('bar_sebelum', implode(', ', $data['charts']['bar']['sebelum'] ?? [])),
        'sesudah' => old('bar_sesudah', implode(', ', $data['charts']['bar']['sesudah'] ?? [])),
    ];
    $initRadar = [
        'labels' => old('radar_labels', implode(', ', $data['charts']['radar']['labels'] ?? [])),
        'values' => old('radar_values', implode(', ', $data['charts']['radar']['values'] ?? [])),
    ];

    $blankIndicator = ['kode' => '', 'indikator' => '', 'capaian' => '', 'status' => 'Baik', 'narasi' => ''];
    $initIndicators = old('indicators', $data['indicators'] ?? [$blankIndicator]);

    $kategoriOptions = ['Sangat Baik', 'Baik', 'Aman', 'Sedang', 'Cukup', 'Perlu Perhatian', 'Rendah'];
    $inputClass = 'block w-full rounded-lg border-gray-300 focus:border-teal-600 focus:ring-teal-600 text-sm';
@endphp

<form method="POST" action="{{ $rapor->exists ? route('admin.rapor-pendidikan.update', $rapor) : route('admin.rapor-pendidikan.store') }}"
      class="space-y-6" x-data="{
        tab: 'akademik',
        kpi: @json($initKpi),
        sections: @json($initSections),
        indicators: @json($initIndicators),
        addKpi() { this.kpi.push({ key: '', label: '', value: '', suffix: '', kategori: 'Baik', tren: '', tren_note: '' }) },
        addItem(group) { this.sections[group].push({ title: '', skor: '', kategori: 'Baik', catatan: '', poin: '' }) },
        addIndicator() { this.indicators.push({ kode: '', indikator: '', capaian: '', status: 'Baik', narasi: '' }) },
        removeAt(list, i) { if (list.length > 1) { list.splice(i, 1) } },
    }">
    @csrf
    @if ($rapor->exists)
        @method('PATCH')
    @endif

    <!-- Identitas Rapor (hero halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <div>
            <h2 class="font-bold text-gray-900">Identitas Rapor</h2>
            <p class="text-xs text-gray-400 mt-1">Tahun &amp; judul menentukan URL /rapor-pendidikan/{tahun} dan judul hero (h1) halaman publik.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Rilis <span class="text-red-500">*</span></label>
                <input id="year" name="year" type="number" min="2000" max="2100" required value="{{ old('year', $rapor->year) }}"
                       placeholder="Contoh: 2025" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-gray-400">Satu tahun hanya satu rapor.</p>
                @error('year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman (hero h1) <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required value="{{ old('title', $rapor->title) }}"
                       placeholder="Contoh: Laporan Transparansi Mutu & Relevansi Vokasi SMKN 1 Kota Besi (Capaian Rapor Pendidikan 2025)" class="{{ $inputClass }}">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $rapor->is_published))
                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-600">
            <span class="text-sm font-medium text-gray-700">Publikasikan (tampil di navbar & halaman publik)</span>
        </label>
        <p class="text-xs text-gray-400">Kosongi untuk menyimpan sebagai draf — halaman publik akan 404 sampai dipublikasikan.</p>
    </div>

    <!-- Kata Pengantar & Komitmen Transparansi (judul sama dengan halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-3">
        <h2 class="font-bold text-gray-900">Kata Pengantar &amp; Komitmen Transparansi <span class="text-red-500">*</span></h2>
        <p class="text-xs text-gray-400">Paragraf pembuka di bawah hero — jelaskan kepatuhan UU KIP No. 14/2008 dan UU PDP No. 27/2022.</p>
        <textarea id="intro" name="intro" rows="4" required
                  placeholder="Sebagai bentuk tanggung jawab publik dan kepatuhan terhadap UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik..."
                  class="{{ $inputClass }}">{{ old('intro', $rapor->intro) }}</textarea>
        @error('intro') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- KPI Highlight (kartu ringkasan grid 4 kolom di halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900">KPI Highlight <span class="text-red-500">*</span></h2>
            <button type="button" @click="addKpi()" class="text-sm font-semibold text-teal-700 hover:text-teal-800">+ Tambah KPI</button>
        </div>
        <p class="text-xs text-gray-400">
            Kartu ringkasan di atas halaman publik (grid 4 kolom): label + badge kategori + nilai + tren panah.
            Key = identitas KPI (huruf kecil/angka/garis bawah, mis. <code>literasi</code>) — kosongkan untuk dibuat otomatis.
        </p>
        <template x-for="(k, i) in kpi" :key="i">
            <div class="rounded-lg border border-gray-200 p-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Key</label>
                    <input type="text" x-model="k.key" :name="'kpi['+i+'][key]'" placeholder="literasi"
                           pattern="[a-z0-9_]*" class="{{ $inputClass }} font-mono">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                    <input type="text" x-model="k.label" :name="'kpi['+i+'][label]'" required placeholder="Kemampuan Literasi" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nilai (0–100)</label>
                    <input type="number" step="0.01" min="0" max="100" x-model="k.value" :name="'kpi['+i+'][value]'" required placeholder="75.53" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Satuan</label>
                    <input type="text" x-model="k.suffix" :name="'kpi['+i+'][suffix]'" placeholder="%" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori (badge)</label>
                    <select x-model="k.kategori" :name="'kpi['+i+'][kategori]'" class="{{ $inputClass }}">
                        @foreach ($kategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tren</label>
                        <input type="text" x-model="k.tren" :name="'kpi['+i+'][tren]'" placeholder="+2,30 (isi -1,00 bila turun)" class="{{ $inputClass }}">
                    </div>
                    <button type="button" @click="removeAt(kpi, i)" class="px-2 py-2 text-red-600 hover:text-red-800" title="Hapus baris">✕</button>
                </div>
                <div class="md:col-span-8">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan Tren (opsional)</label>
                    <input type="text" x-model="k.tren_note" :name="'kpi['+i+'][tren_note]'" placeholder="vs 2024 (73,23)" class="{{ $inputClass }}">
                </div>
            </div>
        </template>
        @error('kpi.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Dimensi Capaian — tab & label sama persis dengan halaman publik -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div>
            <h2 class="font-bold text-gray-900">Kartu Dimensi Capaian <span class="text-red-500">*</span></h2>
            <p class="text-xs text-gray-400 mt-1">Tiga tab yang tampil di halaman publik. Setiap kartu: judul + badge kategori + skor + catatan + poin rincian.</p>
        </div>

        <div class="flex flex-wrap gap-2" role="tablist">
            <button type="button" @click="tab = 'akademik'" :class="tab === 'akademik' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Mutu Akademik</button>
            <button type="button" @click="tab = 'vokasi'" :class="tab === 'vokasi' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Vokasi &amp; Industri</button>
            <button type="button" @click="tab = 'kelola'" :class="tab === 'kelola' ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-sm font-semibold border border-gray-200 transition-colors" role="tab">Iklim Sekolah &amp; Tata Kelola</button>
        </div>

        <!-- Tab: Mutu Akademik -->
        <div x-show="tab === 'akademik'" class="space-y-3" role="tabpanel">
            <template x-for="(item, i) in sections.akademik" :key="'akademik-' + i">
                <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Judul Kartu</label>
                            <input type="text" x-model="item.title" :name="'sections[akademik]['+i+'][title]'" required placeholder="Kemampuan Literasi Peserta Didik" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Skor (teks yang tampil)</label>
                            <input type="text" x-model="item.skor" :name="'sections[akademik]['+i+'][skor]'" required placeholder="75,53" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori (badge)</label>
                            <select x-model="item.kategori" :name="'sections[akademik]['+i+'][kategori]'" class="{{ $inputClass }}">
                                @foreach ($kategoriOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                        <textarea x-model="item.catatan" :name="'sections[akademik]['+i+'][catatan]'" rows="2" required placeholder="91,11% peserta didik mencapai batas kompetensi minimum membaca..." class="{{ $inputClass }}"></textarea>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Poin Rincian (satu poin per baris, tampil sebagai bullet list)</label>
                            <textarea x-model="item.poin" :name="'sections[akademik]['+i+'][poin]'" rows="2" placeholder="Tren meningkat +2,30 dibanding tahun 2024 (73,23)." class="{{ $inputClass }}"></textarea>
                        </div>
                        <button type="button" @click="removeAt(sections.akademik, i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus kartu">✕</button>
                    </div>
                </div>
            </template>
            <button type="button" @click="addItem('akademik')" class="text-xs font-semibold text-teal-700 hover:text-teal-800">+ Tambah Kartu Akademik</button>
            @error('sections.akademik.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Tab: Vokasi & Industri -->
        <div x-show="tab === 'vokasi'" class="space-y-3" role="tabpanel" x-cloak>
            <template x-for="(item, i) in sections.vokasi" :key="'vokasi-' + i">
                <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Judul Kartu</label>
                            <input type="text" x-model="item.title" :name="'sections[vokasi]['+i+'][title]'" required placeholder="Kepuasan Dunia Kerja (Mitra DUDI)" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Skor (teks yang tampil)</label>
                            <input type="text" x-model="item.skor" :name="'sections[vokasi]['+i+'][skor]'" required placeholder="93,33%" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori (badge)</label>
                            <select x-model="item.kategori" :name="'sections[vokasi]['+i+'][kategori]'" class="{{ $inputClass }}">
                                @foreach ($kategoriOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                        <textarea x-model="item.catatan" :name="'sections[vokasi]['+i+'][catatan]'" rows="2" required placeholder="Mitra DUDI memberikan penilaian sangat tinggi terhadap soft skills alumni..." class="{{ $inputClass }}"></textarea>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Poin Rincian (satu poin per baris, tampil sebagai bullet list)</label>
                            <textarea x-model="item.poin" :name="'sections[vokasi]['+i+'][poin]'" rows="2" placeholder="Skor Kualitas Teaching Factory (TeFa): 52,04." class="{{ $inputClass }}"></textarea>
                        </div>
                        <button type="button" @click="removeAt(sections.vokasi, i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus kartu">✕</button>
                    </div>
                </div>
            </template>
            <button type="button" @click="addItem('vokasi')" class="text-xs font-semibold text-teal-700 hover:text-teal-800">+ Tambah Kartu Vokasi</button>
            @error('sections.vokasi.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Tab: Iklim Sekolah & Tata Kelola -->
        <div x-show="tab === 'kelola'" class="space-y-3" role="tabpanel" x-cloak>
            <template x-for="(item, i) in sections.kelola" :key="'kelola-' + i">
                <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Judul Kartu</label>
                            <input type="text" x-model="item.title" :name="'sections[kelola]['+i+'][title]'" required placeholder="Iklim Keamanan Sekolah" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Skor (teks yang tampil)</label>
                            <input type="text" x-model="item.skor" :name="'sections[kelola]['+i+'][skor]'" required placeholder="74,41" class="{{ $inputClass }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Kategori (badge)</label>
                            <select x-model="item.kategori" :name="'sections[kelola]['+i+'][kategori]'" class="{{ $inputClass }}">
                                @foreach ($kategoriOptions as $opt)
                                    <option value="{{ $opt }}">{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                        <textarea x-model="item.catatan" :name="'sections[kelola]['+i+'][catatan]'" rows="2" required placeholder="Sekolah menjamin lingkungan belajar yang bebas dari perundungan..." class="{{ $inputClass }}"></textarea>
                    </div>
                    <div class="flex items-start gap-2">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Poin Rincian (satu poin per baris, tampil sebagai bullet list)</label>
                            <textarea x-model="item.poin" :name="'sections[kelola]['+i+'][poin]'" rows="2" placeholder="Pelaporan Dana BOSP tepat waktu melalui ARKAS." class="{{ $inputClass }}"></textarea>
                        </div>
                        <button type="button" @click="removeAt(sections.kelola, i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus kartu">✕</button>
                    </div>
                </div>
            </template>
            <button type="button" @click="addItem('kelola')" class="text-xs font-semibold text-teal-700 hover:text-teal-800">+ Tambah Kartu Kelola</button>
            @error('sections.kelola.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <!-- Visualisasi Data (judul sama dengan halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="font-bold text-gray-900">Visualisasi Data <span class="text-red-500">*</span></h2>

        <div class="rounded-lg border border-gray-200 p-4 space-y-3"
             x-data="{ sebelum: @json($initBar['label_sebelum']), sesudah: @json($initBar['label_sesudah']) }">
            <h3 class="text-sm font-semibold text-gray-700">
                Grafik Batang — Perbandingan Capaian
                <span class="text-teal-700 font-mono" x-text="sebelum || '…'"></span> vs
                <span class="text-teal-700 font-mono" x-text="sesudah || '…'"></span>
            </h3>
            <p class="text-xs text-gray-400">Judul di atas mengikuti input label tahun — sama seperti heading grafik di halaman publik.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Tahun Sebelum</label>
                    <input type="text" name="bar_label_sebelum" required x-model="sebelum" value="{{ $initBar['label_sebelum'] }}" placeholder="2024" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Tahun Sesudah</label>
                    <input type="text" name="bar_label_sesudah" required x-model="sesudah" value="{{ $initBar['label_sesudah'] }}" placeholder="2025" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Kategori (pisahkan koma)</label>
                    <input type="text" name="bar_labels" required value="{{ $initBar['labels'] }}" placeholder="Literasi, Numerasi, Penyerapan Lulusan, TeFa" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nilai Tahun Sebelum (urut sesuai label, pisahkan koma)</label>
                    <input type="text" name="bar_sebelum" required value="{{ $initBar['sebelum'] }}" placeholder="73.23, 58.14, 86.75, 51.00" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nilai Tahun Sesudah (urut sesuai label, pisahkan koma)</label>
                    <input type="text" name="bar_sesudah" required value="{{ $initBar['sesudah'] }}" placeholder="75.53, 58.57, 75.70, 52.04" class="{{ $inputClass }}">
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700">Grafik Radar — Relevansi Vokasi &amp; Dunia Kerja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Sumbu (pisahkan koma)</label>
                    <input type="text" name="radar_labels" required value="{{ $initRadar['labels'] }}" placeholder="Kepuasan DUDI, Link & Match, Guru Tamu Industri, Kurikulum" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nilai (urut sesuai label, pisahkan koma)</label>
                    <input type="text" name="radar_values" required value="{{ $initRadar['values'] }}" placeholder="93.33, 59.96, 57.82, 64.20" class="{{ $inputClass }}">
                </div>
            </div>
        </div>
        @error('bar_labels') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('bar_sebelum') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('bar_sesudah') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('radar_labels') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
        @error('radar_values') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Katalog Indikator (judul & kolom sama dengan tabel halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Katalog Indikator yang Dipublikasikan <span class="text-red-500">*</span></h2>
            <button type="button" @click="addIndicator()" class="text-sm font-semibold text-teal-700 hover:text-teal-800">+ Tambah Indikator</button>
        </div>
        <p class="text-xs text-gray-400">Baris tabel indikator di halaman publik: Kode · Indikator · Capaian {{ $rapor->year ?? 'Tahun Ini' }} · Status (badge) · Narasi Tindak Lanjut RKT.</p>
        <template x-for="(ind, i) in indicators" :key="i">
            <div class="rounded-lg border border-gray-200 p-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kode</label>
                    <input type="text" x-model="ind.kode" :name="'indicators['+i+'][kode]'" required placeholder="A.1" class="{{ $inputClass }} font-mono">
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Indikator</label>
                    <input type="text" x-model="ind.indikator" :name="'indicators['+i+'][indikator]'" required placeholder="Kemampuan Literasi Peserta Didik" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Capaian</label>
                    <input type="text" x-model="ind.capaian" :name="'indicators['+i+'][capaian]'" required placeholder="75,53" class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status (badge)</label>
                    <select x-model="ind.status" :name="'indicators['+i+'][status]'" class="{{ $inputClass }}">
                        @foreach ($kategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex items-start gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Narasi Tindak Lanjut RKT</label>
                        <input type="text" x-model="ind.narasi" :name="'indicators['+i+'][narasi]'" required placeholder="Penguatan literasi digital berbasis sains..." class="{{ $inputClass }}">
                    </div>
                    <button type="button" @click="removeAt(indicators, i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus baris">✕</button>
                </div>
            </div>
        </template>
        @error('indicators.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Penafian Hukum (judul sama dengan kotak disclaimer halaman publik) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-3">
        <h2 class="font-bold text-gray-900">Penafian Hukum (Disclaimer) <span class="text-red-500">*</span></h2>
        <p class="text-xs text-gray-400">Tampil di kotak kuning bawah halaman publik, diikuti tautan PPID ke halaman Kontak &amp; Lokasi.</p>
        <textarea id="disclaimer" name="disclaimer" rows="4" required
                  placeholder="Seluruh data diekstraksi dari Laporan Rapor Pendidikan Kemendikdasmen RI dalam bentuk agregasi makro institusi..."
                  class="{{ $inputClass }}">{{ old('disclaimer', $rapor->disclaimer) }}</textarea>
        @error('disclaimer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition">
            {{ $rapor->exists ? 'Simpan Perubahan' : 'Simpan Rapor' }}
        </button>
        <a href="{{ route('admin.rapor-pendidikan.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Batal</a>
    </div>
</form>
