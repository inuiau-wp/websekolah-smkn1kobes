<?php

namespace Database\Seeders;

use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Seed the school profile row (single-row config).
     */
    public function run(): void
    {
        SchoolProfile::updateOrCreate(
            ['id' => 1],
            [
                'school_name' => 'SMKN 1 Kotabesi',
                'tagline' => 'Pendidikan Berkualitas untuk Generasi Emas',
                'address' => 'Alamat lengkap sekolah akan dimasukkan melalui dashboard admin.',
                'phone' => '(000) 000-0000',
                'email' => 'info@smkn1kotabesi.sch.id',
                'wa_number' => '628123456789',
                'vision' => 'Menjadi sekolah kejuruan unggulan yang menghasilkan lulusan kompeten, mandiri, dan berkarakter.',
                'mission' => "Menyelenggarakan pendidikan kejuruan yang berkualitas.\nMengembangkan kemampuan siswa sesuai kebutuhan dunia kerja.\nMembangun budaya belajar yang aktif dan berbasis proyek.\nMeningkatkan kerja sama industri dan instansi mitra.",
                'history' => null,        // admin mengisi nanti
                'accreditation' => null,  // admin mengisi nanti
                'headmaster' => null,     // admin mengisi nanti
            ],
        );
    }
}
