<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::insert([
            [
                'name' => 'Agribisnis Tanaman Perkebunan',
                'slug' => 'atp',
                'code' => 'ATP',
                'description' => 'Jurusan Agribisnis Tanaman Perkebunan mempelajari tentang budidaya, pengolahan, dan pemasaran hasil perkebunan.',
                'competencies' => 'Budidaya tanaman perkebunan, pengolahan hasil pertanian, pemasaran hasil perkebunan',
                'head_of_department' => null,
                'color' => '#0d542b',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Komputer dan Jaringan',
                'slug' => 'tkj',
                'code' => 'TKJ',
                'description' => 'Jurusan Teknik Komputer dan Jaringan mempelajari tentang perangkat keras komputer, jaringan, dan pemrograman.',
                'competencies' => 'Perakitan komputer, jaringan komputer, pemrograman web, administrasi sistem',
                'head_of_department' => null,
                'color' => '#1c398e',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Akuntansi Keuangan Lembaga',
                'slug' => 'akl',
                'code' => 'AKL',
                'description' => 'Jurusan Akuntansi Keuangan Lembaga mempelajari tentang pencatatan, pelaporan, dan manajemen keuangan lembaga.',
                'competencies' => 'Pencatatan keuangan, pelaporan keuangan, perpajakan, audit',
                'head_of_department' => null,
                'color' => '#e17100',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
