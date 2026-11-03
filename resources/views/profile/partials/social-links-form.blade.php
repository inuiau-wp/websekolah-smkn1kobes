@if (auth()->user()->role === 'guru')
<div class="max-w-xl">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Link Media Sosial & Custom (Guru)</h3>
    <p class="text-sm text-gray-500 mb-4">Isi hanya yang diinginkan. Kosongkan untuk menyembunyikan ikon di halaman profil sekolah.</p>

    {{-- Predefined social --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        @foreach(['facebook'=>'Facebook','instagram'=>'Instagram','twitter'=>'Twitter (X)','linkedin'=>'LinkedIn','youtube'=>'YouTube','github'=>'GitHub','website'=>'Website Pribadi'] as $key => $label)
            <div>
                <x-input-label for="social_links[{{ $key }}]" :value="$label" />
                <x-text-input id="social_links[{{ $key }}]" name="social_links[{{ $key }}]" type="url"
                              value="{{ old('social_links.'.$key, data_get($user->social_links, $key)) }}"
                              placeholder="https://{{ $key }}.com/username" />
                @error("social_links.{$key}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endforeach
    </div>

    {{-- Custom links --}}
    <div class="border-t pt-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="font-medium text-gray-700">Link Custom (contoh: s.id, portfolio, dsb)</h4>
            <button type="button" id="add-custom-link" class="text-sm text-blue-600 hover:underline">+ Tambah Link</button>
        </div>
        <div id="custom-links-container" class="space-y-3">
            @php
                $customLinks = data_get($user->social_links, 'custom', []);
            @endphp
            @if (!empty($customLinks))
                @foreach ($customLinks as $index => $cl)
                    <div class="custom-link-row flex gap-2 items-start">
                        <input type="text" name="social_links[custom][{{ $index }}][label]" placeholder="Label (contoh: Portfolio)"
                               value="{{ $cl['label'] }}" class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
                        <input type="url" name="social_links[custom][{{ $index }}][url]" placeholder="https://..."
                               value="{{ $cl['url'] }}" class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
                        <button type="button" class="remove-custom-link text-red-500 hover:text-red-700" aria-label="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endforeach
            @else
                <div class="custom-link-row flex gap-2 items-start">
                    <input type="text" name="social_links[custom][0][label]" placeholder="Label (contoh: Portfolio)"
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
                    <input type="url" name="social_links[custom][0][url]" placeholder="https://..."
                           class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
                    <button type="button" class="remove-custom-link text-red-500 hover:text-red-700" aria-label="Hapus">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('custom-links-container');
    const addBtn = document.getElementById('add-custom-link');
    let idx = container.querySelectorAll('.custom-link-row').length;
    addBtn?.addEventListener('click', () => {
        const div = document.createElement('div');
        div.className = 'custom-link-row flex gap-2 items-start';
        div.innerHTML = `
            <input type="text" name="social_links[custom][${idx}][label]" placeholder="Label (contoh: Portfolio)"
                   class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
            <input type="url" name="social_links[custom][${idx}][url]" placeholder="https://..."
                   class="flex-1 rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600" />
            <button type="button" class="remove-custom-link text-red-500 hover:text-red-700" aria-label="Hapus">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>`;
        container.appendChild(div);
        idx++;
    });
    container?.addEventListener('click', e => {
        if (e.target.closest('.remove-custom-link')) {
            e.target.closest('.custom-link-row').remove();
        }
    });
});
</script>
@endif