<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of published news, announcements, and agendas.
     */
    public function index(Request $request): View
    {
        $category = $request->query('category');

        $news = News::published()
            ->with('jurusan')
            ->category($category)
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('news.index', compact('news', 'category'));
    }

    /**
     * Display the specified news item.
     */
    public function show(News $news): View
    {
        abort_unless($news->status === 'published', 404);
        $news->load('jurusan');

        $related = News::published()
            ->whereKeyNot($news->getKey())
            ->when($news->jurusan_id, fn ($q) => $q->where('jurusan_id', $news->jurusan_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('news.show', ['news' => $news, 'related' => $related]);
    }
}
