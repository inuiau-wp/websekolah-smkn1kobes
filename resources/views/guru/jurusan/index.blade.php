<x-guru-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Program Keahlian Saya</h1>
        <p class="text-gray-600 mt-1">@if ($managed)Jabatan Anda terkait kejuruan <strong>{{ $managed->code }} — {{ $managed->name }}</strong>. Anda dapat mengedit konten jurusan ini di menu Edit.@elseJabatan Anda belum terhubung ke program keahlian. Hubungi admin jika dianggap keliru.@endif</p>
    </div>

    @if ($managed)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
            <div class="flex items-center gap-4 mb-5">
                <span class="flex h-12 w-12 items-center justify-center rounded-lg text-white font-bold"
                      style="background-color: {{ $managed->color }}">{{ $managed->code }}</span>
                <div>
                    <h2 class="font-bold text-gray-900">{{ $managed->name }}</h2>
                    <p class="text-xs text-gray-500">/jurusan/{{ $managed->slug }}</p>
                </div>
                <a href="{{ route('guru.jurusan.edit', $managed) }}"
                   class="ml-auto px-4 py-2 bg-blue-600 text-white text-sm rounded-lg font-semibold hover:bg-blue-700 transition">Edit Konten</a>
            </div>

            @if ($managed->description)
                <p class="text-sm text-gray-700 line-clamp-4">{{ $managed->description }}</p>
            @endif
        </div>
    @endif
</x-guru-layout>
