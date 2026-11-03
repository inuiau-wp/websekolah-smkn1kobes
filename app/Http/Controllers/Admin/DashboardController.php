<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Jurusan;
use App\Models\News;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard home (ringkasan konten).
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'totalBerita' => News::count(),
            'beritaDraf' => News::where('status', 'draft')->count(),
            'totalGaleri' => Gallery::count(),
            'totalJurusan' => Jurusan::count(),
            'pesanBelumDibaca' => ContactMessage::where('is_read', false)->count(),
            'totalPesan' => ContactMessage::count(),
        ]);
    }
}
