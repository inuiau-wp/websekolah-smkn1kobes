<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Untuk upload gambar yang disisipan ke konten berita (TinyMCE).
 * Dipakai admin & guru (auth saja).
 */
class NewsImageUploadController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'max:5120'],
        ], [
            'file.image' => 'Berkas harus berupa gambar.',
            'file.max' => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $path = $request->file('file')->store('news/images', 'public');

        // Path relatif (`/storage/...`) — aman untuk host localhost & domain produksi
        return response()->json(['location' => '/storage/'.(string) $path]);
    }
}
