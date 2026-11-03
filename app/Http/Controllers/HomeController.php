<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use App\Models\Gallery;
use App\Models\Jurusan;
use App\Models\News;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\Statistic;
use App\Models\Testimonial;

class HomeController extends Controller
{
    /**
     * Homepage: 100% dari database (hero settings, carousel, statistik,
     * jurusan, berita, galeri, testimoni, partner).
     */
    public function index()
    {
        $jurusan = Jurusan::all();
        $beritas = News::published()
            ->with('jurusan')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
        $galleries = Gallery::query()->orderByDesc('created_at')->limit(8)->get();
        $slides = CarouselSlide::query()->where('is_active', true)->orderBy('sort')->orderBy('id')->get();
        $statistics = Statistic::query()->where('is_active', true)->orderBy('sort')->orderBy('id')->get();
        $testimonials = Testimonial::query()->where('is_active', true)->orderBy('sort')->orderBy('id')->get();
        $partners = Partner::query()->where('is_active', true)->orderBy('sort')->orderBy('id')->get();
        $settings = Setting::allCached();

        return view('home', compact(
            'jurusan', 'beritas', 'galleries',
            'slides', 'statistics', 'testimonials', 'partners', 'settings',
        ));
    }
}
