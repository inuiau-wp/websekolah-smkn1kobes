<?php

namespace Database\Seeders;

use App\Models\RaporPendidikan;
use Illuminate\Database\Seeder;

/**
 * Rapor Pendidikan 2025 — SMKN 1 Kota Besi (NPSN 30205228).
 * Data agregasi Level 2 dari Laporan Rapor Pendidikan Kemendikdasmen RI.
 */
class RaporPendidikanSeeder extends Seeder
{
    public function run(): void
    {
        RaporPendidikan::updateOrCreate(
            ['year' => 2025],
            [
                'title' => 'Laporan Transparansi Mutu & Relevansi Vokasi SMKN 1 Kota Besi (Capaian Rapor Pendidikan 2025)',
                'intro' => 'Sebagai bentuk tanggung jawab publik dan kepatuhan terhadap UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik, SMK Negeri 1 Kota Besi menyajikan ringkasan indikator capaian Rapor Pendidikan Tahun Rilis 2025 (berdasarkan Evaluasi Sistem Pendidikan Kementerian Pendidikan Dasar dan Menengah RI). Seluruh data disajikan dalam bentuk Agregasi Entitas Institusi (Level 2) guna menjamin keterbukaan informasi sekaligus melindungi privasi individu peserta didik dan tenaga pendidik sesuai UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP).',
                'disclaimer' => 'Seluruh data dan statistik yang tersaji pada halaman ini diekstraksi secara resmi dari Laporan Rapor Pendidikan Kementerian Pendidikan Dasar dan Menengah RI Tahun Rilis 2025 untuk SMK Negeri 1 Kota Besi. Data dipublikasikan semata-mata untuk memenuhi akuntabilitas publik (UU No. 14/2008) dalam bentuk agregasi makro institusi tanpa menyajikan data pribadi individu, sesuai ketentuan UU No. 27/2022 tentang Pelindungan Data Pribadi (UU PDP). Interpretasi atau penggunaan data oleh pihak ketiga di luar kepentingan transparansi pendidikan sepenuhnya menjadi tanggung jawab pengguna.',
                'is_published' => true,
                'data' => [
                    'kpi' => [
                        ['key' => 'literasi', 'label' => 'Kemampuan Literasi', 'value' => 75.53, 'suffix' => '', 'kategori' => 'Baik', 'tren' => '+2,30', 'tren_note' => 'vs 2024 (73,23)'],
                        ['key' => 'numerasi', 'label' => 'Kemampuan Numerasi', 'value' => 58.57, 'suffix' => '', 'kategori' => 'Sedang', 'tren' => '+0,43', 'tren_note' => 'vs 2024 (58,14)'],
                        ['key' => 'dudi', 'label' => 'Kepuasan Dunia Kerja (DUDI)', 'value' => 93.33, 'suffix' => '%', 'kategori' => 'Sangat Baik', 'tren' => null, 'tren_note' => null],
                        ['key' => 'keamanan', 'label' => 'Iklim Keamanan Sekolah', 'value' => 74.41, 'suffix' => '', 'kategori' => 'Aman', 'tren' => null, 'tren_note' => null],
                    ],
                    'sections' => [
                        'akademik' => [
                            [
                                'title' => 'Kemampuan Literasi Peserta Didik',
                                'skor' => '75,53',
                                'kategori' => 'Baik',
                                'catatan' => '91,11% peserta didik SMKN 1 Kota Besi telah mencapai batas kompetensi minimum membaca, memahami, dan menganalisis teks.',
                                'poin' => [
                                    'Tren meningkat +2,30 dibanding tahun 2024 (73,23).',
                                    'Fokus pembiasaan: penguatan literasi digital berbasis sains dan modul kerja vokasi.',
                                ],
                            ],
                            [
                                'title' => 'Kemampuan Numerasi Peserta Didik',
                                'skor' => '58,57',
                                'kategori' => 'Sedang',
                                'catatan' => '68,89% peserta didik mencapai batas minimum kompetensi numerasi.',
                                'poin' => [
                                    'Tren meningkat +0,43 dibanding tahun 2024 (58,14).',
                                    'Tindak lanjut RKT: integrasi matematika terapan langsung pada praktik keahlian di bengkel/laboratorium (misal: perhitungan estimasi bahan dan biaya perbaikan).',
                                ],
                            ],
                            [
                                'title' => 'Karakter & Profil Pelajar Pancasila',
                                'skor' => '57,21',
                                'kategori' => 'Baik',
                                'catatan' => 'Pembentukan karakter peserta didik melalui penguatan enam dimensi Profil Pelajar Pancasila.',
                                'poin' => [
                                    'Indikator unggulan: Beriman & Bertakwa kepada Tuhan YME, Gotong Royong, dan Kebinekaan Global.',
                                ],
                            ],
                        ],
                        'vokasi' => [
                            [
                                'title' => 'Kepuasan Dunia Kerja (Mitra DUDI)',
                                'skor' => '93,33%',
                                'kategori' => 'Sangat Baik',
                                'catatan' => 'Mitra Dunia Usaha dan Dunia Industri (DUDI) memberikan penilaian sangat tinggi terhadap soft skills, etika kerja, serta kedisiplinan alumni SMKN 1 Kota Besi selama praktik kerja lapangan (PKL) maupun kerja pasca-lulus.',
                                'poin' => [],
                            ],
                            [
                                'title' => 'Penyerapan Lulusan (Tracer Study)',
                                'skor' => '75,70%',
                                'kategori' => 'Sedang',
                                'catatan' => 'Tingkat penyerapan lulusan (Bekerja, Melanjutkan Pendidikan, Wirausaha).',
                                'poin' => [
                                    'Lulusan terdistribusi pada tiga jalur utama: Bekerja di Industri, Melanjutkan Pendidikan Tinggi, dan Wirausaha Mandiri.',
                                ],
                            ],
                            [
                                'title' => 'Kemitraan & Teaching Factory (TeFa)',
                                'skor' => '59,96',
                                'kategori' => 'Sedang',
                                'catatan' => 'Skor Link & Match dunia kerja serta kualitas Teaching Factory.',
                                'poin' => [
                                    'Skor Kualitas Teaching Factory (TeFa): 52,04.',
                                    'Kehadiran Guru Tamu Industri: 57,82.',
                                    'Undangan kolaborasi: SMKN 1 Kota Besi membuka peluang seluas-luasnya bagi mitra industri untuk berkolaborasi dalam sinkronisasi kurikulum, program guru tamu, dan sertifikasi kompetensi siswa.',
                                ],
                            ],
                        ],
                        'kelola' => [
                            [
                                'title' => 'Iklim Keamanan Sekolah',
                                'skor' => '74,41',
                                'kategori' => 'Aman',
                                'catatan' => 'Sekolah menjamin lingkungan belajar yang bebas dari perundungan, kekerasan fisik, dan diskriminasi.',
                                'poin' => [],
                            ],
                            [
                                'title' => 'Iklim Kebinekaan Sekolah',
                                'skor' => '78,56',
                                'kategori' => 'Sangat Baik',
                                'catatan' => 'Lingkungan sekolah yang inklusif dan menghargai keragaman suku, agama, dan latar belakang sosial-ekonomi.',
                                'poin' => [],
                            ],
                            [
                                'title' => 'Ketepatan Waktu Pelaporan Dana BOSP',
                                'skor' => '100%',
                                'kategori' => 'Sangat Baik',
                                'catatan' => 'Pelaporan Dana Bantuan Operasional Sekolah tepat waktu.',
                                'poin' => [],
                            ],
                            [
                                'title' => 'Pemanfaatan TIK untuk Perencanaan',
                                'skor' => 'Sangat Baik',
                                'kategori' => 'Sangat Baik',
                                'catatan' => 'Penggunaan platform SIPLah dan ARKAS secara transparan.',
                                'poin' => [],
                            ],
                        ],
                    ],
                    'charts' => [
                        'bar' => [
                            'labels' => ['Literasi', 'Numerasi', 'Penyerapan Lulusan', 'TeFa'],
                            'label_sebelum' => '2024',
                            'label_sesudah' => '2025',
                            'sebelum' => [73.23, 58.14, 86.75, 51.00],
                            'sesudah' => [75.53, 58.57, 75.70, 52.04],
                        ],
                        'radar' => [
                            'labels' => ['Kepuasan DUDI', 'Link & Match', 'Guru Tamu Industri', 'Kurikulum'],
                            'values' => [93.33, 59.96, 57.82, 64.20],
                        ],
                    ],
                    'indicators' => [
                        ['kode' => 'A.1', 'indikator' => 'Kemampuan Literasi Peserta Didik', 'capaian' => '75,53', 'status' => 'Baik', 'narasi' => 'Penguatan literasi digital berbasis sains dan modul kerja vokasi.'],
                        ['kode' => 'A.2', 'indikator' => 'Kemampuan Numerasi Peserta Didik', 'capaian' => '58,57', 'status' => 'Sedang', 'narasi' => 'Integrasi matematika terapan pada praktik keahlian di bengkel/laboratorium.'],
                        ['kode' => 'A.3', 'indikator' => 'Karakter & Profil Pelajar Pancasila', 'capaian' => '57,21', 'status' => 'Baik', 'narasi' => 'Penguatan dimensi Beriman-Bertakwa, Gotong Royong, dan Kebinekaan Global.'],
                        ['kode' => 'D.1', 'indikator' => 'Iklim Keamanan Sekolah', 'capaian' => '74,41', 'status' => 'Aman', 'narasi' => 'Menjaga lingkungan bebas perundungan, kekerasan fisik, dan diskriminasi.'],
                        ['kode' => 'D.2', 'indikator' => 'Iklim Kebinekaan Sekolah', 'capaian' => '78,56', 'status' => 'Sangat Baik', 'narasi' => 'Pembiasaan inklusivitas dan penghargaan keragaman.'],
                        ['kode' => 'E.1', 'indikator' => 'Kepuasan Dunia Kerja (DUDI)', 'capaian' => '93,33%', 'status' => 'Sangat Baik', 'narasi' => 'Memelihara kualitas soft skills & etika kerja alumni melalui PKL dan kemitraan.'],
                        ['kode' => 'E.2', 'indikator' => 'Penyerapan Lulusan (BMW)', 'capaian' => '75,70%', 'status' => 'Sedang', 'narasi' => 'Penguatan kemitraan industri & bimbingan karier untuk meningkatkan penyerapan.'],
                        ['kode' => 'E.3', 'indikator' => 'Link & Match Dunia Kerja', 'capaian' => '59,96', 'status' => 'Sedang', 'narasi' => 'Sinkronisasi kurikulum dengan mitra industri dan perluasan TeFa.'],
                        ['kode' => 'E.4', 'indikator' => 'Kualitas Teaching Factory (TeFa)', 'capaian' => '52,04', 'status' => 'Sedang', 'narasi' => 'Peningkatan mutu unit produksi TeFa dan keterlibatan guru tamu industri (57,82).'],
                        ['kode' => 'F.1', 'indikator' => 'Ketepatan Waktu Pelaporan Dana BOSP', 'capaian' => '100%', 'status' => 'Sangat Baik', 'narasi' => 'Pelaporan tepat waktu melalui ARKAS secara transparan.'],
                        ['kode' => 'F.2', 'indikator' => 'Pemanfaatan TIK untuk Perencanaan', 'capaian' => 'Sangat Baik', 'status' => 'Sangat Baik', 'narasi' => 'Penggunaan SIPLah dan ARKAS untuk perencanaan & pengadaan.'],
                    ],
                ],
            ]
        );
    }
}
