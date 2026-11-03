@php
    $fontsLink = 'https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SMKN 1 Kotabesi') }} — {{ $guestTitle ?? 'Portal Masuk' }}</title>
        @include('layouts.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="{{ $fontsLink }}" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col bg-gradient-to-br from-[#1c398e] via-[#0d542b] to-[#e17100]">
            <!-- Kembali ke beranda -->
            <div class="p-4 sm:p-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-white/80 hover:text-white text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Kartu utama -->
            <div class="flex-1 flex flex-col sm:justify-center items-center pt-4 pb-10 px-4">
                <div class="w-full max-w-5xl grid lg:grid-cols-2 overflow-hidden bg-white shadow-2xl sm:rounded-3xl">

                    <!-- Panel kiri: identitas & warna jurusan -->
                    <div class="hidden lg:flex flex-col justify-between p-10 text-white relative"
                         style="background: linear-gradient(160deg, #1c398e 0%, #0d542b 100%);">
                        <div>
                            @if ($schoolProfile->logo_url)
                                <img src="{{ $schoolProfile->logo_url }}" alt="{{ $schoolProfile->school_name }}" class="h-16 w-auto object-contain bg-white rounded-xl p-2 mb-6 shadow-lg">
                            @endif
                            <h1 class="text-2xl font-bold leading-snug">{{ $schoolProfile->school_name ?? config('app.name', 'SMKN 1 Kotabesi') }}</h1>
                            <p class="text-white/70 text-sm mt-2">{{ $schoolProfile->tagline ?? 'Portal informasi & layanan akademik sekolah.' }}</p>
                        </div>

                        <!-- Aksen 3 jurusan -->
                        <div class="space-y-3">
                            @php
                                $jurusanList = App\Models\Jurusan::query()->orderBy('id')->get();
                            @endphp
                            @forelse ($jurusanList as $j)
                                <div class="flex items-center gap-3 bg-white/10 backdrop-blur rounded-xl px-4 py-3">
                                    <span class="h-9 w-9 flex items-center justify-center rounded-lg font-bold text-xs shrink-0" style="background-color: {{ $j->color }};">
                                        {{ $j->code }}
                                    </span>
                                    <span class="text-sm font-medium text-white/90">{{ $j->name }}</span>
                                </div>
                            @empty
                                <div class="text-white/60 text-sm italic">Program keahlian akan diperbarui.</div>
                            @endforelse
                        </div>

                        <p class="text-white/50 text-xs">&copy; {{ date('Y') }} {{ $schoolProfile->school_name ?? config('app.name', 'SMKN 1 Kotabesi') }}</p>
                    </div>

                    <!-- Panel kanan: form -->
                    <div class="p-8 sm:p-10 lg:p-12 flex flex-col justify-center">
                        <!-- Logo mobile -->
                        <div class="lg:hidden flex justify-center mb-6">
                            @if ($schoolProfile->logo_url)
                                <img src="{{ $schoolProfile->logo_url }}" alt="{{ $schoolProfile->school_name }}" class="h-16 w-auto object-contain">
                            @else
                                <h2 class="text-xl font-bold">{{ config('app.name', 'SMKN 1 Kotabesi') }}</h2>
                            @endif
                        </div>
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
