<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Konten Beranda</h1>
        <p class="text-gray-600 mt-1">Semua konten halaman depan dikelola di sini — hero, carousel, statistik, testimoni, dan partner.</p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    @php
        $activeBadge = fn ($active) => $active
            ? '<span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">Aktif</span>'
            : '<span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-500 text-xs font-semibold">Non-aktif</span>';
    @endphp

    <!-- Teks Hero (settings) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Teks Hero &amp; Judul Seksi</h2>
        @php
            $heroImage = $settings['hero_image'] ?? null;
        @endphp
        <form method="POST" action="{{ route('admin.home.settings') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <!-- Foto Latar Hero -->
            <div class="border border-dashed border-gray-300 rounded-xl p-5 bg-gray-50">
                <label class="block text-sm font-medium text-gray-700 mb-1">Foto Hero</label>
                <p class="text-xs text-gray-500 mb-3">Gambar latar seksi hero di halaman depan. Format JPG/PNG/WEBP, maksimal 2 MB. Jika tidak diunggah, akan memakai gambar bawaan.</p>
                @if ($heroImage)
                    <div class="flex items-center gap-4 mb-3 p-3 bg-white rounded-lg border border-gray-200">
                        <img src="{{ asset('storage/'.$heroImage) }}" alt="Foto hero saat ini" class="h-20 w-36 object-cover rounded-lg border">
                        <div class="text-xs">
                            <p class="font-semibold text-gray-900">Foto hero saat ini</p>
                            <label class="mt-1.5 inline-flex items-center gap-1.5 cursor-pointer text-red-600 hover:text-red-800">
                                <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Hapus foto hero saat ini</span>
                            </label>
                        </div>
                    </div>
                @endif
                <input name="hero_image" type="file" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-900 file:text-white file:text-xs file:font-semibold hover:file:bg-gray-800">
                @error('hero_image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge Hero</label>
                    <input name="hero_badge" value="{{ old('hero_badge', $settings['hero_badge'] ?? 'Penerimaan Peserta Didik Baru Terbuka') }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Hero <span class="text-red-500">*</span></label>
                    <input name="hero_title" required value="{{ old('hero_title', $settings['hero_title'] ?? 'Pendidikan Berkualitas untuk Generasi Emas') }}" class="block w-full rounded-lg border-gray-300">
                    @error('hero_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sub Judul Hero</label>
                <textarea name="hero_subtitle" rows="2" class="block w-full rounded-lg border-gray-300">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? 'SMKN 1 Kotabesi menyediakan 3 jurusan unggulan dengan kurikulum modern, fasilitas lengkap, dan prestasi terbaik di bidangnya.') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Section Carousel</label>
                    <input name="section_carousel_title" value="{{ old('section_carousel_title', $settings['section_carousel_title'] ?? 'Kegiatan Terkini') }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Section Statistik</label>
                    <input name="section_statistics_title" value="{{ old('section_statistics_title', $settings['section_statistics_title'] ?? 'Tentang Kami') }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Section Testimoni</label>
                    <input name="section_testimonials_title" value="{{ old('section_testimonials_title', $settings['section_testimonials_title'] ?? 'Testimoni') }}" class="block w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Section Partner</label>
                    <input name="section_partners_title" value="{{ old('section_partners_title', $settings['section_partners_title'] ?? 'Partner & Mitra') }}" class="block w-full rounded-lg border-gray-300">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">Simpan Teks</button>
        </form>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- CAROUSEL -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: false, editId: null }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Carousel ({{ $slides->count() }})</h2>
                <button @click="open = !open; editId = null" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-900 text-white">+ Tambah</button>
            </div>

            {{-- Form tambah --}}
            <div x-show="open" x-cloak class="mb-4 border border-dashed border-gray-300 rounded-lg p-4 space-y-3"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <form method="POST" action="{{ route('admin.home.store', 'slides') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="sort" value="{{ $slides->max('sort') + 1 }}">
                    <input name="title" required placeholder="Judul slide" class="block w-full rounded-lg border-gray-300">
                    <input name="description" placeholder="Keterangan (opsional)" class="block w-full rounded-lg border-gray-300">
                    <input name="image" type="file" accept="image/*" required class="block w-full rounded-lg border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">Simpan Slide</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse ($slides as $slide)
                    <div class="border border-gray-100 rounded-lg p-3">
                        <div class="flex items-start gap-3">
                            <img src="{{ $slide->imageSrc() }}" alt="" class="h-14 w-20 rounded object-cover border shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-sm text-gray-900 truncate">{{ $slide->title }}</p>
                                <p class="text-xs text-gray-400">Urutan: {{ $slide->sort }} • {!! $activeBadge($slide->is_active) !!}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button @click="editId = editId === {{ $slide->id }} ? null : {{ $slide->id }}" class="text-xs font-medium text-blue-600">Edit</button>
                                <form method="POST" action="{{ route('admin.home.destroy', ['slides', $slide->id]) }}"
                                      onsubmit="return confirm('Hapus slide ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>

                        {{-- Form edit inline --}}
                        <form method="POST" action="{{ route('admin.home.update', ['slides', $slide->id]) }}"
                              enctype="multipart/form-data" x-show="editId === {{ $slide->id }}" x-cloak class="mt-3 pt-3 border-t border-gray-100 space-y-2"
                                   x-transition:enter="ease-out duration-200"
                                   x-transition:enter-start="opacity-0 -translate-y-1"
                                   x-transition:enter-end="opacity-100 translate-y-0"
                                   x-transition:leave="ease-in duration-150"
                                   x-transition:leave-start="opacity-100"
                                   x-transition:leave-end="opacity-0">
                            @csrf @method('PATCH')
                            <input name="title" required value="{{ $slide->title }}" class="block w-full rounded-lg border-gray-300 text-sm">
                            <input name="description" value="{{ $slide->description }}" placeholder="Keterangan" class="block w-full rounded-lg border-gray-300 text-sm">
                            <div class="flex items-center gap-3">
                                <input name="sort" type="number" min="0" value="{{ $slide->sort }}" class="w-24 rounded-lg border-gray-300 text-sm">
                                <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" name="is_active" value="1" @checked($slide->is_active) class="rounded border-gray-300"> Aktif
                                </label>
                            </div>
                            <input name="image" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 text-sm">
                            <button class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold">Simpan Slide</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada slide.</p>
                @endforelse
            </div>
        </div>

        <!-- STATISTIK -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: false, editId: null }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Statistik ({{ $statistics->count() }})</h2>
                <button @click="open = !open; editId = null" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-900 text-white">+ Tambah</button>
            </div>

            <div x-show="open" x-cloak class="mb-4 border border-dashed border-gray-300 rounded-lg p-4"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <form method="POST" action="{{ route('admin.home.store', 'statistics') }}" class="flex gap-3 items-end">
                    @csrf
                    <input type="hidden" name="sort" value="{{ $statistics->max('sort') + 1 }}">
                    <div class="flex-1">
                        <input name="label" required placeholder="Label (Contoh: Siswa Aktif)" class="block w-full rounded-lg border-gray-300">
                    </div>
                    <div class="w-28">
                        <input name="value" required placeholder="1000+" class="block w-full rounded-lg border-gray-300">
                    </div>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold">Simpan</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse ($statistics as $stat)
                    <div class="border border-gray-100 rounded-lg p-3">
                        <div class="flex items-center gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-gray-900">{{ $stat->value }} <span class="font-medium text-gray-600">{{ $stat->label }}</span></p>
                                <p class="text-xs text-gray-400 ml-0.5">{!! $activeBadge($stat->is_active) !!}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="editId = editId === {{ $stat->id }} ? null : {{ $stat->id }}" class="text-xs font-medium text-blue-600">Edit</button>
                                <form method="POST" action="{{ route('admin.home.destroy', ['statistics', $stat->id]) }}"
                                      onsubmit="return confirm('Hapus statistik ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.home.update', ['statistics', $stat->id]) }}"
                              x-show="editId === {{ $stat->id }}" x-cloak class="mt-3 pt-3 border-t border-gray-100 flex gap-2 items-end"
                                  x-transition:enter="ease-out duration-200"
                                  x-transition:enter-start="opacity-0 -translate-y-1"
                                  x-transition:enter-end="opacity-100 translate-y-0"
                                  x-transition:leave="ease-in duration-150"
                                  x-transition:leave-start="opacity-100"
                                  x-transition:leave-end="opacity-0">
                            @csrf @method('PATCH')
                            <div class="flex-1">
                                <input name="label" value="{{ $stat->label }}" class="block w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div class="w-28">
                                <input name="value" value="{{ $stat->value }}" class="block w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                                <input type="checkbox" name="is_active" value="1" @checked($stat->is_active) class="rounded border-gray-300"> Aktif
                            </label>
                            <button class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold">Simpan</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada statistik.</p>
                @endforelse
            </div>
        </div>

        <!-- TESTIMONI -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: false, editId: null }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Testimoni ({{ $testimonials->count() }})</h2>
                <button @click="open = !open; editId = null" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-900 text-white">+ Tambah</button>
            </div>

            <div x-show="open" x-cloak class="mb-4 border border-dashed border-gray-300 rounded-lg p-4 space-y-3"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <form method="POST" action="{{ route('admin.home.store', 'testimonials') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="sort" value="{{ $testimonials->max('sort') + 1 }}">
                    <div class="flex gap-3">
                        <input name="name" required placeholder="Nama" class="block flex-1 rounded-lg border-gray-300">
                        <input name="role" placeholder="Peran (Contoh: Wali Murid TKJ)" class="block w-48 rounded-lg border-gray-300">
                    </div>
                    <textarea name="content" required rows="2" placeholder="Isi testimoni" class="block w-full rounded-lg border-gray-300"></textarea>
                    <input name="photo" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 text-sm">
                    <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300"> Aktif
                    </label>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold block">Simpan Testimoni</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse ($testimonials as $item)
                    <div class="border border-gray-100 rounded-lg p-3">
                        <div class="flex items-start gap-3">
                            <img src="{{ $item->photoSrc() }}" alt="" class="h-10 w-10 rounded-full object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-sm">{{ $item->name }} <span class="text-xs text-gray-400 font-normal">• {{ $item->role }}</span></p>
                                <p class="text-xs text-gray-500 line-clamp-2">{{ $item->content }}</p>
                                <p class="mt-1">{!! $activeBadge($item->is_active) !!}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button @click="editId = editId === {{ $item->id }} ? null : {{ $item->id }}" class="text-xs font-medium text-blue-600">Edit</button>
                                <form method="POST" action="{{ route('admin.home.destroy', ['testimonials', $item->id]) }}"
                                      onsubmit="return confirm('Hapus testimoni ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.home.update', ['testimonials', $item->id]) }}"
                              enctype="multipart/form-data" x-show="editId === {{ $item->id }}" x-cloak class="mt-3 pt-3 border-t border-gray-100 space-y-2"
                                   x-transition:enter="ease-out duration-200"
                                   x-transition:enter-start="opacity-0 -translate-y-1"
                                   x-transition:enter-end="opacity-100 translate-y-0"
                                   x-transition:leave="ease-in duration-150"
                                   x-transition:leave-start="opacity-100"
                                   x-transition:leave-end="opacity-0">
                            @csrf @method('PATCH')
                            <div class="flex gap-2">
                                <input name="name" required value="{{ $item->name }}" class="block flex-1 rounded-lg border-gray-300 text-sm">
                                <input name="role" value="{{ $item->role }}" class="block w-48 rounded-lg border-gray-300 text-sm">
                            </div>
                            <textarea name="content" required rows="2" class="block w-full rounded-lg border-gray-300 text-sm">{{ $item->content }}</textarea>
                            <div class="flex items-center gap-3">
                                <input name="sort" type="number" min="0" value="{{ $item->sort }}" class="w-24 rounded-lg border-gray-300 text-sm">
                                <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" name="is_active" value="1" @checked($item->is_active) class="rounded border-gray-300"> Aktif
                                </label>
                            </div>
                            <input name="photo" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 text-sm">
                            <button class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold">Simpan Testimoni</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada testimoni.</p>
                @endforelse
            </div>
        </div>

        <!-- PARTNER -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ open: false, editId: null }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Partner &amp; Mitra ({{ $partners->count() }})</h2>
                <button @click="open = !open; editId = null" class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-gray-900 text-white">+ Tambah</button>
            </div>

            <div x-show="open" x-cloak class="mb-4 border border-dashed border-gray-300 rounded-lg p-4 space-y-3"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <form method="POST" action="{{ route('admin.home.store', 'partners') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="sort" value="{{ $partners->max('sort') + 1 }}">
                    <input name="name" required placeholder="Nama partner" class="block w-full rounded-lg border-gray-300">
                    <input name="url" placeholder="Website (https://...)" class="block w-full rounded-lg border-gray-300">
                    <input name="logo" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 text-sm">
                    <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300"> Aktif
                    </label>
                    <button class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold block">Simpan Partner</button>
                </form>
            </div>

            <div class="space-y-3">
                @forelse ($partners as $partner)
                    <div class="border border-gray-100 rounded-lg p-3">
                        <div class="flex items-center gap-3">
                            @if ($partner->logoSrc())
                                <img src="{{ $partner->logoSrc() }}" alt="" class="h-8 w-8 object-contain shrink-0">
                            @else
                                <span class="h-8 w-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] shrink-0">logo</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-sm truncate">{{ $partner->name }}</p>
                                <p class="text-[11px] text-gray-400">{!! $activeBadge($partner->is_active) !!}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="editId = editId === {{ $partner->id }} ? null : {{ $partner->id }}" class="text-xs font-medium text-blue-600">Edit</button>
                                <form method="POST" action="{{ route('admin.home.destroy', ['partners', $partner->id]) }}"
                                      onsubmit="return confirm('Hapus partner ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.home.update', ['partners', $partner->id]) }}"
                              enctype="multipart/form-data" x-show="editId === {{ $partner->id }}" x-cloak class="mt-3 pt-3 border-t border-gray-100 space-y-2"
                                   x-transition:enter="ease-out duration-200"
                                   x-transition:enter-start="opacity-0 -translate-y-1"
                                   x-transition:enter-end="opacity-100 translate-y-0"
                                   x-transition:leave="ease-in duration-150"
                                   x-transition:leave-start="opacity-100"
                                   x-transition:leave-end="opacity-0">
                            @csrf @method('PATCH')
                            <input name="name" required value="{{ $partner->name }}" class="block w-full rounded-lg border-gray-300 text-sm">
                            <input name="url" value="{{ $partner->url }}" placeholder="Website" class="block w-full rounded-lg border-gray-300 text-sm">
                            <div class="flex items-center gap-3">
                                <input name="sort" type="number" min="0" value="{{ $partner->sort }}" class="w-24 rounded-lg border-gray-300 text-sm">
                                <label class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" name="is_active" value="1" @checked($partner->is_active) class="rounded border-gray-300"> Aktif
                                </label>
                                <label class="inline-flex items-center gap-1 text-xs text-red-600">
                                    <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300"> Hapus logo
                                </label>
                            </div>
                            <input name="logo" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 text-sm">
                            <button class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold">Simpan Partner</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Belum ada partner.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-admin-layout>
