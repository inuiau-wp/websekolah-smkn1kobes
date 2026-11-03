# Web SMKN 1 Kota Besi

Website resmi **SMKN 1 Kotabesi** — portal informasi sekolah dengan 3 jurusan unggulan dan dashboard multi-role untuk pengelolaan konten.

## Fitur Utama

### Halaman Publik
- **Beranda** — hero, carousel kegiatan, statistik sekolah, kartu 3 jurusan, berita & galeri unggulan
- **Profil Sekolah** `/profil` — visi-misi, dewan guru (dengan tombol *Kunjungi Halaman* ke halaman pribadi guru), cover & logo dinamis
- **Jurusan** `/jurusan` — ATP, TKJ, AKL: deskripsi, kompetensi, visi-misi, kepala jurusan
- **Berita & Pengumuman** `/berita` — filter kategori: Berita, Pengumuman, Agenda, **Modul Materi**, **Tutorial**
- **Galeri** `/galeri` & **Kontak** `/kontak` (map lokasi + pesan masuk ke admin)
- **Halaman Pribadi Guru** `/guru/{id}` — biodata (tanpa NIP), CV tertulis guru, prestasi, artikel/modul guru

### Dashboard Multi-Role
| Role | Kemampuan |
|---|---|
| **Admin** | Kelola konten home, profile sekolah (logo, cover, koordinat map), jurusan, berita, galeri, pesan kontak, akun guru/siswa |
| **Guru** | Berita/modul milik sendiri, galeri, jurusan yang dipimpin (kepala program), halaman pribadi (CV, prestasi, headline) |
| **Siswa** | Pengumuman & agenda sekolah |

### Sistem Posting Berita
- Rich text editor **TinyMCE** (format, tabel, blok kode, perataan, ukuran gambar)
- Kategori: Berita · Pengumuman · Agenda · **Modul Materi** · **Tutorial**
- Lampiran materi: **.md** (dikonversi ke editor) · **.pdf** (ter-embed) · **.docx** (tombol unduh)
- Upload gambar konten (jpg/png/webp/bmp/avif) + paste screenshot
- Embed **YouTube/Vimeo** (paste link) & **post Instagram** (tombol Embed)
- Konten disanitasi otomatis saat disimpan (anti-XSS)

## Teknologi
- Laravel 13 (PHP 8.3+) + Blade + Tailwind CSS v4
- TinyMCE 7, league/commonmark (Markdown→HTML), Vite
- MySQL/MariaDB — auth via Laravel Breeze
- Tests: PHPUnit/Pest — 158 pengujian

## Instalasi Lokal
```bash
composer install
npm install
cp .env.example .env   # isi DB, lalu:
php artisan key:generate
php artisan migrate --seed   # seed 3 jurusan + 1 admin
php artisan storage:link
npm run dev        # aset dev
# atau:
npm run build      # aset produksi
```
URL lokal: `http://smkn1kotabesi.test` (FlyEnv + Apache2)

## Struktur Ringkas
```
app/Http/Controllers/Admin|Guru|Siswa   # dashboard per role
resources/views                         # Blade (publik + dashboard)
routes/web.php                          # rute publik & dashboard
DATABASE.md, DEPLOY.md, DESAIN.md       # dokumentasi internal (tidak di-publish)
```

## Keamanan
- Password default seeder hanya untuk lokasi dev (`migrate --seed`); ganti segera di produksi
- `.env` tidak pernah disertakan dalam repo — buat manual di server
