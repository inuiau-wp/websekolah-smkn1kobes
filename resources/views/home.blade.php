<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang di SMKN 1 Kotabesi</h2>
    </x-slot>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gray-900 text-white py-16 lg:py-24">
        <div class="absolute inset-0">
            @if (! empty($settings['hero_image']))
                <img src="{{ asset('storage/'.$settings['hero_image']) }}"
                     alt="Foto latar {{ $schoolProfile->school_name }}"
                     class="w-full h-full object-cover opacity-60">
            @else
                <img src="https://images.unsplash.com/photo-1509062322274-3e7ac9ff7d3b?w=1920&q=80"
                     alt="Kegiatan Sekolah"
                     class="w-full h-full object-cover opacity-60">
            @endif
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            @if ($schoolProfile->logo_url)
                <img src="{{ $schoolProfile->logo_url }}" alt="{{ $schoolProfile->school_name }}" class="h-16 md:h-20 w-auto mx-auto mb-6 object-contain drop-shadow-md">
            @endif
            <span class="inline-block px-4 py-1.5 bg-green-600/20 text-green-100 rounded-full text-sm font-medium mb-6 animate-pulse">
                {{ $settings['hero_badge'] ?? 'Penerimaan Peserta Didik Baru Terbuka' }}
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                {!! ($settings['hero_title'] ?? 'Pendidikan Berkualitas<br>untuk Generasi Emas') !!}
            </h1>
            <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
                SMKN 1 Kotabesi menyediakan 3 jurusan unggulan dengan kurikulum modern,
                fasilitas lengkap, dan prestasi terbaik di bidangnya.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#jurusan" class="px-8 py-3 bg-white text-gray-900 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                    Lihat Jurusan
                </a>
                <a href="#kontak" class="px-8 py-3 border border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-colors">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Carousel / Slider -->
    <section class="py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $settings['section_carousel_title'] ?? 'Kegiatan Terkini' }}</h2>
                <p class="text-gray-600">Berita dan kegiatan terbaru dari sekolah</p>
            </div>

            @if ($slides->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada slide kegiatan.</p>
                    <p class="text-gray-400 text-sm mt-1">(Konten akan diunggah melalui dashboard admin)</p>
                </div>
            @else
            <div class="relative overflow-hidden rounded-xl border border-gray-200 shadow-sm bg-white"
                 x-data="{
                    activeIndex: 0,
                    totalSlides: {{ $slides->count() }},
                    next() { this.activeIndex = (this.activeIndex + 1) % this.totalSlides; },
                    prev() { this.activeIndex = (this.activeIndex - 1 + this.totalSlides) % this.totalSlides; },
                    goTo(i) { this.activeIndex = i; }
                 }"
                 x-init="timer = setInterval(() => next(), 5000)"
                 @mouseenter="clearInterval(timer)"
                 @mouseleave="timer = setInterval(() => next(), 5000)">

                <!-- Track -->
                <div class="flex transition-transform duration-500 ease-in-out"
                     :style="'transform: translateX(-' + activeIndex * 100 + '%)'">
                    @foreach($slides as $slide)
                        <div class="shrink-0 w-full py-8 px-4 text-center">
                            <img src="{{ $slide->imageSrc() }}" alt="{{ $slide->title }}" class="w-32 h-32 mx-auto mb-4 rounded-full object-cover border-4 border-gray-100">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $slide->title }}</h3>
                            @if ($slide->description)
                                <p class="text-gray-600 max-w-md mx-auto">{{ $slide->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Prev / Next buttons -->
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <button @click="prev()" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/80 text-gray-700 shadow hover:bg-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                </div>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <button @click="next()" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/80 text-gray-700 shadow hover:bg-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Navigation dots -->
                <div class="flex justify-center gap-2 pb-6">
                    @foreach($slides as $index => $slide)
                        <button @click="goTo({{ $index }})" aria-label="Slide {{ $index + 1 }}"
                                class="h-2.5 rounded-full transition-all"
                                :class="activeIndex === {{ $index }} ? 'w-8 bg-gray-900' : 'w-2.5 bg-gray-300'"></button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Statistik Sekolah -->
    <section class="py-12 lg:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $settings['section_statistics_title'] ?? 'Tentang Kami' }}</h2>
                <p class="text-gray-600">Sebuah angka yang menggambarkan keunggulan kami</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @forelse ($statistics as $stat)
                    <div class="text-center bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="text-4xl font-bold text-gray-900 mb-2">{{ $stat->value }}</div>
                        <div class="text-sm text-gray-600">{{ $stat->label }}</div>
                    </div>
                @empty
                    <div class="col-span-full bg-white border border-dashed border-gray-300 rounded-xl p-8 text-center text-gray-400 text-sm">
                        Statistik akan diisi melalui dashboard admin.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Kartu 3 Jurusan -->
    <section id="jurusan" class="py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Jurusan Unggulan</h2>
                <p class="text-gray-600">Pilih jurusan yang sesuai dengan minat dan bakat Anda</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($jurusan as $j)
                    <div class="jurusan-card relative bg-white rounded-2xl shadow-sm hover:shadow-xl overflow-hidden transition-all duration-300 group border border-gray-100 flex flex-col">
                        <!-- Header berwarna sesuai jurusan -->
                        <div class="relative p-8 pb-10 text-white" style="background: linear-gradient(135deg, {{ $j->color }} 0%, {{ $j->color }}BB 100%);">
                            <span class="absolute top-4 right-4 px-3 py-1 bg-white/25 backdrop-blur rounded-full text-xs font-bold tracking-wide">{{ $j->code }}</span>
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4 bg-white/20">
                                @if($j->code === 'ATP')
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                @elseif($j->code === 'TKJ')
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold leading-snug">{{ $j->name }}</h3>
                        </div>

                        <div class="p-6 flex flex-col flex-1 -mt-5 bg-white rounded-t-2xl">
                            <p class="text-gray-600 text-sm mb-5 line-clamp-3">{{ $j->description ?? 'Deskripsi jurusan akan diperbarui ol�h admin.' }}</p>
                            <div class="flex flex-wrap gap-2 mb-6">
                                @php $comps = array_filter(array_map('trim', explode(',', $j->competencies ?? ''))) @endphp
                                @forelse($comps as $comp)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                                          style="background-color: {{ $j->color }}15; color: {{ $j->color }};">
                                        {{ $comp }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Kompetensi belum diisi</span>
                                @endforelse
                            </div>
                            <a href="{{ route('jurusan.show', $j->slug) }}"
                               class="mt-auto block w-full px-4 py-2.5 text-center rounded-lg font-semibold transition-colors text-white hover:opacity-90"
                               style="background-color: {{ $j->color }};">
                                Selengkapnya →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Berita & Pengumuman -->
    <section id="berita" class="py-12 lg:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Berita & Pengumuman</h2>
                    <p class="text-gray-600">Informasi terbaru dari sekolah</p>
                </div>
                <a href="{{ route('news.index') }}" class="text-sm font-medium text-gray-900 hover:text-gray-600">Lihat semua →</a>
            </div>

            @if ($beritas->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada berita terbaru.</p>
                    <p class="text-gray-400 text-sm mt-1">(Konten akan dimasukkan melalui dashboard admin)</p>
                </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($beritas as $item)
                    <article class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-all">
                        @if ($item->cover_image)
                            <a href="{{ route('news.show', $item->slug) }}">
                                <img src="{{ $item->cover_url }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                            </a>
                        @else
                            <a href="{{ route('news.show', $item->slug) }}" class="block w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200"></a>
                        @endif
                        <div class="p-6">
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                             {{ match ($item->category) {
                                                 'announcement' => 'bg-amber-100 text-amber-800',
                                                 'agenda' => 'bg-purple-100 text-purple-800',
                                                 'materi' => 'bg-indigo-100 text-indigo-800',
                                                 'tutorial' => 'bg-cyan-100 text-cyan-800',
                                                 default => 'bg-emerald-100 text-emerald-800',
                                             } }}">
                                    {{ $item->categoryLabel() }}
                                </span>
                                @if ($item->jurusan)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold text-white"
                                          style="background-color: {{ $item->jurusan->color }}">
                                        {{ $item->jurusan->code }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug">
                                <a href="{{ route('news.show', $item->slug) }}" class="hover:text-gray-700">{{ $item->title }}</a>
                            </h3>
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $item->excerpt ?? 'Ringkasan akan diperbarui oleh admin.' }}</p>
                            <a href="{{ route('news.show', $item->slug) }}" class="text-sm font-medium text-gray-900 hover:text-gray-600 flex items-center gap-1">
                                Baca selengkapnya
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Galeri Foto -->
    <section id="galeri" class="py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Galeri Foto</h2>
                <p class="text-gray-600">Kegiatan dan momen terbaik sekolah</p>
            </div>

            @if ($galleries->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Belum ada foto galeri.</p>
                    <p class="text-gray-400 text-sm mt-1">(Foto akan diunggah melalui dashboard admin)</p>
                </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($galleries as $photo)
                    <a href="{{ route('gallery.index') }}"
                       class="aspect-square bg-gray-200 rounded-lg overflow-hidden group relative" title="{{ $photo->title }}">
                        <img src="{{ $photo->imageSrc() }}" alt="{{ $photo->title }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300 opacity-80 group-hover:opacity-100">
                    </a>
                @endforeach
            </div>
            <div class="text-center mt-8">
                <a href="{{ route('gallery.index') }}" class="inline-block px-6 py-2.5 border border-gray-300 rounded-lg font-medium hover:bg-gray-50 transition">
                    Lihat semua foto →
                </a>
            </div>
            @endif
        </div>
    </section>

    <!-- Testimoni -->
    <section class="py-12 lg:py-16 bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-white mb-2">{{ $settings['section_testimonials_title'] ?? 'Testimoni' }}</h2>
                <p class="text-gray-400">Apa kata mereka tentang kami</p>
            </div>

            @if ($testimonials->isEmpty())
                <div class="bg-gray-800 border border-dashed border-gray-700 rounded-xl p-12 text-center">
                    <p class="text-gray-400">Testimoni akan diisi melalui dashboard admin.</p>
                </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $item)
                    <div class="bg-gray-800 p-8 rounded-xl border border-gray-700">
                        <div class="flex gap-1 text-yellow-400 mb-4">
                            ★ ★ ★ ★ ★
                        </div>
                        <p class="text-gray-300 italic mb-6">"{{ $item->content }}"</p>
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->photoSrc() }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-full object-cover">
                            <div>
                                <div class="font-bold text-white text-sm">{{ $item->name }}</div>
                                <div class="text-xs text-gray-400">{{ $item->role }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <!-- Partner / Mitra -->
    <section class="py-12 lg:py-16 bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $settings['section_partners_title'] ?? 'Partner & Mitra' }}</h2>
                <p class="text-gray-600">Kerja sama dengan instansi terpercaya</p>
            </div>

            @if ($partners->isEmpty())
                <div class="bg-white border border-dashed border-gray-300 rounded-xl p-12 text-center">
                    <p class="text-gray-500">Partner akan diisi melalui dashboard admin.</p>
                </div>
            @else
            <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                @foreach($partners as $partner)
                    <div class="flex items-center justify-center h-16">
                        @if ($partner->logoSrc())
                            <img src="{{ $partner->logoSrc() }}" alt="{{ $partner->name }}" class="h-10 object-contain">
                        @else
                            <span class="text-gray-400 font-semibold text-lg">{{ $partner->name }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

</x-app-layout>
