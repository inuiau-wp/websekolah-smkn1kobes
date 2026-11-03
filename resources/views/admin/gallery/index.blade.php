@php($layout ??= 'admin-layout')
@php($prefix ??= 'admin')

<x-dynamic-component :component="$layout">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Galeri</h1>
            <p class="text-gray-600 mt-1">Foto kegiatan & fasilitas — tampil di halaman /galeri dan section beranda.</p>
        </div>
        <a href="{{ route($prefix.'.gallery.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Unggah Foto
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

    <!-- Statistik + filter kategori -->
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route($prefix.'.gallery.index') }}"
           class="px-4 py-2 rounded-full text-xs font-semibold {{ request()->query('category') ? 'bg-white text-gray-700 border border-gray-200' : 'bg-gray-900 text-white' }}">
            Semua ({{ $stats['total'] }})
        </a>
        <span class="px-4 py-2 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Upload dashboard: {{ $stats['admin_upload'] }}</span>

        @foreach ($categories as $cat)
            <a href="{{ request()->query('category') === $cat ? route($prefix.'.gallery.index') : route($prefix.'.gallery.index', ['category' => $cat]) }}"
               class="px-4 py-2 rounded-full text-xs font-medium {{ request()->query('category') === $cat ? 'bg-gray-900 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                {{ ucfirst($cat) }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($photos as $photo)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden group">
                <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                    <img src="{{ $photo->imageSrc() }}" alt="{{ $photo->title }}" class="w-full h-full object-cover" loading="lazy">
                    @if ($photo->category)
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-black/60 text-white text-[11px] font-medium backdrop-blur">
                            {{ ucfirst($photo->category) }}
                        </span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="font-semibold text-sm text-gray-900 truncate" title="{{ $photo->title }}">{{ $photo->title }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">
                        {{ is_string($photo->created_at) ? $photo->created_at : $photo->created_at?->translatedFormat('d M Y') }}
                        @if ($photo->uploadedBy)
                            • {{ $photo->uploadedBy->name }}
                        @endif
                    </p>
                    <div class="mt-2 flex items-center gap-3 text-xs">
                        <a href="{{ route($prefix.'.gallery.edit', $photo) }}" class="font-medium text-blue-600 hover:text-blue-800">Edit</a>
                        <form action="{{ route($prefix.'.gallery.destroy', $photo) }}" method="POST"
                              onsubmit="return confirm('Hapus foto {{ $photo->title }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center text-gray-400">
                Belum ada foto pada filter ini. Unggah pertama lewat tombol "Unggah Foto".
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $photos->links() }}
    </div>
</x-dynamic-component>
