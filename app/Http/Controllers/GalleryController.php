<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Contracts\View\View;

class GalleryController extends Controller
{
    /**
     * Display the photo gallery grid.
     */
    public function index(): View
    {
        $photos = Gallery::query()
            ->orderByDesc('created_at')
            ->paginate(24)
            ->withQueryString();

        $categories = Gallery::query()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        return view('gallery.index', compact('photos', 'categories'));
    }
}
