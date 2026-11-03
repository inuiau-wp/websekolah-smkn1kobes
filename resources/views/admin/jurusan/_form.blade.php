{{-- Form bersama create/edit jurusan --}}
@php($jurusan ??= null)

<form method="POST" action="{{ $jurusan ? route('admin.jurusan.update', $jurusan) : route('admin.jurusan.store') }}"
      enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($jurusan)
        @method('PATCH')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Jurusan <span class="text-red-500">*</span></label>
            <input id="name" name="name" type="text" required value="{{ old('name', $jurusan?->name) }}"
                   placeholder="Contoh: Teknik Komputer dan Jaringan"
                   class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Singkat <span class="text-red-500">*</span></label>
            <input id="code" name="code" type="text" required maxlength="10" value="{{ old('code', $jurusan?->code) }}"
                   placeholder="Contoh: TKJ"
                   class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 uppercase">
            <p class="mt-1 text-xs text-gray-400">Dipakai pada badge &amp; warna kartu. Maks 10 karakter.</p>
            @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna Aksen <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-2">
                <input id="color" name="color" type="color" required value="{{ old('color', $jurusan?->color ?? '#0d542b') }}"
                       class="h-10 w-14 !rounded-lg border border-gray-300 cursor-pointer p-1">
                <input id="color-text" name="color_text" type="text" disabled
                       value="{{ old('color', $jurusan?->color ?? '#0d542b') }}"
                       class="block flex-1 rounded-lg border-gray-300 font-mono text-sm disabled:bg-gray-100 disabled:text-gray-500">
            </div>
            <p class="mt-1 text-xs text-gray-400">Warna dipakai di kartu, badge, dan header detail jurusan.</p>
            @error('color') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="head_of_department_id" class="block text-sm font-medium text-gray-700 mb-1">Kepala Jurusan (Akun Guru)</label>
            <select id="head_of_department_id" name="head_of_department_id"
                    class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                <option value="">— Pilih dari Akun Guru Terdaftar —</option>
                @foreach ($teachers ?? [] as $teacher)
                    <option value="{{ $teacher->id }}" @selected(old('head_of_department_id', $jurusan?->head_of_department_id) == $teacher->id)>
                        {{ $teacher->name }} {{ $teacher->position ? '('.$teacher->position.')' : '' }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-400">Pilih akun guru agar pas foto dan nama kepala jurusan otomatis terhubung.</p>
            @error('head_of_department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="head_of_department" class="block text-sm font-medium text-gray-700 mb-1">Nama Kepala Jurusan (Cadangan / Teks Manual)</label>
        <input id="head_of_department" name="head_of_department" type="text" value="{{ old('head_of_department', $jurusan?->head_of_department) }}"
               placeholder="Contoh: Budi Santoso, S.Kom."
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        <p class="mt-1 text-xs text-gray-400">Dipakai jika belum memilih akun guru di atas.</p>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea id="description" name="description" rows="4" maxlength="2000"
                  placeholder="Deskripsi lengkap jurusan (tampil di halaman detail)..."
                  class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('description', $jurusan?->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="competencies" class="block text-sm font-medium text-gray-700 mb-1">Kompetensi Keahlian</label>
        <input id="competencies" name="competencies" type="text" value="{{ old('competencies', $jurusan?->competencies) }}"
               placeholder="Jaringan Komputer, Administrasi Sistem, Keamanan Jaringan (pisahkan dengan koma)"
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        <p class="mt-1 text-xs text-gray-400">Petunjuk: tulis beberapa kompetensi dipisah <strong>koma (,)</strong> — akan tampil sebagai tag di halaman detail.</p>
        @error('competencies') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <!-- Visi & Misi Jurusan -->
    <div class="border border-gray-200 rounded-xl p-4 bg-gray-50 space-y-4">
        <h2 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Visi &amp; Misi Jurusan</h2>
        <div>
            <label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi Jurusan</label>
            <textarea id="vision" name="vision" rows="3" maxlength="1000"
                      placeholder="Visi khusus jurusan ini (tampil di section Visi & Misi halaman detail)..."
                      class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('vision', $jurusan?->vision) }}</textarea>
            @error('vision') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Misi Jurusan (tulis per baris)</label>
            <textarea id="mission" name="mission" rows="5" maxlength="2000"
                      placeholder="1. Misi pertama&#10;2. Misi kedua&#10;3. Misi ketiga..."
                      class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('mission', $jurusan?->mission) }}</textarea>
            <p class="mt-1 text-xs text-gray-400">Setiap baris ditampilkan sebagai satu poin misi di halaman detail.</p>
            @error('mission') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (URL)</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $jurusan?->slug) }}"
               placeholder="Kosongkan untuk dibuat otomatis dari nama"
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900 font-mono text-sm">
        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="border border-dashed border-gray-300 rounded-lg p-4">
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Foto Jurusan (maks 2 MB)</label>
        @if ($jurusan?->image_path)
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ $jurusan->imageSrc() }}" alt="Foto saat ini" class="h-16 w-24 object-cover rounded-lg border">
                <div class="text-xs text-gray-500">
                    <p class="font-medium text-gray-700">Foto saat ini</p>
                    <p>Centang di bawah untuk menghapus tanpa mengganti.</p>
                    <label class="mt-1 inline-flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-red-600">Hapus foto saat ini</span>
                    </label>
                </div>
            </div>
        @endif
        <input id="image" name="image" type="file" accept="image/*"
               class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
        @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            {{ $jurusan ? 'Simpan Perubahan' : 'Tambah Jurusan' }}
        </button>
        <a href="{{ route('admin.jurusan.index') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
    </div>
</form>

<script>
    // Sinkronkan color picker dengan teks hex
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    colorInput?.addEventListener('input', () => colorText.value = colorInput.value);
</script>
