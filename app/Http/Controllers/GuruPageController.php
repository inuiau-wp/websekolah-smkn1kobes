<?php

namespace App\Http\Controllers;

use App\Models\GuruProfile;
use App\Models\News;
use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * Halaman pribadi guru yang bisa dilihat publik.
 * Menampilkan biodata guru (tanpa NIP), CV, prestasi, dan artikel/modul
 * (berita published yang ditulis guru).
 */
class GuruPageController extends Controller
{
    public function show(User $user): View
    {
        // Hanya akun guru yang punya halaman publik
        if ($user->role !== 'guru') {
            abort(404);
        }

        $profile = GuruProfile::query()->firstOrCreate(['user_id' => $user->id]);

        $articles = News::query()
            ->published()
            ->where('author_id', $user->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        return view('guru.public.show', [
            'guru' => $user,
            'guruProfile' => $profile,
            'articles' => $articles,
        ]);
    }
}
