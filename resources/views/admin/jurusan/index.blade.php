<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Jurusan</h1>
            <p class="text-gray-600 mt-1">Program keahlian yang direkrut siswa — muncul di navbar, kartu beranda, dan halaman publik.</p>
        </div>
        <a href="{{ route('admin.jurusan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Jurusan
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Jurusan</th>
                    <th class="px-4 py-3">Warna</th>
                    <th class="px-4 py-3">Kepala Jurusan</th>
                    <th class="px-4 py-3">Foto</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($jurusan as $j)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-white font-bold text-xs"
                                      style="background-color: {{ $j->color }}">{{ $j->code }}</span>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $j->name }}</p>
                                    <p class="text-xs text-gray-400">/jurusan/{{ $j->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-block h-4 w-4 rounded border border-gray-200 align-middle" style="background-color: {{ $j->color }}"></span>
                            <code class="ml-1 text-xs text-gray-500">{{ $j->color }}</code>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $kaprogName = $j->getHeadOfDepartmentName();
                                $kaprogPhoto = $j->getHeadOfDepartmentPhotoUrl();
                            @endphp
                            @if ($kaprogName)
                                <div class="flex items-center gap-2">
                                    @if ($kaprogPhoto)
                                        <img src="{{ $kaprogPhoto }}" alt="{{ $kaprogName }}" class="h-7 w-7 rounded-full object-cover border">
                                    @else
                                        <div class="h-7 w-7 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center text-[10px] font-bold">
                                            {{ strtoupper(substr($kaprogName, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="text-gray-900 font-medium text-xs">{{ $kaprogName }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($j->image_path)
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">Ada</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-gray-100 text-gray-400 text-xs">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('jurusan.show', $j->slug) }}" target="_blank"
                               class="text-xs font-medium text-gray-500 hover:text-gray-900 mr-3">Lihat</a>
                            <a href="{{ route('admin.jurusan.edit', $j) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                            <form action="{{ route('admin.jurusan.destroy', $j) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Hapus jurusan {{ $j->code }}? Berita yang terelasi akan berubah jadi berita umum.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada jurusan. Tambah pertama lewat tombol di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
