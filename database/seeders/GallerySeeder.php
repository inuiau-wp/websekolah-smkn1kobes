<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Seed contoh foto galeri (placeholder — foto asli akan diunggah
     * lewat dashboard admin). Kategori bebas, dipakai untuk pengelompokan.
     */
    public function run(): void
    {
        $items = [
            // Kegiatan
            ['title' => 'Upacara Bendera', 'category' => 'Kegiatan', 'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80'],
            ['title' => 'Praktik di Laboratorium', 'category' => 'Kegiatan', 'image' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=800&q=80'],
            ['title' => 'Kegiatan Pesantren Ramadhan', 'category' => 'Kegiatan', 'image' => 'https://images.unsplash.com/photo-1544717298-f28559b7efc2?w=800&q=80'],

            // Fasilitas
            ['title' => 'Laboratorium Komputer', 'category' => 'Fasilitas', 'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80'],
            ['title' => 'Ruang Praktik Siswa', 'category' => 'Fasilitas', 'image' => 'https://images.unsplash.com/photo-1610873083197-2c124099236e?w=800&q=80'],
            ['title' => 'Gedung Sekolah', 'category' => 'Fasilitas', 'image' => 'https://images.unsplash.com/photo-1564981797816-1043664bf5cf?w=800&q=80'],

            // Ekstrakurikuler
            ['title' => 'Ekstrakurikuler Pramuka', 'category' => 'Ekstrakurikuler', 'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=800&q=80'],
            ['title' => 'Lomba Kreativitas Siswa', 'category' => 'Ekstrakurikuler', 'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80'],
            ['title' => 'Kegiatan Olahraga', 'category' => 'Ekstrakurikuler', 'image' => 'https://images.unsplash.com/photo-1521412644187-c49fa049e84d?w=800&q=80'],

            // Industri / PKL
            ['title' => 'Kunjungan Industri', 'category' => 'Industri', 'image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=800&q=80'],
            ['title' => 'PKL Siswa TKJ', 'category' => 'Industri', 'image' => 'https://images.unsplash.com/photo-1580894732444-8ecded7900cd?w=800&q=80'],
            ['title' => 'Sertifikasi Kompetensi', 'category' => 'Industri', 'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80'],
        ];

        foreach ($items as $item) {
            Gallery::updateOrCreate(
                ['title' => $item['title']],
                [
                    'image_path' => $item['image'],
                    'category' => $item['category'],
                    'uploaded_by' => null,
                ],
            );
        }
    }
}
