{{-- Form bersama create/edit Rapor Pendidikan — data JSON dibangun dari input terstruktur --}}
@php
    $data = $rapor->data ?? [];

    $blankKpi = ['label' => '', 'value' => '', 'suffix' => '', 'kategori' => 'Baik', 'tren' => '', 'tren_note' => ''];
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
        kpi: @json($initKpi),
        sections: @json($initSections),
        indicators: @json($initIndicators),
        addKpi() { this.kpi.push({ label: '', value: '', suffix: '', kategori: 'Baik', tren: '', tren_note: '' }) },
        addItem(group) { this.sections[group].push({ title: '', skor: '', kategori: 'Baik', catatan: '', poin: '' }) },
        addIndicator() { this.indicators.push({ kode: '', indikator: '', capaian: '', status: 'Baik', narasi: '' }) },
        removeAt(list, i) { list.length > 1 && list.splice(i, 1) },
    }">
    @csrf
    @if ($rapor->exists)
        @method('PATCH')
    @endif

    <!-- Identitas Rapor -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="font-bold text-gray-900">Identitas Rapor</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun Rilis <span class="text-red-500">*</span></label>
                <input id="year" name="year" type="number" min="2000" max="2100" required value="{{ old('year', $rapor->year) }}"
                       placeholder="Contoh: 2025" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-gray-400">Satu tahun hanya satu rapor. Menentukan URL /rapor-pendidikan/{tahun}.</p>
                @error('year') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman <span class="text-red-500">*</span></label>
                <input id="title" name="title" type="text" required value="{{ old('title', $rapor->title) }}"
                       placeholder="Contoh: Laporan Transparansi Mutu & Relevansi Vokasi 2025" class="{{ $inputClass }}">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="intro" class="block text-sm font-medium text-gray-700 mb-1">Kata Pengantar & Komitmen Transparansi <span class="text-red-500">*</span></label>
            <textarea id="intro" name="intro" rows="4" required
                      placeholder="Penjelasan kepatuhan UU KIP No. 14/2008 dan UU PDP No. 27/2022..."
                      class="{{ $inputClass }}">{{ old('intro', $rapor->intro) }}</textarea>
            @error('intro') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="disclaimer" class="block text-sm font-medium text-gray-700 mb-1">Penafian Hukum (Disclaimer) <span class="text-red-500">*</span></label>
            <textarea id="disclaimer" name="disclaimer" rows="4" required
                      placeholder="Teks penafian: agregasi Level 2, perlindungan data pribadi, dsb."
                      class="{{ $inputClass }}">{{ old('disclaimer', $rapor->disclaimer) }}</textarea>
            @error('disclaimer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <label class="inline-flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="is_published" value="0">
            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $rapor->is_published))
                   class="rounded border-gray-300 text-teal-600 focus:ring-teal-600">
            <span class="text-sm font-medium text-gray-700">Publikasikan (tampil di navbar & halaman publik)</span>
        </label>
        <p class="text-xs text-gray-400">Kosongi untuk menyimpan sebagai draf — halaman publik akan 404 sampai dipublikasikan.</p>
    </div>

    <!-- KPI Highlight -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900">KPI Highlight <span class="text-red-500">*</span></h2>
            <button type="button" @click="addKpi()" class="text-sm font-semibold text-teal-700 hover:text-teal-800">+ Tambah KPI</button>
        </div>
        <p class="text-xs text-gray-400">Kartu ringkasan di atas halaman (grid 4 kolom). Contoh: Kemampuan Literasi, nilai 75.53.</p>
        <template x-for="(k, i) in kpi" :key="i">
            <div class="rounded-lg border border-gray-200 p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                    <input type="text" x-model="k.label" :name="'kpi['+i+'][label]'" required placeholder="Kemampuan Literasi" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nilai (0–100)</label>
                    <input type="number" step="0.01" min="0" max="100" x-model="k.value" :name="'kpi['+i+'][value]'" required placeholder="75.53" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Satuan</label>
                    <input type="text" x-model="k.suffix" :name="'kpi['+i+'][suffix]'" placeholder="%" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                    <select x-model="k.kategori" :name="'kpi['+i+'][kategori]'" class="{{ $inputClass }}">
                        @foreach ($kategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Tren</label>
                        <input type="text" x-model="k.tren" :name="'kpi['+i+'][tren]'" placeholder="+2,30" class="{{ $inputClass }}">
                    </div>
                    <button type="button" @click="removeAt(kpi, i)" class="px-2 py-2 text-red-600 hover:text-red-800" title="Hapus baris">✕</button>
                </div>
                <div class="md:col-span-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Catatan Tren (opsional)</label>
                    <input type="text" x-model="k.tren_note" :name="'kpi['+i+'][tren_note]'" placeholder="vs 2024 (73,23)" class="{{ $inputClass }}">
                </div>
            </div>
        </template>
        @error('kpi.*') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Dimensi Capaian (3 tab) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <h2 class="font-bold text-gray-900">Kartu Dimensi Capaian <span class="text-red-500">*</span></h2>
        <template x-for="group in ['akademik', 'vokasi', 'kelola']" :key="group">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700 capitalize" x-text="group"></h3>
                    <button type="button" @click="addItem(group)" class="text-xs font-semibold text-teal-700 hover:text-teal-800">+ Tambah Kartu</button>
                </div>
                <template x-for="(item, i) in sections[group]" :key="group + i">
                    <div class="rounded-lg border border-gray-200 p-4 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Judul Kartu</label>
                                <input type="text" x-model="item.title" :name="'sections['+group+']['+i+'][title]'" required placeholder="Kemampuan Literasi Peserta Didik" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Skor (teks)</label>
                                <input type="text" x-model="item.skor" :name="'sections['+group+']['+i+'][skor]'" required placeholder="75,53" class="{{ $inputClass }}">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Kategori</label>
                                <select x-model="item.kategori" :name="'sections['+group+']['+i+'][kategori]'" class="{{ $inputClass }}">
                                    @foreach ($kategoriOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Catatan</label>
                            <textarea x-model="item.catatan" :name="'sections['+group+']['+i+'][catatan]'" rows="2" required placeholder="Narasi singkat capaian..." class="{{ $inputClass }}"></textarea>
                        </div>
                        <div class="flex items-start gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Poin Rincian (satu poin per baris, opsional)</label>
                                <textarea x-model="item.poin" :name="'sections['+group+']['+i+'][poin]'" rows="2" placeholder="Tren meningkat +2,30 dibanding tahun 2024." class="{{ $inputClass }}"></textarea>
                            </div>
                            <button type="button" @click="removeAt(sections[group], i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus kartu">✕</button>
                        </div>
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- Grafik -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
        <h2 class="font-bold text-gray-900">Data Grafik <span class="text-red-500">*</span></h2>
        <div class="rounded-lg border border-gray-200 p-4 space-y-3">
            <h3 class="text-sm font-semibold text-gray-700">Grafik Batang — Perbandingan Dua Tahun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Tahun Sebelum</label>
                    <input type="text" name="bar_label_sebelum" required value="{{ $initBar['label_sebelum'] }}" placeholder="2024" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Label Tahun Sesudah</label>
                    <input type="text" name="bar_label_sesudah" required value="{{ $initBar['label_sesudah'] }}" placeholder="2025" class="{{ $inputClass }}">
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
            <h3 class="text-sm font-semibold text-gray-700">Grafik Radar — Relevansi Vokasi</h3>
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
        @error('radar_labels') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Tabel Indikator -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Katalog Indikator <span class="text-red-500">*</span></h2>
            <button type="button" @click="addIndicator()" class="text-sm font-semibold text-teal-700 hover:text-teal-800">+ Tambah Indikator</button>
        </div>
        <template x-for="(ind, i) in indicators" :key="i">
            <div class="rounded-lg border border-gray-200 p-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Kode</label>
                    <input type="text" x-model="ind.kode" :name="'indicators['+i+'][kode]'" required placeholder="A.1" class="{{ $inputClass }}">
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
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select x-model="ind.status" :name="'indicators['+i+'][status]'" class="{{ $inputClass }}">
                        @foreach ($kategoriOptions as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2 flex items-start gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Narasi RKT</label>
                        <input type="text" x-model="ind.narasi" :name="'indicators['+i+'][narasi]'" required placeholder="Tindak lanjut..." class="{{ $inputClass }}">
                    </div>
                    <button type="button" @click="removeAt(indicators, i)" class="mt-6 px-2 py-2 text-red-600 hover:text-red-800" title="Hapus baris">✕</button>
                </div>
            </div>
        </template>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition">
            {{ $rapor->exists ? 'Simpan Perubahan' : 'Simpan Rapor' }}
        </button>
        <a href="{{ route('admin.rapor-pendidikan.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Batal</a>
    </div>
</form>
