<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Rapor Pendidikan {{ $rapor->year }}</h1>
            <p class="text-gray-600 mt-1">Perbarui data capaian tahun rilis {{ $rapor->year }}.</p>
        </div>
        @if ($rapor->is_published)
            <a href="{{ route('rapor.show', $rapor->year) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Lihat Halaman Publik
            </a>
        @endif
    </div>

    @include('admin.rapor._upload')

    @include('admin.rapor._form')
</x-admin-layout>
