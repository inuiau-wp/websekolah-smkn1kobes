<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Seed contoh berita/pengumuman/agenda (placeholder — konten asli
     * akan dimasukkan lewat dashboard admin).
     */
    public function run(): void
    {
        $items = [
            [
                'title' => 'Penerimaan Peserta Didik Baru Tahun Ajaran 2026/2027',
                'excerpt' => 'Pendaftaran PPDB untuk tahun ajaran 2026/2027 telah dibuka. Informasi seleksi dan jadwal akan diperbarui oleh admin.',
                'content' => 'Pendaftaran Peserta Didik Baru (PPDB) SMKN 1 Kotabesi tahun ajaran 2026/2027 telah dibuka.\n\nTersedia tiga jurusan pilihan: Agribisnis Tanaman Perkebunan (ATP), Teknik Komputer dan Jaringan (TKJ), serta Akuntansi Keuangan Lembaga (AKL).\n\nDetail persyaratan pendaftaran akan diisi melalui dashboard admin.',
                'category' => 'announcement',
                'jurusan_code' => null,
                'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&q=80',
            ],
            [
                'title' => 'Siswa TKJ Meraih Juara 1 Lomba Karya Ilmiah Remaja Tingkat Kabupaten',
                'excerpt' => 'Tim siswa Teknik Komputer dan Jaringan berhasil meraih juara 1 pada ajang LKIR tingkat kabupaten. (Konten contoh)',
                'content' => 'Tim siswa Teknik Komputer dan Jaringan (TKJ) SMKN 1 Kotabesi berhasil meraih juara 1 pada ajang Lomba Karya Ilmiah Remaja (LKIR) tingkat kabupaten.\n\nPrestasi ini merupakan contoh konten berita yang akan diganti oleh admin melalui dashboard.',
                'category' => 'news',
                'jurusan_code' => 'tkj',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=800&q=80',
            ],
            [
                'title' => 'Siswa AKL Lulus Sertifikasi Internasional Akuntansi',
                'excerpt' => 'Siswa Akuntansi Keuangan Lembaga mendapatkan sertifikasi internasional. (Konten contoh)',
                'content' => 'Siswi jurusan Akuntansi Keuangan Lembaga (AKL) berhasil mendapatkan sertifikasi internasional di bidang akuntansi.\n\nKonten ini adalah contoh; detail sebenarnya akan diisi oleh admin.',
                'category' => 'news',
                'jurusan_code' => 'akl',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&q=80',
            ],
            [
                'title' => 'Rapat Orang Tua Siswa Kelas XII',
                'excerpt' => 'Rapat sosialisasi kelulusan dan persiapan PKL untuk orang tua siswa kelas XII. (Konten contoh)',
                'content' => 'Sekolah mengundang orang tua/wali siswa kelas XII untuk hadir pada rapat sosialisasi kelulusan persiapan Praktik Kerja Lapangan (PKL).\n\nWaktu dan tempat akan dikonfirmasi admin.',
                'category' => 'agenda',
                'jurusan_code' => null,
                'image' => null,
            ],
            [
                'title' => 'Uji Kompetensi Keahlian (UKK) Jurusan ATP',
                'excerpt' => 'Agenda Uji Kompetensi Keahlian untuk jurusan Agribisnis Tanaman Perkebunan. (Konten contoh)',
                'content' => 'Uji Kompetensi Keahlian (UKK) jurusan Agribisnis Tanaman Perkebunan (ATP) akan diselenggarakan untuk siswa kelas XII.\n\nJadwal uji akan diinformasikan oleh admin.',
                'category' => 'agenda',
                'jurusan_code' => 'atp',
                'image' => null,
            ],
            [
                'title' => 'Kegiatan Praktik Kerja Lapangan Siswa TKJ',
                'excerpt' => 'Siswa TKJ menjalani PKL di industri mitra jaringan komputer. (Konten contoh)',
                'content' => 'Siswa kelas XI Teknik Komputer dan Jaringan mulai menjalani Praktik Kerja Lapangan di industri mitra bidang jaringan komputer.\n\nKonten contoh; akan diganti melalui dashboard admin.',
                'category' => 'news',
                'jurusan_code' => 'tkj',
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&q=80',
            ],
        ];

        foreach ($items as $item) {
            $jurusanId = $item['jurusan_code']
                ? Jurusan::where('slug', $item['jurusan_code'])->value('id')
                : null;

            News::updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title' => $item['title'],
                    'excerpt' => $item['excerpt'],
                    'content' => $item['content'],
                    'category' => $item['category'],
                    'jurusan_id' => $jurusanId,
                    'cover_image' => $item['image'],
                    'status' => 'published',
                    'published_at' => now()->subDays(random_int(1, 7)),
                ],
            );
        }
    }
}
