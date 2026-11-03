@php($layout ??= 'admin-layout')
@php($prefix ??= 'admin')

<x-dynamic-component :component="$layout">
    <div class="mb-6">
        <a href="{{ route($prefix.'.gallery.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Kelola Galeri</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Unggah Foto</h1>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        @include('admin.gallery._form', ['prefix' => $prefix])
    </div>
</x-dynamic-component>
