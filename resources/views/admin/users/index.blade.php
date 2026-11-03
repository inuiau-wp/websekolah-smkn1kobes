<x-admin-layout>
    <div class="mb-6 flex items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Akun</h1>
            <p class="text-gray-600 mt-1">Data login guru, admin, dan siswa. Pendaftaran publik ditutup — akun dibuat di sini.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Akun
        </a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-sm text-emerald-800">{{ session('status') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filter per role + stat -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach (['' => 'Semua', 'admin' => 'Admin', 'guru' => 'Guru', 'siswa' => 'Siswa'] as $val => $label)
            <?php $count = $val === '' ? $stats['total'] : $stats[$val] ?? 0; ?>
            <a href="{{ request()->query('role') === $val ? route('admin.users.index') : route('admin.users.index', ['role' => $val]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-medium {{ request()->query('role') === $val ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ $label }} ({{ $count }})
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">Pengguna</th>
                    <th class="px-4 py-3">Jabatan & NIP</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($u->photo_url)
                                    <img src="{{ $u->photo_url }}" alt="{{ $u->name }}" class="h-10 w-10 shrink-0 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-800 text-white font-bold text-xs">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900 leading-snug">{{ $u->name }}</p>
                                    @if ($u->nip)
                                        <p class="text-[11px] text-gray-500 font-mono">NIP: {{ $u->nip }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if ($u->position)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->position === 'Kepala Sekolah' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $u->position }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                         {{ $u->role === 'admin' ? 'bg-red-100 text-red-800' : ($u->role === 'guru' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if (! $u->is(auth()->user()))
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-3">Edit</a>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block"
                                      onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            @else
                                <a href="{{ route('admin.users.edit', $u) }}"
                                   class="text-xs font-medium text-blue-600 hover:text-blue-800 mr-2">Edit</a>
                                <span class="text-xs text-gray-400">(Anda)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">
                            Tidak ada akun pada filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</x-admin-layout>
