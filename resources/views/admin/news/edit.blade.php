<x-admin-layout>
    <div class="mb-6">
        <a href="{{ route('admin.news.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Kelola Berita</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Berita</h1>
        <p class="text-sm text-gray-500 mt-1">Penulis: {{ $news->author?->name ?? '—' }} • Tayang: {{ $news->published_at?->translatedFormat('d F Y') ?? 'belum' }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-3xl">
        @include('admin.news._form', ['news' => $news])
    </div>
</x-admin-layout>
