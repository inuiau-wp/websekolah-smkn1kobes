{{-- Form bersama create/edit galeri --}}
@php($photo ??= null)
@php($categories ??= collect())
@php($prefix ??= 'admin')

<form method="POST" action="{{ $photo ? route($prefix.'.gallery.update', $photo) : route($prefix.'.gallery.store') }}"
      enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($photo)
        @method('PATCH')
    @endif

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Foto <span class="text-red-500">*</span></label>
        <input id="title" name="title" type="text" required value="{{ old('title', $photo?->title) }}"
               placeholder="{{ $photo ? 'Judul foto saat ini' : 'Contoh: Upacara Bendera Senin (berlaku semua foto jika multi-upload)' }}"
               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
            <input id="category" name="category" type="text" list="category-list" value="{{ old('category', $photo?->category) }}"
                   placeholder="Contoh: Kegiatan / Fasilitas / Prestasi"
                   class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
            <datalist id="category-list">
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}"></option>
                @endforeach
            </datalist>
            <p class="mt-1 text-xs text-gray-400">Pilih dari daftar atau tulis kategori baru — filter publik dibuat otomatis.</p>
            @error('category') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
            <input id="description" name="description" type="text" value="{{ old('description', $photo?->description) }}"
                   placeholder="Keterangan singkat foto"
                   class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
        </div>
    </div>

    <div class="border border-dashed border-gray-300 rounded-lg p-4">
        <label for="{{ $photo ? 'image' : 'images' }}" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $photo ? 'Ganti Foto (maks 2 MB)' : 'Foto (bisa pilih beberapa sekaligus, masing-masing maks 2 MB)' }}
        </label>

        @if ($photo?->image_path)
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ $photo->imageSrc() }}" alt="Foto saat ini" class="h-24 w-32 object-cover rounded-lg border">
                <p class="text-xs text-gray-500"><span class="font-medium text-gray-700">Foto saat ini</span> — unggah gambar baru untuk mengganti.</p>
            </div>
        @endif

        @if ($photo)
            <input id="image" name="image" type="file" accept="image/*" class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
        @else
            <input id="images" name="images[]" type="file" accept="image/*" multiple
                   class="block w-full rounded-lg border-gray-300 file:mr-3 file:px-4 file:py-2 file:rounded-md file:border-0 file:bg-gray-900 file:text-white file:text-sm hover:file:bg-gray-800">
            <p class="mt-1 text-xs text-gray-400">Setiap berkas diunggah menjadi satu entri foto terpisah dengan judul di atas.</p>
        @endif

        @error($photo ? 'image' : 'images.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            {{ $photo ? 'Simpan Perubahan' : 'Unggah ke Galeri' }}
        </button>
        <a href="{{ route($prefix.'.gallery.index') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
    </div>
</form>
