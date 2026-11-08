<x-app-layout>
    <!-- Hero header -->
    <section class="relative bg-gray-900 text-white py-16 overflow-hidden">
        @php
            $coverUrl = $profil->cover_url;
        @endphp
        @if ($coverUrl)
            <div class="absolute inset-0">
                <img src="{{ $coverUrl }}" alt="{{ $profil->school_name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gray-900/60"></div>
            </div>
        @else
            <div class="absolute inset-0" style="background: linear-gradient(135deg, #1c398e 0%, #0d542b 55%, #e17100 110%);"></div>
        @endif
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-white/70 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-white font-medium">Profil Sekolah</span>
            </nav>
            <div class="flex flex-col md:flex-row items-center gap-6 mb-6">
                @if ($profil->logo_url)
                    <img src="{{ $profil->logo_url }}" alt="{{ $profil->school_name }}" class="h-20 md:h-24 w-auto object-contain bg-white/10 p-2 rounded-xl backdrop-blur-sm border border-white/20 shadow-lg">
                @endif
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold mb-2">Profil {{ $profil->school_name }}</h1>
                    <p class="text-white/80 max-w-2xl">{{ $profil->tagline ?? 'Kenali lebih dekat sekolah kami.' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Konten profil -->
    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">

            <!-- Visi & Misi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </span>
                        <h2 class="text-xl font-bold text-gray-900">Visi</h2>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $profil->vision ?? 'Visi sekolah akan dimasukkan melalui dashboard admin.' }}
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </span>
                        <h2 class="text-xl font-bold text-gray-900">Misi</h2>
                    </div>
                    @php
                        $misiLines = $profil->mission ? str($profil->mission)->explode("\n")->filter() : collect();
                    @endphp
                    <ul class="space-y-2.5">
                        @forelse ($misiLines as $line)
                            <li class="flex gap-2 text-gray-600">
                                <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $line }}</span>
                            </li>
                        @empty
                            <li class="text-gray-400 italic">Misi sekolah akan dimasukkan melalui dashboard admin.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Sejarah -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </span>
                    <h2 class="text-xl font-bold text-gray-900">Sejarah</h2>
                </div>
                @if ($profil->history)
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">{{ $profil->history }}</p>
                @else
                    <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <p class="text-gray-400 text-sm">Sejarah sekolah belum diisi.</p>
                        <p class="text-gray-400 text-xs mt-1">(Konten layanan akan diurus dari dashboard nanti)</p>
                    </div>
                @endif
            </div>

            <!-- Akreditasi + Kepala Sekolah -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Akreditasi -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center flex flex-col justify-center">
                    <span class="flex h-12 w-12 mx-auto mb-3 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </span>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Status Akreditasi</h2>
                    <p class="text-xs text-gray-500 mb-4">Badan Akreditasi Nasional Sekolah/Madrasah (BAN-S/M)</p>
                    @if ($profil->accreditation)
                        <span class="inline-block mx-auto px-6 py-2.5 bg-blue-50 text-blue-800 font-bold text-3xl rounded-xl border border-blue-200">{{ $profil->accreditation }}</span>
                    @else
                        <p class="text-gray-400 text-sm italic">Peringkat akreditasi belum diisi.</p>
                    @endif
                </div>

                <!-- Kepala Sekolah -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center flex flex-col justify-center items-center">
                    @php
                        $kepsekPhoto = $headmasterUser?->photo_url;
                        $kepsekName = $headmasterUser?->name ?? $profil->headmaster ?? 'Nama Kepala Sekolah';
                        $kepsekNip = $headmasterUser?->nip;
                        $kepsekBio = $headmasterUser?->bio;
                    @endphp
                    @if ($kepsekPhoto)
                        <img src="{{ $kepsekPhoto }}" alt="{{ $kepsekName }}" class="h-24 w-24 rounded-full object-cover border-4 border-amber-100 shadow-md mb-3">
                    @else
                        <div class="h-24 w-24 mx-auto mb-3 rounded-full bg-amber-50 border-2 border-amber-200 flex items-center justify-center text-amber-700 font-bold text-2xl shadow-inner">
                            {{ strtoupper(substr($kepsekName, 0, 1)) }}
                        </div>
                    @endif
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-900 mb-2">
                        Kepala Sekolah
                    </span>
                    <h3 class="text-lg font-bold text-gray-900 leading-snug">{{ $kepsekName }}</h3>
                    @if ($kepsekBio)
                        <p class="text-xs text-gray-600 mt-3 italic line-clamp-3 max-w-md">&ldquo;{{ $kepsekBio }}&rdquo;</p>
                    @endif
                </div>
            </div>

            <!-- Dewan Guru & Tenaga Kependidikan -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Pendidik & Tenaga Kependidikan</h2>
                        <p class="text-sm text-gray-500 mt-1">Daftar pimpinan, dewan guru, dan staf pendukung SMKN 1 Kotabesi</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                        Total: {{ $teachers->count() }} Pendidik & Staf
                    </span>
                </div>

                @if ($teachers->isEmpty())
                    <div class="border border-dashed border-gray-300 rounded-xl p-10 text-center">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-gray-500 font-medium">Data dewan guru belum ditambahkan.</p>
                        <p class="text-gray-400 text-xs mt-1">Akun guru yang dibuat oleh Administrator akan otomatis tampil di sini.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($teachers as $t)
                            <div class="group rounded-xl border border-gray-200 bg-white p-5 text-center shadow-xs hover:shadow-md hover:border-gray-300 transition flex flex-col items-center">
                                @if ($t->photo_url)
                                    <img src="{{ $t->photo_url }}" alt="{{ $t->name }}" class="h-24 w-24 rounded-full object-cover border-2 border-gray-100 shadow mb-3 group-hover:scale-105 transition-transform">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-2xl shadow mb-3 group-hover:scale-105 transition-transform">
                                        {{ strtoupper(substr($t->name, 0, 1)) }}
                                    </div>
                                @endif
                                <h3 class="font-bold text-gray-900 text-sm leading-snug mb-1">{{ $t->name }}</h3>
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold mb-1
                                    {{ $t->position === 'Kepala Sekolah' ? 'bg-amber-100 text-amber-900' : (str_contains($t->position ?? '', 'Waka') ? 'bg-blue-100 text-blue-900' : 'bg-gray-100 text-gray-700') }}">
                                    {{ $t->position ?? 'Guru / Pendidik' }}
                                </span>

                                @if ($t->role === 'guru')
                                    <a href="{{ route('guru.public.show', $t) }}"
                                       title="Kunjungi halaman pribadi {{ $t->name }}"
                                       class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-slate-900 text-white hover:bg-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        Kunjungi Halaman
                                    </a>
                                @endif

                                {{-- Social media icons --}}
                                @php
                                    $socialIcons = [
                                        'facebook' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>',
                                        'instagram' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
                                        'twitter' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>',
                                        'linkedin' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                                        'youtube' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                                        'github' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>',
                                        'website' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                                    ];
                                    $genericIcon = '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>';
                                @endphp
                                @if (!empty($t->social_links))
                                    <div class="flex items-center justify-center gap-2 mt-2">
                                        @foreach ($t->social_links as $key => $val)
                                            @if (is_array($val))
                                                {{-- custom links --}}
                                                @foreach ($val as $c)
                                                    <a href="{{ $c['url'] }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700 transition" title="{{ $c['label'] }}">
                                                        {!! $genericIcon !!}
                                                    </a>
                                                @endforeach
                                            @else
                                                {{-- predefined --}}
                                                <a href="{{ $val }}" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-700 transition" title="{{ ucfirst($key) }}">
                                                    @if (isset($socialIcons[$key]))
                                                        {!! $socialIcons[$key] !!}
                                                    @else
                                                        {!! $genericIcon !!}
                                                    @endif
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Kontak singkat -->
            <div class="bg-gray-900 rounded-xl text-white p-8 lg:p-10">
                <div class="flex flex-col md:flex-row md:items-center gap-8">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold mb-4">Informasi {{ $profil->school_name }}</h2>
                        <ul class="space-y-3 text-white/80 text-sm">
                            <li class="flex gap-3 items-start">
                                <svg class="w-5 h-5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>{{ $profil->address ?? 'Alamat sekolah akan dimasukkan admin.' }}</span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <svg class="w-5 h-5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.68l1.515 4.548a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.548 1.515a1 1 0 01.68.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                <span>{{ $profil->phone ?? 'Nomor telepon belum diisi.' }}</span>
                            </li>
                            <li class="flex gap-3 items-center">
                                <svg class="w-5 h-5 shrink-0 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span>{{ $profil->email ?? 'Email belum diisi.' }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('contact.index') }}" class="inline-flex items-center justify-center gap-2 bg-white text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Halaman Kontak
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        @if ($profil->wa_number)
                            <a href="https://wa.me/{{ $profil->wa_number }}" target="_blank"
                               class="inline-flex items-center justify-center gap-2 border border-white/30 px-6 py-3 rounded-lg font-semibold hover:bg-white/10 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                                WhatsApp Sekolah
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </section>
</x-app-layout>
