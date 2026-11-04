<?php

namespace App\Http\Controllers;

use App\Models\RaporPendidikan;

class RaporController extends Controller
{
    /**
     * Halaman publik Rapor Pendidikan per tahun rilis.
     */
    public function show(int $year)
    {
        $rapor = RaporPendidikan::published()->where('year', $year)->firstOrFail();

        return view('rapor.show', compact('rapor'));
    }
}
