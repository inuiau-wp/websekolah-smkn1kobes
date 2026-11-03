<x-app-layout>
    <!-- Header halaman -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">Galeri Foto</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Galeri Foto</h1>
            <p class="text-gray-600 mt-1">Dokumentasi kegiatan dan momen terbaik sekolah</p>
        </div>
    </header>

    <section class="py-12 lg:py-16" x-data="{ lightbox: null }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($photos->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada foto di galeri.</p>
                    <p class="text-gray-400 text-sm mt-1">(Foto akan diunggah melalui dashboard admin)</p>
                </div>
            @else
                <!-- Ringkasan kategori (tanda kategori tersedia, belum jadi filter aktif) -->
                @if ($categories->whereNotNull('category')->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-8">
                        @foreach ($categories as $c)
                            <span class="px-4 py-2 rounded-full text-sm font-medium bg-white text-gray-700 border border-gray-200">
                                {{ $c->category ?? 'Tanpa Kategori' }} ({{ $c->total }})
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Grid foto -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($photos as $photo)
                        <button type="button" @click="lightbox = {{ $loop->index }}"
                                class="group relative aspect-square bg-gray-200 rounded-lg overflow-hidden focus:outline-none focus:ring-2 focus:ring-gray-900">
                            <img src="{{ $photo->imageSrc() }}" alt="{{ $photo->title }}"
                                 class="w-full h-full object-cover transition-transform duration-300 opacity-80 group-hover:opacity-100 group-hover:scale-110">
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <p class="text-white text-sm font-medium truncate">{{ $photo->title }}</p>
                                @if ($photo->category)
                                    <p class="text-white/70 text-xs">{{ $photo->category }}</p>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-10">
                    {{ $photos->links() }}
                </div>

                <!-- Lightbox (Alpine.js) -->
                <div x-show="lightbox !== null" x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90"
                     @keydown.escape.window="lightbox = null"
                     @click.self="lightbox = null">
                    <button type="button" @click="lightbox = null"
                            class="absolute top-4 right-4 text-white/70 hover:text-white p-2" aria-label="Tutup">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <button type="button" x-show="lightbox > 0" @click="lightbox--"
                            class="absolute left-4 text-white/70 hover:text-white p-2" aria-label="Sebelumnya">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button type="button" x-show="lightbox < {{ $photos->count() - 1 }}" @click="lightbox++"
                            class="absolute right-4 text-white/70 hover:text-white p-2" aria-label="Selanjutnya">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <template x-if="lightbox !== null">
                        <figure class="max-w-4xl w-full text-center"
                                x-data="{
                                    photos: @js($photos->map(fn ($p) => ['src' => $p->imageSrc(), 'title' => $p->title, 'category' => $p->category])),
                                }">
                            <img :src="photos[lightbox].src" :alt="photos[lightbox].title"
                                 class="w-full max-h-[80vh] object-contain rounded-lg">
                            <figcaption class="mt-3 text-white">
                                <span class="font-medium" x-text="photos[lightbox].title"></span>
                                <span class="block text-white/60 text-sm" x-text="photos[lightbox].category"></span>
                            </figcaption>
                        </figure>
                    </template>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
