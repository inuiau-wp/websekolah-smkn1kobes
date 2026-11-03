<x-admin-layout>
    <div class="mb-6 flex items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pesan Kontak</h1>
            <p class="text-gray-600 mt-1">Pesan dari pengunjung melalui halaman /kontak.</p>
        </div>
        <a href="{{ route('admin.kontak.index', ['unread_only' => false]) }}"
           class="text-sm text-gray-500 hover:text-gray-900">
            Tampilkan semua {{ $stats['total'] }}
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif

    <!-- Filter unread + stat -->
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ request()->query('unread_only') ? route('admin.kontak.index') : route('admin.kontak.index', ['unread_only' => 1]) }}"
           class="px-4 py-2 rounded-full text-xs font-semibold {{ request()->query('unread_only') ? 'bg-red-100 text-red-800' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
            Belum dibaca: {{ $stats['unread'] }}
        </a>
        <span class="px-4 py-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Total: {{ $stats['total'] }}</span>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Pengirim</th>
                    <th class="px-4 py-3">Subjek</th>
                    <th class="px-4 py-3">Isi (pratinjau)</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($messages as $m)
                    <tr class="hover:bg-gray-50 {{ $m->is_read ? 'bg-gray-50/50' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white font-medium text-xs" style="background-color: {{ $m->name[0] ?? '#6b7280' }}">{{ strtoupper($m->name[0] ?? '?') }}</div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $m->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $m->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900 truncate max-w-xs">{{ $m->subject ?? '(tanpa subjek)' }}</td>
                        <td class="px-4 py-3 text-gray-600 line-clamp-2">{{ $m->message }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         {{ $m->is_read ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $m->is_read ? 'Dibaca' : 'Baru' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if (! $m->is_read)
                                <form action="{{ route('admin.kontak.read', $m) }}" method="POST" class="inline-block mr-2">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-blue-600 hover:text-blue-800">Tandai dibaca</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.kontak.destroy', $m) }}" method="POST" class="inline-block"
                                  onsubmit="return confirm('Hapus pesan dari {{ $m->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            {{ request()->query('unread_only') ? 'Tidak ada pesan baru.' : 'Belum ada pesan kontak.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
</x-admin-layout>
