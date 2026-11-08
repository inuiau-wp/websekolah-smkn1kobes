<div align="center">

# Web SMKN 1 Kota Besi

**Website resmi SMKN 1 Kotabesi** — portal informasi sekolah dan dashboard pengelolaan konten multi-role.

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL%2FMariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square&logo=vite&logoColor=white)
![Petunjuk Pengujian](https://img.shields.io/badge/Tes-182_lulus-16A34A?style=flat-square)

> **Rapor Pendidikan** kini ada di navbar: data mutu per tahun, grafik perbandingan antar tahun, dan CRUD admin dengan template impor `.md`/`.csv`. Lihat [Rapor Pendidikan (Admin)](#rapor-pendidikan-admin).

</div>

---

## Daftar Isi

- [Halaman Publik](#halaman-publik)
- [Dashboard Multi-Role](#dashboard-multi-role)
- [Sistem Posting](#sistem-posting)
- [Teknologi](#teknologi)
- [Instalasi Lokal](#instalasi-lokal)
- [Struktur Proyek](#struktur-proyek)
- [Keamanan](#keamanan)
- [Deploy ke Hosting Bersama](#deploy-ke-hosting-bersama)

---

## Halaman Publik

| Halaman | Rute | Isi |
|---|---|---|
| 🏠 **Beranda** | `/` | Hero, carousel kegiatan, statistik sekolah, kartu 3 jurusan, berita & galeri unggulan |
| ℹ️ **Profil Sekolah** | `/profil` | Visi & misi, sejarah, akreditasi, dewan guru (dengan tautan ke halaman pribadi guru) |
| 🎓 **Jurusan** | `/jurusan` | ATP, TKJ, AKL: deskripsi, kompetensi, visi & misi, kepala jurusan |
| 📰 **Berita** | `/berita` | Filter kategori: Berita, Pengumuman, Agenda, Modul Materi, Tutorial |
| 📷 **Galeri** | `/galeri` | Album kegiatan sekolah |
| 📞 **Kontak** | `/kontak` | Peta lokasi dan form pesan masuk ke admin |
| 👤 **Halaman Guru** | `/guru/{id}` | Biodata (tanpa NIP), CV tertulis, prestasi, artikel & modul guru |
| 📊 **Rapor Pendidikan** | `/rapor-pendidikan/{tahun}` | Data mutu per tahun (hanya tahun yang dipublikasikan): KPI, tab mutu akademik/vokasi/iklim, grafik, tabel indikator dengan pencarian, disclaimer referensi PPID |
| 📊 **Perbandingan Rapor** | `/rapor-pendidikan/perbandingan` | Grafik garis antar tahun, dibangun otomatis dari semua tahun terpublikasi |

## Dashboard Multi-Role

| Role | Kemampuan |
|---|---|
| 🛡️ **Admin** | Konten beranda penuh (carousel, statistik, testimoni, mitra, foto hero), profil sekolah (logo, cover, koordinat peta, favicon ikut), jurusan, berita, galeri, rapor pendidikan, pesan kontak, akun guru & siswa |
| 📚 **Guru** | Mengelola berita/modul miliknya, galeri, jurusan yang dipimpin (kepala program), halaman pribadi (CV, prestasi, headline, tautan media sosial) |
| ✏️ **Siswa** | Melihat pengumuman & agenda sekolah, info jurusan, profil akun |

### Kelola Konten Beranda

Semua seksi beranda tampil dari database: `carousel_slides`, `statistics`, `testimonials`, `partners`, dan `settings` (termasuk upload foto hero, maks 2 MB). Admin bisa mengatur keempat seksi plus pengaturan umum dari dashboard tanpa menyentuh kode.

### Rapor Pendidikan (Admin)

- CRUD `admin/rapor-pendidikan` dengan form yang tampilannya meniru halaman publik 1:1, isian berulang via Alpine.js
- **Template unduh `.md` / `.csv`** (`admin/rapor-pendidikan/template/{format}`) dan **impor ke form** (`admin/rapor-pendidikan/import`), lengkap round-trip teruji
- Seeder data contoh tahun 2025 tersedia (`RaporPendidikanSeeder`)

## Sistem Posting

- Rich text editor **TinyMCE 7** dengan format, tabel, blok kode, perataan, dan ukuran gambar
- **Lampiran materi**: `.md` dikonversi otomatis ke editor, `.pdf` tampil ter-embed, `.docx` punya tombol unduh
- Upload gambar (jpg/png/webp/bmp/avif) plus tempel langsung tangkapan layar
- **Embed untuk konten**: paste tautan YouTube/Vimeo, atau tombol Embed untuk post Instagram
- HTML disanitasi saat disimpan, sehingga konten tidak membawa skrip berbahaya (anti-XSS)

## Teknologi

![Laravel](https://img.shields.io/badge/Laravel_13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS_4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![TinyMCE](https://img.shields.io/badge/TinyMCE_7-22BBDF?style=for-the-badge&logo=tinymce&logoColor=white)
![CommonMark](https://img.shields.io/badge/league%2Fcommonmark-4B8BBE?style=for-the-badge)
![MySQL](https://img.shields.io/badge/MySQL%2FMariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Breeze](https://img.shields.io/badge/Auth-Laravel_Breeze-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Pengujian](https://img.shields.io/badge/Tes_182_lulus-16A34A?style=for-the-badge&logo=pest&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js_3-0982BF?style=for-the-badge&logo=alpinedotjs&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
![Font Awesome](https://img.shields.io/badge/Font_Awesome_6.7-538DD7?style=for-the-badge&logo=fontawesome&logoColor=white)

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env    # isi kredensial DB, lalu:
php artisan key:generate
php artisan migrate --seed    # seed 3 jurusan (ATP, TKJ, AKL) + 1 admin
php artisan storage:link
npm run dev         # aset untuk pengembangan
# atau
npm run build       # aset untuk produksi
```

Target lokal: `http://smkn1kotabesi.test` (FlyEnv + Apache2).
Produksi: `https://smkn1kotabesi.sch.id`.

## Struktur Proyek

```
app/Http/Controllers/Admin|Guru|Siswa   # dashboard per role
resources/views                         # Blade (halaman publik + dashboard)
routes/web.php                          # rute publik & dashboard
DATABASE.md, DEPLOY.md, DESAIN.md       # dokumentasi internal (tidak dipublikasikan)
```

## Keamanan

- Password default pada seeder hanya untuk lingkungan dev (`migrate --seed`); sudahi segera saat produksi
- `.env` tidak pernah ikut dalam repo; buat manual di server, lalu jalankan `config:cache`
- **Registrasi publik ditutup**; akun guru dan siswa dibuat oleh admin
- Middleware role (`EnsureUserIsAdmin`/`Guru`/`Siswa`) membalas 404 (bukan 403) agar area admin tidak bisa ditebak
- Form kontak publik dibatasi `throttle:10,1` terhadap spam
- HTML berita disanitasi saat disimpan dan berkas yang diunggah dibatasi ukurannya (cover 4 MB, foto hero 2 MB)

## Perintah Composer

```bash
composer run setup    # install + key + migrate + npm install + build
composer run test     # config:clear + artisan test
```

## Deploy ke Hosting Bersama

Panduan lengkap ada di [DEPLOY.md](DEPLOY.md). Ringkasannya:

1. Jalankan `npm run build` lokal, lalu unggah proyek via FTP (FileZilla), kecuali `node_modules/`
2. Arahkan document root server ke `public/`
3. Buat `.env` di server dengan `APP_KEY` dan kredensial produksi
4. Pastikan `storage/` dan `bootstrap/cache/` dapat ditulis, lalu jalankan `migrate --force`
