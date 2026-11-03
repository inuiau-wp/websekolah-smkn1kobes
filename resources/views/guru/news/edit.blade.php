<x-guru-layout>
    <div class="mb-6">
        <a href="{{ route('guru.news.index') }}" class="text-sm text-gray-500 hover:text-gray-900">← Kembali ke Berita Saya</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Berita — {{ $news->title }}</h1>
        <p class="text-gray-600 text-sm mt-1">Ubah konten atau status tayang berita Anda.</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-4xl">
        @include('guru.news._form', ['news' => $news, 'jurusanList' => $jurusanList])
    </div>
</x-guru-layout>
