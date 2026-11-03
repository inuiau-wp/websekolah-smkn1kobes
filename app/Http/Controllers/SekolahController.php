<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\User;

class SekolahController extends Controller
{
    /**
     * Display the public school profile page.
     */
    public function profil()
    {
        $profil = SchoolProfile::query()->firstOrCreate([], [
            'school_name' => 'SMKN 1 Kotabesi',
        ]);

        // Cari akun dengan jabatan Kepala Sekolah
        $headmasterUser = User::where('position', 'Kepala Sekolah')->first();

        // Ambil daftar guru dan tenaga kependidikan
        $teachers = User::whereIn('role', ['guru', 'admin'])
            ->where(function ($q) {
                $q->whereNotNull('position')
                    ->orWhere('role', 'guru');
            })
            ->orderByRaw("CASE 
                WHEN position = 'Kepala Sekolah' THEN 1 
                WHEN position LIKE '%Waka%' THEN 2 
                WHEN position LIKE '%Kepala Program%' THEN 3 
                ELSE 4 
            END")
            ->orderBy('name')
            ->get();

        return view('profil', compact('profil', 'headmasterUser', 'teachers'));
    }
}
