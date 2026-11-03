<x-admin-layout>
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Kelola Akun</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Akun Pengguna</h1>
        <p class="text-sm text-gray-500 mt-1">Buat akun untuk Guru / Tenaga Pendidik, Admin, atau Siswa.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Role Selector -->
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role Akun <span class="text-red-500">*</span></label>
                <select id="role" name="role" required onchange="toggleGuruFields(this.value)"
                        class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                    <option value="guru" @selected(old('role', 'guru') === 'guru')>Guru / Tenaga Pendidik</option>
                    <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                    <option value="siswa" @selected(old('role') === 'siswa')>Siswa</option>
                </select>
                <p class="mt-1 text-xs text-gray-400">Pendaftaran publik ditutup; semua akun dikelola oleh admin.</p>
                @error('role') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Nama & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" required
                           value="{{ old('name') }}"
                           placeholder="Contoh: Budi Santoso, S.Pd., M.T."
                           class="block w-full rounded-lg border-gray-300 @error('name') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email') }}"
                           placeholder="Contoh: budi@smkn1kotabesi.sch.id"
                           class="block w-full rounded-lg border-gray-300 @error('email') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Bagian Khusus Guru/Pendidik (Jabatan, NIP, Bio) -->
            <div id="guru-fields" class="space-y-5 p-5 bg-gray-50 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Data Guru & Tenaga Kependidikan</h3>
                    <span class="text-xs text-blue-600 font-medium">Tampil di Halaman Profil Sekolah</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Tugas</label>
                        <input id="position" name="position" type="text" list="position-list"
                               value="{{ old('position') }}"
                               placeholder="Pilih atau ketik jabatan..."
                               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        <datalist id="position-list">
                            <option value="Kepala Sekolah">
                            <option value="Waka Kurikulum">
                            <option value="Waka Kesiswaan">
                            <option value="Waka HUMAS & Hubungan Industri">
                            <option value="Waka Sarana & Prasarana">
                            <option value="Kepala Program ATP">
                            <option value="Kepala Program TKJ">
                            <option value="Kepala Program AKL">
                            <option value="Guru Produktif ATP">
                            <option value="Guru Produktif TKJ">
                            <option value="Guru Produktif AKL">
                            <option value="Guru Mata Pelajaran Umum">
                            <option value="Kepala Tata Usaha">
                            <option value="Staff Tata Usaha">
                        </datalist>
                        <p class="mt-1 text-xs text-gray-400">Pilih dari saran atau ketik jabatan khusus. Jika dipilih <strong>Kepala Sekolah</strong>, otomatis jadi data pimpinan di website.</p>
                        @error('position') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-700 mb-1">NIP (Nomor Induk Pegawai)</label>
                        <input id="nip" name="nip" type="text"
                               value="{{ old('nip') }}"
                               placeholder="Contoh: 19780101 200501 1 002"
                               class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">
                        <p class="mt-1 text-xs text-gray-400">Opsional. Kosongkan jika belum memiliki NIP.</p>
                        @error('nip') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Singkat / Sambutan (opsional)</label>
                    <textarea id="bio" name="bio" rows="2"
                              placeholder="Misal: Lulusan S1 Pendidikan TI UNY atau kata sambutan singkat jika Kepala Sekolah..."
                              class="block w-full rounded-lg border-gray-300 focus:border-gray-900 focus:ring-gray-900">{{ old('bio') }}</textarea>
                    @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Upload Foto Profil (Opsional) -->
            <div class="border border-dashed border-gray-300 rounded-xl p-5 bg-white">
                <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Profil / Pas Foto (Opsional)</label>
                <p class="text-xs text-gray-500 mb-3">Guru juga dapat mengunggah foto sendiri nanti setelah login. Format JPG, PNG, atau WEBP (maksimal 2 MB).</p>
                <input id="photo" name="photo" type="file" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800">
                @error('photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <!-- Kata Sandi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password" name="password" type="password" required minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="block w-full rounded-lg border-gray-300 @error('password') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                           placeholder="Ulangi kata sandi"
                           class="block w-full rounded-lg border-gray-300 @error('password_confirmation') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                    @error('password_confirmation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <button type="submit" class="px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                    Buat Akun
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-gray-200 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function toggleGuruFields(role) {
            const el = document.getElementById('guru-fields');
            if (role === 'siswa') {
                el.classList.add('hidden');
            } else {
                el.classList.remove('hidden');
            }
        }
        // Init state on load
        toggleGuruFields(document.getElementById('role').value);
    </script>
</x-admin-layout>
