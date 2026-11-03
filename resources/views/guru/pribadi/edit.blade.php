<x-guru-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Halaman Pribadi Saya</h1>
        <p class="text-gray-600 mt-1">
            Kustomisasi halaman yang tampil publik di <code>/guru/{{ $guru->id }}</code> —
            <a href="{{ route('guru.public.show', $guru) }}" target="_blank" class="text-blue-600 underline">Pratinjau halaman</a>
        </p>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('guru.pribadi.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PATCH')

            <!-- Headline -->
            <div>
                <label for="headline" class="block text-sm font-medium text-gray-700 mb-1">Gelar / Headline</label>
                <input id="headline" name="headline" type="text" value="{{ old('headline', $guruProfile->headline) }}"
                       placeholder="Contoh: Guru Produktif TKJ — Ahli Jaringan & Mikrotik"
                       class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
                @error('headline') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- CV -->
            <div>
                <label for="cv_content" class="block text-sm font-medium text-gray-700 mb-1">Curriculum Vitae (isi sendiri)</label>
                <textarea id="cv_content" name="cv_content" rows="10" maxlength="10000"
                          placeholder="Riwayat pendidikan:&#10;- S1 Pendidikan Teknik Elektronika — Universitas Padang&#10;- S2 ...&#10;&#10;Riwayat organisasi:&#10;- Bendahara TIM MIPA ...&#10;&#10;Pengalaman mengajar:&#10;- ...&#10;&#10;Sertifikasi:&#10;- ..."
                          class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">{{ old('cv_content', $guruProfile->cv_content) }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Tulis CV bebas sesuai keinginan — setiap baris &amp; spasi akan dipertahankan. Baris kosong membuat paragraf baru.</p>
                @error('cv_content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Prestasi -->
            <div>
                <label for="prestasi" class="block text-sm font-medium text-gray-700 mb-1">Prestasi (tulis per baris)</label>
                <textarea id="prestasi" name="prestasi" rows="6"
                          placeholder="Contoh:&#10;Juara 1 LKS Nasional bidang IT Network (2019)&#10;Sertifikasi MTCNA/MTCRE&#10;Pelatih tim LKS tingkat provinsi..."
                          class="block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">{{ old('prestasi', $guruProfile->prestasi) }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Setiap baris tampil satu poin prestasi di halaman publik Anda.</p>
                @error('prestasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">Simpan Halaman</button>
                <a href="{{ route('guru.dashboard') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>

    <!-- Info turunan -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl mt-6">
        <h2 class="font-bold text-gray-900 mb-2">Konten yang otomatis tampil di halaman publik</h2>
        <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
            <li>Biodata: nama, jabatan, bio &amp; foto (dari menu <a href="{{ route('profile.edit') }}" class="text-blue-600 underline">Profil &amp; Foto</a>). <strong class="text-gray-800">NIP tidak pernah tampil publik.</strong></li>
            <li>Link media sosial (dari menu Profil &amp; Foto).</li>
            <li>Daftar artikel/modul: berita dipublikasikan oleh Anda (dari menu <a href="{{ route('guru.news.index') }}" class="text-blue-600 underline">Berita Saya</a>).</li>
        </ul>
    </div>
</x-guru-layout>
