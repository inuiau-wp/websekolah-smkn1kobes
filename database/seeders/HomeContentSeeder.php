<?php

namespace Database\Seeders;

use App\Models\CarouselSlide;
use App\Models\Partner;
use App\Models\Statistic;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seed konten beranda awal dari konten hardcode agar tampilan tidak berubah saat swap DB-driven.
 * Admin selanjutnya bisa mengganti lewat dashboard (Fase 5).
 */
class HomeContentSeeder extends Seeder
{
    public function run(): void
    {
        // --- Carousel: 4 slide saat ini (foto external URL placeholder) ---
        $slides = [
            ['title' => 'Upacara Peringatan Hari Kemerdekaan', 'image_path' => 'https://images.unsplash.com/photo-1523927822181-ae318a434294?w=800&q=80', 'description' => 'Kegiatan ini dihadiri oleh seluruh siswa dan guru di lingkungan sekolah.'],
            ['title' => 'Lomba Karya Ilmiah Remaja', 'image_path' => 'https://images.unsplash.com/photo-1544717298-f28559b7efc2?w=800&q=80', 'description' => 'Kegiatan ini dihadiri oleh seluruh siswa dan guru di lingkungan sekolah.'],
            ['title' => 'Praktik Lapangan Jurusan TKJ', 'image_path' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=800&q=80', 'description' => 'Kegiatan ini dihadiri oleh seluruh siswa dan guru di lingkungan sekolah.'],
            ['title' => 'Prestasi Jurusan ATP Tingkat Provinsi', 'image_path' => 'https://images.unsplash.com/photo-1610873083197-2c124099236e?w=800&q=80', 'description' => 'Kegiatan ini dihadiri oleh seluruh siswa dan guru di lingkungan sekolah.'],
        ];

        foreach ($slides as $i => $slide) {
            CarouselSlide::updateOrCreate(
                ['title' => $slide['title']],
                $slide + ['sort' => $i, 'is_active' => true]
            );
        }

        // --- Statistik ---
        $statistics = [
            ['label' => 'Siswa Aktif', 'value' => '1000+'],
            ['label' => 'Guru & Staf', 'value' => '75'],
            ['label' => 'Jurusan Unggulan', 'value' => '3'],
            ['label' => 'Tahun Berdiri', 'value' => '25+'],
        ];

        foreach ($statistics as $i => $stat) {
            Statistic::updateOrCreate(['label' => $stat['label']], $stat + ['sort' => $i, 'is_active' => true]);
        }

        // --- Testimoni ---
        $testimonials = [
            ['name' => 'Budi Santoso', 'role' => 'Wali Murid TKJ', 'content' => 'Sekolah yang sangat unggul, anak saya sangat senang belajar di sini.', 'photo_path' => 'https://i.pravatar.cc/100?img=11'],
            ['name' => 'Siti Rahayu', 'role' => 'Siswa TKJ', 'content' => 'Fasilitas laboratorium komputer sangat lengkap dan mantap.', 'photo_path' => 'https://i.pravatar.cc/100?img=12'],
            ['name' => 'Andi Wijaya', 'role' => 'Siswa ATP', 'content' => 'Jurusan ATP sangat membantu saya mempelajari pertanian modern.', 'photo_path' => 'https://i.pravatar.cc/100?img=13'],
        ];

        foreach ($testimonials as $i => $item) {
            Testimonial::updateOrCreate(
                ['name' => $item['name']],
                $item + ['sort' => $i, 'is_active' => true]
            );
        }

        // --- Partner / Mitra ---
        $partners = [
            'DINAS PENDIDIKAN',
            'DINAS PERTANIAN',
            'KOMINFO KABUPATEN',
            'PERTAHUNGAN',
            'BANK INDONESIA',
            'POLTEKNIK',
        ];

        foreach ($partners as $i => $name) {
            Partner::updateOrCreate(['name' => $name], ['sort' => $i, 'is_active' => true]);
        }
    }
}
