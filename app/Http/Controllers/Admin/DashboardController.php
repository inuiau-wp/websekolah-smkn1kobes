<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Jurusan;
use App\Models\News;
use App\Models\Partner;
use App\Models\RaporPendidikan;
use App\Models\Statistic;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard home (ringkasan seluruh konten).
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            // Berita
            'totalBerita' => News::count(),
            'beritaDraf' => News::where('status', 'draft')->count(),
            'beritaKategoriLabels' => News::query()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->map(fn ($total, $category) => News::categories()[$category] ?? $category),
            'beritaKategoriData' => News::query()
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category')
                ->values(),
            'beritaTerbaru' => News::with('author')->latest('created_at')->take(5)->get(),

            // Galeri
            'totalGaleri' => Gallery::count(),

            // Pesan kontak
            'totalPesan' => ContactMessage::count(),
            'pesanBelumDibaca' => ContactMessage::where('is_read', false)->count(),
            'pesanTerbaru' => ContactMessage::latest('created_at')->take(5)->get(),

            // Akun & jurusan
            'guruCount' => User::where('role', 'guru')->count(),
            'siswaCount' => User::where('role', 'siswa')->count(),
            'totalJurusan' => Jurusan::count(),

            // Rapor Pendidikan
            'raporTotal' => RaporPendidikan::count(),
            'raporDipublikasikan' => RaporPendidikan::where('is_published', true)->count(),

            // Konten beranda
            'slideCount' => CarouselSlide::count(),
            'statistikCount' => Statistic::count(),
            'testimoniCount' => Testimonial::count(),
            'partnerCount' => Partner::count(),
        ]);
    }
}
