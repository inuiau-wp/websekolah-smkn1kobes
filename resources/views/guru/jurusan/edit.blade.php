<x-guru-layout>
    <div class="mb-6">
        <a href="{{ route('guru.jurusan.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Program Keahlian Saya</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Konten Jurusan {{ $jurusan->code }}</h1>
        <p class="text-sm text-gray-600 mt-1">Nama, kode, warna, dan kepala jurusan diatur oleh admin — Anda hanya dapat mengubah deskripsi, kompetensi, dan foto jurusan.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('guru.jurusan.update', $jurusan) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PATCH')

            <!-- Info statis (tidak dapat diubah guru) -->
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Data_terkunci (Admin Only)</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Nama Jurusan</dt>
                        <dd class="font-semibold text-gray-800">{{ $jurusan->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Kode</dt>
                        <dd class="font-semibold text-gray-800">{{ $jurusan->code }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea id="description" name="description" rows="5" maxlength="2000"
                          placeholder="Deskripsi lengkap jurusan (tampil di halaman detail)..."
                          class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">{{ old('description', $jurusan->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="competencies" class="block text-sm font-medium text-gray-700 mb-1">Kompetensi Keahlian</label>
                <input id="competencies" name="competencies" type="text" value="{{ old('competencies', $jurusan->competencies) }}"
                       placeholder="Jaringan Komputer, Administrasi Sistem, Keamanan Jaringan (pisahkan dengan koma)"
                       class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                <p class="mt-1 text-xs text-gray-400">Tulis beberapa kompetensi dipisah <strong>koma (,)</strong> — akan tampil sebagai tag di halaman detail.</p>
                @error('competencies') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Visi & Misi Jurusan -->
            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 space-y-4">
                <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Visi &amp; Misi Jurusan</h2>
                <div>
                    <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi Jurusan</label>
                    <textarea id="vision" name="vision" rows="3" maxlength="1000"
                              placeholder="Visi khusus jurusan ini (tampil di section Visi & Misi halaman detail)..."
                              class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">{{ old('vision', $jurusan->vision) }}</textarea>
                    @error('vision') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Misi Jurusan (tulis per baris)</label>
                    <textarea id="mission" name="mission" rows="5" maxlength="2000"
                              placeholder="1. Misi pertama&#10;2. Misi kedua..."
                              class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">{{ old('mission', $jurusan->mission) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Setiap baris ditampilkan sebagai satu poin misi di halaman detail.</p>
                    @error('mission') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="border border-dashed border-gray-300 rounded-lg p-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Foto Jurusan (maks 2 MB)</label>
                @if ($jurusan->image_path)
                    <div class="flex items-center gap-4 mb-3">
                        <img src="{{ $jurusan->imageSrc() }}" alt="Foto saat ini" class="h-24 w-36 object-cover rounded-lg border">
                        <div class="text-xs">
                            <p class="font-medium text-gray-700">Foto jurusan saat ini</p>
                            <label class="mt-1.5 inline-flex items-center gap-1.5 cursor-pointer text-red-600 hover:text-red-800">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span>Hapus foto saat ini</span>
                            </label>
                        </div>
                    </div>
                @endif
                <input id="image" name="image" type="file" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-blue-600 file:text-white file:text-xs file:font-semibold hover:file:bg-blue-700">
                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Simpan Konten</button>
                <a href="{{ route('guru.jurusan.index') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</x-guru-layout>
