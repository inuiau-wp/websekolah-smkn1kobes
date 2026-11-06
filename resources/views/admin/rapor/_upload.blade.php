{{-- Impor template & unduhan data Rapor Pendidikan --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-xl">
            <h2 class="font-bold text-gray-900">Impor dari Template</h2>
            <p class="text-xs text-gray-500 mt-1">
                Unduh template (.md atau .csv), isi data di luar aplikasi, lalu unggah di sini —
                seluruh isian template langsung mengisi form di bawah untuk direviu sebelum disimpan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.rapor-pendidikan.template', ['format' => 'md']) }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                Unduh Template (.md)
            </a>
            <a href="{{ route('admin.rapor-pendidikan.template', ['format' => 'csv']) }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                Unduh Template (.csv)
            </a>
            @if ($rapor->exists)
                <span class="hidden sm:inline text-gray-300">|</span>
                <a href="{{ route('admin.rapor-pendidikan.template', ['format' => 'md', 'year' => $rapor->year]) }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-teal-200 bg-teal-50 text-xs font-semibold text-teal-800 hover:bg-teal-100 transition">
                    Data {{ $rapor->year }} (.md)
                </a>
                <a href="{{ route('admin.rapor-pendidikan.template', ['format' => 'csv', 'year' => $rapor->year]) }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-teal-200 bg-teal-50 text-xs font-semibold text-teal-800 hover:bg-teal-100 transition">
                    Data {{ $rapor->year }} (.csv)
                </a>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.rapor-pendidikan.import') }}" enctype="multipart/form-data"
          class="mt-4 flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
        @csrf
        <input type="hidden" name="context" value="{{ $rapor->exists ? 'edit' : 'create' }}">
        <input type="hidden" name="rapor_id" value="{{ $rapor->id }}">
        <input type="file" name="file" accept=".md,.markdown,.csv,.txt" required
               class="block w-full sm:w-auto text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-white file:text-sm file:font-semibold hover:file:bg-gray-800">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-semibold hover:bg-teal-700 transition">
            Unggah &amp; Isi Form
        </button>
        @error('file') <p class="w-full text-xs text-red-600">{{ $message }}</p> @enderror
    </form>
</div>
