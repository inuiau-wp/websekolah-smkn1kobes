<x-app-layout>
    <!-- Header banner dengan warna aksen jurusan -->
    <section class="py-12 lg:py-16 text-white" style="background: linear-gradient(135deg, {{ $jurusan->color }} 0%, {{ $jurusan->color }}CC 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="text-sm text-white/70 mb-4">
                <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jurusan.index') }}" class="hover:text-white">Jurusan</a>
                <span class="mx-1">/</span>
                <span class="text-white font-medium">{{ $jurusan->code }}</span>
            </nav>

            <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-semibold mb-4">{{ $jurusan->code }}</span>
            <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ $jurusan->name }}</h1>
            <p class="text-white/80 max-w-2xl">
                {{ $jurusan->competencies ?? 'Kompetensi keahlian akan diperbarui oleh admin.' }}
            </p>
        </div>
    </section>

    <!-- Konten utama -->
     <section class="py-12 lg:py-16">        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Kolom kiri: deskripsi & kompetensi -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Deskripsi -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Tentang Jurusan</h2>
                        @if ($jurusan->description)
                            <p class="text-gray-600 leading-relaxed">{{ $jurusan->description }}</p>
                        @else
                            <p class="text-gray-400 italic leading-relaxed">
                                Deskripsi lengkap jurusan ini akan dimasukkan melalui dashboard admin. Silakan hubungi sekolah untuk informasi lebih lanjut sementara waktu.
                            </p>
                        @endif
                    </div>

                    <!-- Visi & Misi Jurusan -->
                    <div class="rounded-xl border border-gray-200 shadow-sm overflow-hidden bg-white">
                        <div class="px-8 py-5" style="background: linear-gradient(135deg, {{ $jurusan->color }}15 0%, {{ $jurusan->color }}05 100%); border-bottom: 3px solid {{ $jurusan->color }};">
                            <h2 class="text-xl font-bold" style="color: {{ $jurusan->color }};">Visi &amp; Misi Jurusan</h2>
                            <p class="text-sm text-gray-500 mt-1">Arah dan tujuan pendidikan jurusan {{ $jurusan->code }}.</p>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Visi -->
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-white text-xs font-bold" style="background-color: {{ $jurusan->color }};">V</span>
                                    <h3 class="font-bold text-gray-900 uppercase tracking-wide text-sm">Visi</h3>
                                </div>
                                @if ($jurusan->vision)
                                    <p class="text-gray-600 leading-relaxed">{{ $jurusan->vision }}</p>
                                @else
                                    <p class="text-gray-400 italic text-sm">Visi jurusan ini akan diperbarui oleh admin / kepala jurusan.</p>
                                @endif
                            </div>
                            <!-- Misi -->
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full text-white text-xs font-bold" style="background-color: {{ $jurusan->color }};">M</span>
                                    <h3 class="font-bold text-gray-900 uppercase tracking-wide text-sm">Misi</h3>
                                </div>
                                @if ($jurusan->mission)
                                    <ul class="space-y-2.5">
                                        @foreach (preg_split('/\r\n|\r|\n/', $jurusan->mission) as $missionPoint)
                                            @if (trim($missionPoint) !== '')
                                                <li class="flex items-start gap-3">
                                                    <span class="mt-1 flex h-4 w-4 shrink-0 items-center justify-center rounded-full" style="background-color: {{ $jurusan->color }}20;">
                                                        <span class="h-1.5 w-1.5 rounded-full" style="background-color: {{ $jurusan->color }};"></span>
                                                    </span>
                                                    <span class="text-sm text-gray-600 leading-relaxed">{{ trim($missionPoint) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-400 italic text-sm">Misi jurusan ini akan diperbarui oleh admin / kepala jurusan.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kompetensi Keahlian -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Kompetensi Keahlian</h2>
                        @if ($jurusan->competencies)
                            <div class="flex flex-wrap gap-2">
                                @foreach (explode(',', $jurusan->competencies) as $comp)
                                    <span class="px-3 py-1.5 rounded-lg text-sm font-medium"
                                          style="background-color: {{ $jurusan->color }}15; color: {{ $jurusan->color }};">
                                        {{ trim($comp) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-400 italic">Kompetensi keahlian akan diperbarui oleh admin.</p>
                        @endif
                    </div>

                    <!-- Berita terkait jurusan (placeholder) -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Berita Terkait</h2>
                        <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <p class="text-gray-400 text-sm">Belum ada berita untuk jurusan ini.</p>
                            <p class="text-gray-400 text-xs mt-1">(Berita akan muncul di sini setelah fitur berita selesai dibuat)</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom kanan: placeholder foto & ketua jurusan -->
                <div class="space-y-8">
                    <!-- Foto laboratorium / praktik (dari admin) atau placeholder -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        @if ($jurusan->image_path)
                            <img src="{{ $jurusan->imageSrc() }}" alt="{{ $jurusan->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="h-48 flex items-center justify-center"
                                 style="background-color: {{ $jurusan->color }}10; border-bottom: 1px solid {{ $jurusan->color }}30;">
                                <div class="text-center px-4">
                                    <svg class="w-10 h-10 mx-auto mb-2" style="color: {{ $jurusan->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <p class="text-xs text-gray-400">Foto<br>laboratorium / praktik<br>(placeholder)</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Kepala jurusan -->
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-center">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Kepala Jurusan</h3>
                        @php
                            $kaprogName = $jurusan->getHeadOfDepartmentName();
                            $kaprogPhoto = $jurusan->getHeadOfDepartmentPhotoUrl();
                        @endphp
                        @if ($kaprogPhoto)
                            <img src="{{ $kaprogPhoto }}" alt="{{ $kaprogName }}" class="h-20 w-20 mx-auto mb-3 rounded-full object-cover border-2 border-gray-100 shadow">
                        @else
                            <div class="h-20 w-20 mx-auto mb-3 rounded-full flex items-center justify-center font-bold text-xl shadow-inner"
                                 style="background-color: {{ $jurusan->color }}15; color: {{ $jurusan->color }};">
                                @if ($kaprogName)
                                    {{ strtoupper(substr($kaprogName, 0, 1)) }}
                                @else
                                    <svg class="w-10 h-10" style="color: {{ $jurusan->color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @endif
                            </div>
                        @endif
                        <p class="font-bold text-gray-900">{{ $kaprogName ?? 'Nama Kepala Jurusan' }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $kaprogName ? '' : '(akan diisi admin)' }}</p>
                    </div>

                    <!-- CTA -->
                    <div class="rounded-xl p-6 text-center text-white" style="background-color: {{ $jurusan->color }};">
                        <h3 class="font-bold mb-2">Tertarik dengan jurusan ini?</h3>
                        <p class="text-sm text-white/80 mb-4">Hubungi kami untuk informasi pendaftaran.</p>
                        <a href="https://wa.me/628123456789?text=Halo%2C%20saya%20tertarik%20dengan%20jurusan%20{{ strtoupper($jurusan->code) }}%20di%20SMKN%201%20Kotabesi"
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-white px-5 py-2.5 rounded-lg font-semibold hover:bg-white/90 transition"
                           style="color: {{ $jurusan->color }};">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                            Daftar via WhatsApp
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</x-app-layout>
