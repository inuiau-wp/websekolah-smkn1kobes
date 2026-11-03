<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Sekolah</h1>
        <p class="text-gray-600 mt-1">Data yang tampil di halaman /profil dan /kontak (kontak singkat, peta, WhatsApp).</p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-4xl">
        <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            <!-- Logo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Sekolah</label>
                    @if ($profil->logo_path)
                        <div class="flex items-center gap-4 mb-3">
                            <img src="{{ asset('storage/'.$profil->logo_path) }}" alt="Logo" class="h-16 w-32 object-contain rounded-lg border">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-red-600">Hapus logo</span>
                            </label>
                        </div>
                    @else
                        <div class="h-32 w-32 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] shrink-0 border border-dashed border-gray-300">logo</div>
                    @endif
                    <input name="logo" type="file" accept="image/*"
                           class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
                    @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-[140px] hidden md:block">
                    <p class="text-xs text-gray-400">Logo ditampilkan di header profil &amp; kontak.</p>
                </div>
            </div>

            <!-- Gambar Cover Hero -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Cover Hero (halaman /profil)</label>
                    @if ($profil->cover_path)
                        <div class="flex items-center gap-4 mb-3">
                            <img src="{{ $profil->cover_url }}" alt="Cover saat ini" class="h-24 w-48 object-cover rounded-lg border">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="remove_cover" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="text-red-600">Hapus cover</span>
                            </label>
                        </div>
                    @else
                        <div class="h-24 w-48 rounded-lg flex items-center justify-center text-gray-400 text-[10px] border border-dashed border-gray-300 bg-gray-50">belum ada cover</div>
                    @endif
                    <input name="cover" type="file" accept="image/*"
                           class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
                    <p class="mt-1 text-xs text-gray-400">Direkomendasikan 1920×600 px, maks 4 MB. Tanpa cover, hero tampil gradient warna jurusan.</p>
                    @error('cover') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="mt-[130px] hidden md:block">
                    <p class="text-xs text-gray-400">Cover menjadi latar belakang header halaman profil publik.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input id="school_name" name="school_name" required value="{{ old('school_name', $profil->school_name) }}"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    @error('school_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                    <input id="tagline" name="tagline" value="{{ old('tagline', $profil->tagline) }}"
                           placeholder="Contoh: Pendidikan Berkualitas untuk Generasi Emas"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input id="address" name="address" value="{{ old('address', $profil->address) }}"
                           placeholder="Alamat lengkap sekolah (dipakai Google Maps)"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input id="phone" name="phone" value="{{ old('phone', $profil->phone) }}"
                           placeholder="Contoh: (0xxx) xxx-xxxx"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $profil->email) }}"
                           placeholder="Contoh: info@smkn1kotabesi.sch.id"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="wa_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input id="wa_number" name="wa_number" value="{{ old('wa_number', $profil->wa_number) }}"
                           placeholder="Contoh: 628123456789 (tanpa +)"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    <p class="mt-1 text-xs text-gray-400">Dipakai tautan wa.me di halaman kontak & profil.</p>
                </div>
            </div>

            <!-- Koordinat Peta -->
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-1">Koordinat Peta (Opsional, Presisi)</h2>
                <p class="text-xs text-gray-500 mb-3">
                    Bila diisi, peta di halaman kontak memakai koordinat ini (bukan geocode alamat). Tips mengambil koordinat:
                    buka lokasi sekolah di <a href="https://www.google.com/maps" target="_blank" class="text-blue-600 underline">Google Maps</a>,
                    klik kanan pada titik yang tepat lalu salin angka pertama (latitude) dan kedua (longitude).
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="map_latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                        <input id="map_latitude" name="map_latitude" type="text" inputmode="decimal"
                               value="{{ old('map_latitude', $profil->map_latitude) }}"
                               placeholder="Contoh: -2.416326"
                               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        @error('map_latitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="map_longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                        <input id="map_longitude" name="map_longitude" type="text" inputmode="decimal"
                               value="{{ old('map_longitude', $profil->map_longitude) }}"
                               placeholder="Contoh: 112.9857232"
                               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        @error('map_longitude') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-400">Kosongkan kedua kolom agar peta kembali mengikuti nama sekolah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi</label>
                    <textarea id="vision" name="vision" rows="3"
                              placeholder="Visi sekolah..."
                              class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('vision', $profil->vision) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Misi</label>
                    <textarea id="mission" name="mission" rows="4"
                              placeholder="Satu misi per baris..."
                              class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('mission', $profil->mission) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Pisahkan tiap misi dengan baris baru (akan ditampilkan sebagai daftar).</p>
                </div>
                <div>
                    <label for="history" class="block text-sm font-medium text-gray-700 mb-1">Sejarah</label>
                    <textarea id="history" name="history" rows="4"
                              placeholder="Sejarah singkat sekolah..."
                              class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('history', $profil->history) }}</textarea>
                </div>
                <div>
                    <label for="accreditation" class="block text-sm font-medium text-gray-700 mb-1">Akreditasi</label>
                    <input id="accreditation" name="accreditation" value="{{ old('accreditation', $profil->accreditation) }}"
                           placeholder="Contoh: A"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                </div>
                <div>
                    <label for="headmaster" class="block text-sm font-medium text-gray-700 mb-1">Kepala Sekolah (Nama Cadangan)</label>
                    <input id="headmaster" name="headmaster" value="{{ old('headmaster', $profil->headmaster) }}"
                           placeholder="Nama lengkap kepala sekolah"
                           class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    <p class="mt-1 text-xs text-gray-400">
                        Tips: Jika Anda membuat akun guru dengan jabatan <strong>Kepala Sekolah</strong> di <a href="{{ route('admin.users.index') }}" class="text-blue-600 underline">Kelola Akun</a>, nama, pas foto, dan NIP akan otomatis terhubung ke website.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Simpan Profil
                </button>
                <a href="{{ route('profil') }}" target="_blank"
                   class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Lihat Profil (publik)</a>
            </div>
        </form>
    </div>
</x-admin-layout>
