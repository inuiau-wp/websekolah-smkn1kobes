<x-app-layout>
    <!-- Header halaman -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('home') }}" class="hover:text-gray-900">Beranda</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">Kontak & Lokasi</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Kontak & Lokasi</h1>
            <p class="text-gray-600 mt-1">Hubungi kami untuk informasi kapan saja</p>
            @if ($profil->logo_url)
                <div class="mt-4">
                    <img src="{{ $profil->logo_url }}" alt="{{ $profil->school_name }}" class="h-16 w-auto mx-auto md:mx-0 object-contain">
                </div>
            @endif
        </div>
    </header>

    <section class="py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Kolom kiri: info kontak -->
                <div class="space-y-6">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Kontak</h2>
                        <ul class="space-y-4 text-sm">
                            <li class="flex gap-3 items-start">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900">Alamat</p>
                                    <p class="text-gray-600">{{ $profil->address ?? 'Alamat sekolah akan dimasukkan admin.' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3 items-start">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.68l1.515 4.548a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.548 1.515a1 1 0 01.68.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900">Telepon</p>
                                    <p class="text-gray-600">{{ $profil->phone ?? 'Nomor telepon akan dimasukkan admin.' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3 items-start">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900">Email</p>
                                    <p class="text-gray-600 break-all">{{ $profil->email ?? 'Email akan dimasukkan admin.' }}</p>
                                </div>
                            </li>
                            <li class="flex gap-3 items-start">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <div>
                                    <p class="font-semibold text-gray-900">Jam Layanan</p>
                                    <p class="text-gray-600">(Akan diisi admin)<br>Senin – Jumat, 07.00 – 15.00 WIB</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- CTA WhatsApp -->
                    <div class="bg-emerald-600 rounded-xl p-6 text-white">
                        <h2 class="text-lg font-bold mb-2">Butuh jawaban cepat?</h2>
                        <p class="text-sm text-white/80 mb-4">Hubungi kami langsung melalui WhatsApp.</p>
                        <a href="https://wa.me/{{ $profil->wa_number ?? '628123456789' }}?text={{ rawurlencode('Halo, saya ingin bertanya tentang SMKN 1 Kotabesi.') }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-white text-emerald-700 px-5 py-2.5 rounded-lg font-semibold hover:bg-white/90 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                            Chat via WhatsApp
                        </a>
                    </div>
                </div>

                <!-- Kolom kanan: form pesan -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">Kirim Pesan</h2>
                        <p class="text-gray-600 text-sm mb-6">Pesan Anda akan diterima oleh admin sekolah.</p>

                        @if (session('status'))
                            <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                                <p class="text-sm text-emerald-800 flex gap-2 items-start">
                                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ session('status') }}
                                </p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                                    <input id="name" name="name" type="text" required
                                           value="{{ old('name') }}"
                                           class="block w-full rounded-lg border-gray-300 @error('name') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                                    <input id="email" name="email" type="email" required
                                           value="{{ old('email') }}"
                                           class="block w-full rounded-lg border-gray-300 @error('email') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                                <input id="subject" name="subject" type="text"
                                       value="{{ old('subject') }}"
                                       placeholder="Contoh: Pertanyaan tentang pendaftaran"
                                       class="block w-full rounded-lg border-gray-300 @error('subject') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">
                                @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Pesan <span class="text-red-500">*</span></label>
                                <textarea id="message" name="message" rows="6" required
                                          placeholder="Tulis pesan Anda di sini..."
                                          class="block w-full rounded-lg border-gray-300 @error('message') border-red-400 @enderror focus:border-gray-900 focus:ring-gray-900">{{ old('message') }}</textarea>
                                @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 transition">
                                    Kirim Pesan
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Google Maps -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mt-8">
                        @php
                            // Prioritas: koordinat presisi dari admin. Bila kosong, gunakan NAMA sekolah
                            // (bukan alamat bebas — alamat bebas sering salah geocode di Maps).
                            $hasCoords = $profil->map_latitude !== null && $profil->map_longitude !== null;
                            $mapQuery = $hasCoords
                                ? number_format((float) $profil->map_latitude, 6, '.', '').','.number_format((float) $profil->map_longitude, 6, '.', '')
                                : ($profil->school_name ?? 'SMKN 1 Kotabesi');
                        @endphp
                        <div class="aspect-video w-full">
                            <iframe src="https://maps.google.com/maps?q={{ rawurlencode($mapQuery) }}{{ $hasCoords ? '&z=16' : '' }}&output=embed"
                                    class="w-full h-full border-0" allowfullscreen loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Lokasi {{ $profil->school_name }}"></iframe>
                        </div>
                        <div class="p-4 flex items-center justify-between gap-3">
                            <p class="text-sm text-gray-600">
                                @if ($hasCoords)
                                    Peta menggunakan koordinat presisi set oleh admin.
                                @else
                                    Peta lokasi berdasarkan nama sekolah.
                                    <span class="text-gray-400">(isi koordinat di dashboard admin agar presisi)</span>
                                @endif
                            </p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode($mapQuery) }}"
                               target="_blank"
                               class="shrink-0 inline-flex items-center gap-1.5 text-sm font-medium text-gray-900 hover:text-gray-600">
                                Buka di Maps
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</x-app-layout>
