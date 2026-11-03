<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard ringkasan guru.
     */
    public function __invoke(): View
    {
        $teacherId = auth()->id();

        $stats = [
            'total' => News::where('author_id', $teacherId)->count(),
            'published' => News::where('author_id', $teacherId)->published()->count(),
            'draft' => News::where('author_id', $teacherId)->where('status', 'draft')->count(),
        ];

        $recentNews = News::query()
            ->where('author_id', $teacherId)
            ->with('jurusan')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('guru.dashboard', compact('stats', 'recentNews'));
    }
}
