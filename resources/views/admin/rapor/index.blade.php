<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Rapor Pendidikan</h1>
            <p class="text-gray-600 mt-1">Publikasi etis capaian per tahun rilis — muncul di menu navbar "Rapor Pendidikan" (dropdown Tahun Rapor).</p>
        </div>
        <a href="{{ route('admin.rapor-pendidikan.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Rapor
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
                    <th class="px-4 py-3">Tahun</th>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">KPI / Indikator</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($rapor as $r)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="inline-flex h-8 min-w-8 items-center justify-center rounded-lg bg-teal-600 px-2 text-white font-bold text-xs">{{ $r->year }}</span>
                        </td>
                        <td class="px-4 py-3 max-w-md">
                            <p class="font-semibold text-gray-900 line-clamp-1">{{ $r->title }}</p>
                            <p class="text-xs text-gray-400">/rapor-pendidikan/{{ $r->year }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                            {{ count($r->data['kpi'] ?? []) }} KPI · {{ count($r->data['indicators'] ?? []) }} indikator
                        </td>
                        <td class="px-4 py-3">
                            @if ($r->is_published)
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-semibold">Terpublikasi</span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-semibold">Draf</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if ($r->is_published)
                                <a href="{{ route('rapor.show', $r->year) }}" target="_blank"
                                   class="text-xs font-medium text-gray-500 hover:text-gray-900 mr-3">Lihat</a>
                            @endif
                            <a href="{{ route('admin.rapor-pendidikan.edit', $r) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                            <form action="{{ route('admin.rapor-pendidikan.destroy', $r) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Hapus Rapor Pendidikan tahun {{ $r->year }}? Halaman publiknya akan ikut hilang.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada rapor. Tambah tahun rapor pertama lewat tombol di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>
