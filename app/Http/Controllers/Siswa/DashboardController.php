<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\News;
use App\Models\SchoolProfile;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan portal dashboard siswa.
     */
    public function __invoke(): View
    {
        $announcements = News::published()
            ->whereIn('category', ['announcement', 'agenda'])
            ->with('jurusan')
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        $jurusanList = Jurusan::all();

        $profil = SchoolProfile::query()->firstOrCreate(
            ['id' => 1],
            ['school_name' => 'SMKN 1 Kotabesi']
        );

        return view('siswa.dashboard', compact('announcements', 'jurusanList', 'profil'));
    }
}
