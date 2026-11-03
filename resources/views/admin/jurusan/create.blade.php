<x-admin-layout>
    <div class="mb-6">
        <a href="{{ route('admin.jurusan.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Kelola Jurusan</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Tambah Jurusan</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        @include('admin.jurusan._form')
    </div>
</x-admin-layout>
