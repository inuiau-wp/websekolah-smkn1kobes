<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengatur konten beranda yang sebelumnya hardcoded: hero (settings),
 * carousel, statistik, testimoni, dan partner. Satu controller memakai
 * parameter {type} (slides|statistics|testimonials|partners).
 */
class HomeContentController extends Controller
{
    public function index(): View
    {
        $slides = CarouselSlide::query()->orderBy('sort')->orderBy('id')->get();
        $statistics = Statistic::query()->orderBy('sort')->orderBy('id')->get();
        $testimonials = Testimonial::query()->orderBy('sort')->orderBy('id')->get();
        $partners = Partner::query()->orderBy('sort')->orderBy('id')->get();
        $settings = Setting::allCached();

        return view('admin.home.index', compact('slides', 'statistics', 'testimonials', 'partners', 'settings'));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $validated = $request->validate($this->rules($type, false), $this->messagesFor($type));

        $modelClass = $this->modelFor($type);
        $imageField = $this->imageField($type);
        $imageColumn = $this->imageColumn($type);

        if ($imageField !== '' && ($path = $this->storeImage($request, $imageField))) {
            $validated[$imageColumn] = $path;
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $modelClass::create($validated);

        return redirect()->route('admin.home.index')->with('status', $this->label($type).' berhasil ditambahkan.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $validated = $request->validate($this->rules($type, true), $this->messagesFor($type));

        $modelClass = $this->modelFor($type);
        $model = $modelClass::query()->findOrFail($id);

        $imageField = $this->imageField($type);
        $imageColumn = $this->imageColumn($type);

        if ($imageField !== '' && ($newPath = $this->storeImage($request, $imageField))) {
            $old = $model->{$imageColumn};
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $validated[$imageColumn] = $newPath;
        }

        // is_active checkbox (default: periksa tersedia)
        $validated['is_active'] = $request->boolean('is_active');

        // Hapus logo partner (boleh kosong, fallback tampil teks) / foto testimoni
        if ($imageField !== '' && $request->boolean('remove_image') && in_array($type, ['testimonials', 'partners'])) {
            $old = $model->{$imageColumn};
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            $validated[$imageColumn] = null;
        }

        $model->update($validated);

        $name = $model->title ?? $model->name ?? $model->label ?? $type;

        return redirect()->route('admin.home.index')->with('status', $this->label($type).' "'.$name.'" berhasil diperbarui.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $model = $this->modelFor($type)::query()->findOrFail($id);

        $imageColumn = $this->imageColumn($type);
        $old = $imageColumn ? $model->{$imageColumn} : null;
        if ($old && ! str_starts_with($old, 'http')) {
            Storage::disk('public')->delete($old);
        }

        $model->delete();

        return redirect()->route('admin.home.index')->with('status', $this->label($type).' "'.$model->title.'" berhasil dihapus.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_badge' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['required', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_cta_primary' => ['nullable', 'string', 'max:50'],
            'hero_cta_secondary' => ['nullable', 'string', 'max:50'],
            'section_carousel_title' => ['nullable', 'string', 'max:100'],
            'section_statistics_title' => ['nullable', 'string', 'max:100'],
            'section_testimonials_title' => ['nullable', 'string', 'max:100'],
            'section_partners_title' => ['nullable', 'string', 'max:100'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ], [
            'hero_title.required' => 'Judul hero wajib diisi.',
            'hero_image.image' => 'Berkas harus berupa gambar.',
            'hero_image.max' => 'Ukuran foto hero maksimal 2 MB.',
        ]);

        // Upload / hapus foto latar hero
        if ($request->hasFile('hero_image')) {
            $old = Setting::get('hero_image');
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('hero_image', $request->file('hero_image')->store('beranda', 'public'));
        } elseif ($request->boolean('remove_hero_image')) {
            $old = Setting::get('hero_image');
            if ($old && ! str_starts_with($old, 'http')) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('hero_image', null);
        }

        unset($validated['hero_image'], $validated['remove_hero_image']);

        foreach ($validated as $key => $value) {
            if ($value === null) {
                continue; // jangan simpan field opsional yang kosong
            }
            Setting::set($key, $value);
        }

        return redirect()->route('admin.home.index')->with('status', 'Teks beranda berhasil disimpan.');
    }

    /**
     * Kelas model terkait type.
     *
     * @return class-string<Model>
     */
    private function modelFor(string $type): string
    {
        return match ($type) {
            'slides' => CarouselSlide::class,
            'statistics' => Statistic::class,
            'testimonials' => Testimonial::class,
            default => Partner::class,
        };
    }

    /**
     * Nama input file gambar utama ('' bila tidak ada).
     */
    private function imageField(string $type): string
    {
        return match ($type) {
            'slides' => 'image',
            'testimonials' => 'photo',
            'partners' => 'logo',
            default => '',
        };
    }

    /**
     * Nama kolom path gambar ('' bila tidak ada).
     */
    private function imageColumn(string $type): string
    {
        return match ($type) {
            'slides' => 'image_path',
            'testimonials' => 'photo_path',
            'partners' => 'logo_path',
            default => '',
        };
    }

    private function label(string $type): string
    {
        return match ($type) {
            'slides' => 'Slide carousel',
            'statistics' => 'Statistik',
            'testimonials' => 'Testimoni',
            default => 'Partner',
        };
    }

    /**
     * Aturan validasi per type; foto slide wajib hanya saat create.
     *
     * @return array<string, array<int|string, mixed>>
     */
    private function rules(string $type, bool $isUpdate): array
    {
        return match ($type) {
            'slides' => [
                'title' => ['required', 'string', 'max:150'],
                'description' => ['nullable', 'string', 'max:300'],
                'sort' => ['nullable', 'integer', 'min:0'],
                'image' => [$isUpdate ? 'nullable' : 'required', 'image', 'max:2048'],
            ],
            'statistics' => [
                'label' => ['required', 'string', 'max:100'],
                'value' => ['required', 'string', 'max:50'],
                'sort' => ['nullable', 'integer', 'min:0'],
            ],
            'testimonials' => [
                'name' => ['required', 'string', 'max:100'],
                'role' => ['nullable', 'string', 'max:100'],
                'content' => ['required', 'string', 'max:500'],
                'sort' => ['nullable', 'integer', 'min:0'],
                'photo' => ['nullable', 'image', 'max:2048'],
            ],
            default => [ // partners
                'name' => ['required', 'string', 'max:150'],
                'url' => ['nullable', 'url', 'max:200'],
                'sort' => ['nullable', 'integer', 'min:0'],
                'logo' => ['nullable', 'image', 'max:2048'],
            ],
        };
    }

    /**
     * @return array<string, string>
     */
    private function messagesFor(string $type): array
    {
        return [
            'title.required' => 'Judul slide wajib diisi.',
            'image.required' => 'Foto slide wajib diunggah.',
            'image.image' => 'Berkas harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'logo.image' => 'Berkas harus berupa gambar.',
            'label.required' => 'Label statistik wajib diisi.',
            'value.required' => 'Nilai statistik wajib diisi.',
            'name.required' => 'Nama wajib diisi.',
            'content.required' => 'Isi testimoni wajib diisi.',
            'url.url' => 'URL tidak valid — sertakan https:// di awal.',
        ];
    }

    /** @phpstan-ignore-next-line unused parameter kept for symmetry */
    private function storeImage(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('beranda', 'public');
    }
}
