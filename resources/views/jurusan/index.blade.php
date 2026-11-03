<x-app-layout>
    <!-- Page Heading -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">Jurusan</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Jurusan Unggulan</h1>
            <p class="text-gray-600 mt-1">Pilih jurusan yang sesuai dengan minat dan bakat Anda</p>
        </div>
    </header>

    <!-- Jurusan Grid -->
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($jurusan->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada jurusan yang tersedia. <span class="text-gray-400 text-sm">(Konten akan dimasukkan melalui dashboard admin)</span></p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach ($jurusan as $j)
                        <div class="jurusan-card bg-white p-8 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all">
                            <!-- Foto jurusan (dari admin) atau placeholder -->
                            @if ($j->image_path)
                                <img src="{{ $j->imageSrc() }}" alt="{{ $j->name }}" class="w-full h-40 rounded-lg mb-6 object-cover">
                            @else
                                <div class="h-40 rounded-lg mb-6 flex items-center justify-center"
                                     style="background-color: {{ $j->color }}15; border: 2px dashed {{ $j->color }}40;">
                                    <div class="text-center px-4">
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-lg text-white font-bold text-lg mb-2"
                                              style="background-color: {{ $j->color }}">{{ $j->code }}</span>
                                        <p class="text-xs text-gray-400">Foto jurusan<br>(placeholder)</p>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-white text-xs font-semibold"
                                      style="background-color: {{ $j->color }}">{{ $j->code }}</span>
                            </div>

                            <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $j->name }}</h2>
                            <p class="text-gray-600 text-sm mb-6 line-clamp-3">
                                {{ $j->description ?? 'Deskripsi jurusan akan diperbarui oleh admin.' }}
                            </p>

                            <a href="{{ route('jurusan.show', $j->slug) }}"
                               class="block w-full px-4 py-2 text-white text-center rounded-lg font-medium hover:opacity-90 transition-opacity"
                               style="background-color: {{ $j->color }}">
                                Lihat Detail
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
