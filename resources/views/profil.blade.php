<x-app-layout>
    {{-- Desain Fase 8j: filter antislop (core + ui + human + layoutmobile).
         Dial: ENERGY 2 / RHYTHM 2 / MOTION 1. Palet: ink + kertas + satu aksen teal. --}}
    @php
        $coverUrl = $profil->cover_url;
        $misiLines = $profil->mission ? str($profil->mission)->explode("\n")->filter() : collect();
        $kepsekPhoto = $headmasterUser?->photo_url;
        $kepsekName = $headmasterUser?->name ?? $profil->headmaster ?? 'Kepala Sekolah';
        $kepsekBio = $headmasterUser?->bio;
    @endphp

    {{-- Hero: panel teal solid saat tanpa cover; cover nyata dengan scrim gelap saat ada --}}
    <section class="relative bg-teal-800 text-white overflow-hidden">
        @if ($coverUrl)
            <div class="absolute inset-0">
                <img src="{{ $coverUrl }}" alt="Halaman depan {{ $profil->school_name }}"
                     fetchpriority="high" decoding="async"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'">
                <div class="absolute inset-0 bg-gray-900/70"></div>
            </div>
        @endif

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-20">
            <nav class="text-sm" aria-label="Breadcrumb">
                <ol class="flex items-center gap-1.5 text-white/80">
                    <li><a href="{{ route('home') }}" class="hover:text-white hover:underline underline-offset-4">Beranda</a></li>
                    <li class="text-white/40" aria-hidden="true">/</li>
                    <li class="text-white font-medium" aria-current="page">Profil Sekolah</li>
                </ol>
            </nav>

            <div class="mt-8">
                @if ($profil->logo_url)
                    <img src="{{ $profil->logo_url }}" alt="Logo {{ $profil->school_name }}"
                         width="96" height="96"
                         class="h-24 w-24 object-contain bg-white rounded-xl p-2.5 shadow-lg mb-6">
                @endif
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight mb-3">{{ $profil->school_name }}</h1>
                <p class="max-w-2xl text-lg text-white/85 leading-relaxed">{{ $profil->tagline ?? 'Kenali lebih dekat sekolah kami.' }}</p>
                <span class="inline-block mt-4 text-sm text-teal-200 border-t border-white/25 pt-2">Profil, visi misi, dan dewan sekolah</span>
            </div>
        </div>
    </section>

    {{-- Visi: blok focal satu kalimat besar (tujuan: satu titik fokus per layar) --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <p class="text-sm font-semibold text-teal-700 mb-3">Visi</p>
        @if ($profil->vision)
            <p class="text-2xl md:text-3xl font-medium leading-snug text-gray-900 max-w-3xl">{{ $profil->vision }}</p>
        @else
            <p class="text-lg italic text-gray-500">Visi sekolah akan ditetapkan melalui dashboard admin.</p>
        @endif
    </section>

    {{-- Misi: daftar bernomor, hierarki lewat tipografi bukan kartu+ikon --}}
    <section class="bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <p class="text-sm font-semibold text-teal-700">Misi</p>
                @if ($misiLines->isNotEmpty())
                    <ol class="lg:col-span-2 space-y-5 max-w-3xl">
                        @foreach ($misiLines as $line)
                            <li class="flex gap-4">
                                <span class="font-bold text-teal-700 tabular-nums" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                                <span class="text-gray-700 leading-relaxed">{{ $line }}</span>
                            </li>
                        @endforeach
                    </ol>
                @else
                    <p class="lg:col-span-2 italic text-gray-500">Misi sekolah akan diisi melalui dashboard admin.</p>
                @endif
            </div>
        </div>
    {{-- Sejarah: ukur baca editorial, satu kolom nyaman dibaca --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="max-w-3xl">
            <p class="text-sm font-semibold text-teal-700 mb-3">Sejarah</p>
            @if ($profil->history)
                <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $profil->history }}
                </div>
            @else
                <div class="border border-dashed border-gray-300 rounded-xl p-8">
                    <p class="text-gray-600 font-medium">Sejarah sekolah belum diisi.</p>
                    <p class="text-gray-500 text-sm mt-1">Konten ini diurus dari dashboard admin.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Akreditasi & Kepala Sekolah: dua fakta dengan komposisi berbeda, bukan kartu kembar --}}
    <section class="bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div>
                <p class="text-sm font-semibold text-teal-700 mb-3">Akreditasi</p>
                <p class="text-xs text-gray-500 mb-4">Badan Akreditasi Nasional Sekolah/Madrasah (BAN-S/M)</p>
                @if ($profil->accreditation)
                    <p class="text-5xl font-bold text-teal-800 tracking-tight" aria-label="Peringkat akreditasi {{ $profil->accreditation }}">{{ $profil->accreditation }}</p>
                @else
                    <p class="italic text-gray-500">Peringkat akreditasi belum diisi.</p>
                @endif
            </div>

            <div class="lg:border-l lg:border-gray-200 lg:pl-10">
                <p class="text-sm font-semibold text-teal-700 mb-4">Kepala Sekolah</p>
                <div class="flex items-start gap-5">
                    @if ($kepsekPhoto)
                        <img src="{{ $kepsekPhoto }}" alt="{{ $kepsekName }}"
                             width="96" height="96"
                             class="h-24 w-24 rounded-full object-cover ring-2 ring-white shadow-md shrink-0">
                    @else
                        <div class="h-24 w-24 rounded-full bg-teal-800 text-white flex items-center justify-center font-bold text-2xl shrink-0"
                             aria-hidden="true">{{ strtoupper(mb_substr($kepsekName, 0, 1)) }}</div>
                    @endif
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 leading-snug">{{ $kepsekName }}</h2>
                        @if ($kepsekBio)
                            <p class="mt-2 text-sm text-gray-600 leading-relaxed line-clamp-3">&ldquo;{{ $kepsekBio }}&rdquo;</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    {{-- Pendidik & Tenaga Kependidikan --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        @php
            // Ikon brand sosial (path terverifikasi dari halaman sebelumnya) + tautan generik.
            $socialIcons = [
                'facebook' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
                'instagram' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>',
                'twitter' => '<path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>',
                'linkedin' => '<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>',
                'youtube' => '<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>',
                'github' => '<path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/>',
                'website' => '<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>',
            ];
            $genericIconPath = '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>';
        @endphp
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-8">
            <div>
                <p class="text-sm font-semibold text-teal-700 mb-2">Dewan Sekolah</p>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Pendidik &amp; Tenaga Kependidikan</h2>
            </div>
            <p class="text-sm text-gray-600 tabular-nums">{{ $teachers->count() }} pendidik dan staf terdaftar</p>
        </div>

        @if ($teachers->isEmpty())
            <div class="border border-dashed border-gray-300 rounded-xl p-10 text-center">
                <p class="text-gray-600 font-medium">Daftar dewan guru belum tersedia.</p>
                <p class="text-gray-500 text-sm mt-1">Akun guru yang dibuat oleh Administrator akan otomatis tampil di halaman ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($teachers as $t)
                    @php
                        $jabatan = $t->position ?? 'Guru / Pendidik';
                    @endphp
                    <div class="flex flex-col items-center rounded-xl border border-gray-200 bg-white p-6 text-center hover:border-teal-200 transition-colors">
                        @if ($t->photo_url)
                            <img src="{{ $t->photo_url }}" alt="{{ $t->name }}"
                                 width="88" height="88"
                                 class="h-22 w-22 rounded-full object-cover mb-3">
                        @else
                            <div class="h-22 w-22 rounded-full bg-teal-800 text-white flex items-center justify-center font-bold text-xl mb-3"
                                 aria-hidden="true">{{ strtoupper(mb_substr($t->name, 0, 1)) }}</div>
                        @endif

                        <h3 class="font-bold text-gray-900 text-sm leading-snug">{{ $t->name }}</h3>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $jabatan }}</p>

                        @if ($t->role === 'guru')
                            <a href="{{ route('guru.public.show', $t) }}"
                               title="Kunjungi halaman pribadi {{ $t->name }}"
                               class="mt-4 min-h-11 inline-flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">
                                Kunjungi Halaman
                            </a>
                        @endif

                        @if (!empty($t->social_links))
                            <ul class="flex items-center justify-center gap-1.5 mt-2">
                                @foreach ($t->social_links as $key => $val)
                                    @php
                                        $customs = is_array($val) ? $val : [['url' => $val, 'label' => ucfirst($key)]];
                                    @endphp
                                    @foreach ($customs as $c)
                                        @if (($c['url'] ?? '') !== '')
                                            @php
                                                $iconPath = is_array($val) ? $genericIconPath : ($socialIcons[$key] ?? $genericIconPath);
                                            @endphp
                                            <li>
                                                <a href="{{ $c['url'] }}" target="_blank" rel="noopener noreferrer"
                                                   title="{{ $c['label'] ?? ucfirst($key) }}"
                                                   class="w-11 h-11 flex items-center justify-center text-gray-400 hover:text-teal-700 hover:bg-teal-50 rounded-lg transition-colors">
                                                    <span class="sr-only">{{ $c['label'] ?? ucfirst($key) }}</span>
                                                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        {!! $iconPath !!}
                                                    </svg>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    {{-- Kontak: pita gelap penutup, CTA spesifik dan hanya jika data ada --}}
    <section class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-14 flex flex-col md:flex-row md:items-center gap-8">
            <div class="flex-1">
                <h2 class="text-xl font-bold mb-4">Informasi Sekolah</h2>
                <ul class="space-y-3 text-white/80 text-sm">
                    <li class="flex gap-3 items-start">
                        <span class="w-5 text-center text-white/50 shrink-0" aria-hidden="true">Alamat</span>
                        <span>{{ $profil->address ?? 'Alamat sekolah akan diisi dari dashboard admin.' }}</span>
                    </li>
                    @if ($profil->phone)
                        <li class="flex gap-3 items-center">
                            <span class="w-5 text-center text-white/50 shrink-0" aria-hidden="true">Telp</span>
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $profil->phone) }}" class="hover:text-white hover:underline underline-offset-4">{{ $profil->phone }}</a>
                        </li>
                    @endif
                    @if ($profil->email)
                        <li class="flex gap-3 items-center">
                            <span class="w-5 text-center text-white/50 shrink-0" aria-hidden="true">Surel</span>
                            <a href="mailto:{{ $profil->email }}" class="hover:text-white hover:underline underline-offset-4">{{ $profil->email }}</a>
                        </li>
                    @endif
                    <li class="flex gap-3 items-center">
                        <span class="w-5 text-center text-white/50 shrink-0" aria-hidden="true">Galeri</span>
                        <a href="{{ route('gallery.index') }}" class="hover:text-white hover:underline underline-offset-4">Lihat aktivitas di halaman Galeri</a>
                    </li>
                </ul>
            </div>
            <div class="flex flex-col gap-3">
                <a href="{{ route('contact.index') }}"
                   class="min-h-11 inline-flex items-center justify-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Kirim pesan melalui halaman Kontak
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
                @if ($profil->wa_number)
                    <a href="https://wa.me/{{ $profil->wa_number }}" target="_blank" rel="noopener noreferrer"
                       class="min-h-11 inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold border border-white/30 hover:bg-white/10 transition-colors">
                        Hubungi via WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
