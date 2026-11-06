<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\RaporPendidikanController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Guru\PersonalPageController;
use App\Http\Controllers\GuruPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsImageUploadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\SekolahController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/berita', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{news:slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/kontak', [ContactController::class, 'index'])->name('contact.index');
Route::post('/kontak', [ContactController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contact.store');

Route::get('/profil', [SekolahController::class, 'profil'])->name('profil');

// Halaman pribadi guru (publik) — /guru/{id}, hanya id angka agar tidak bentrok /guru/* dashboard
Route::get('/guru/{user}', [GuruPageController::class, 'show'])
    ->whereNumber('user')
    ->name('guru.public.show');

Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
Route::get('/jurusan/{jurusan:slug}', [JurusanController::class, 'show'])->name('jurusan.show');

// Rapor Pendidikan (publik) — perbandingan antar tahun & /rapor-pendidikan/{tahun}
Route::get('/rapor-pendidikan/perbandingan', [RaporController::class, 'perbandingan'])
    ->name('rapor.perbandingan');

Route::get('/rapor-pendidikan/{year}', [RaporController::class, 'show'])
    ->whereNumber('year')
    ->name('rapor.show');

// Fase 8i — bahasa aplikasi ditetapkan Indonesia saja (switcher ID/EN dihapus)

Route::get('/dashboard', function () {
    return match (auth()->user()?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => redirect()->route('home'),
    };
})->middleware(['auth'])->name('dashboard');

// Area admin (diproteksi role admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::resource('jurusan', App\Http\Controllers\Admin\JurusanController::class)
        ->except(['show']); // detail jurusan sudah ada di halaman publik

    Route::resource('news', App\Http\Controllers\Admin\NewsController::class)
        ->except(['show']) // detail berita sudah ada di halaman publik
        ->parameters(['news' => 'news']);

    Route::resource('gallery', App\Http\Controllers\Admin\GalleryController::class)
        ->except(['show']); // detail foto = lightbox di halaman publik

    // Fase 8i-lanjutan — template & impor Rapor Pendidikan
    Route::get('rapor-pendidikan/template/{format}', [RaporPendidikanController::class, 'template'])
        ->whereIn('format', ['md', 'csv'])
        ->name('rapor-pendidikan.template');
    Route::post('rapor-pendidikan/import', [RaporPendidikanController::class, 'import'])
        ->name('rapor-pendidikan.import');

    // Fase 8i — Kelola Rapor Pendidikan (publikasi etis per tahun)
    Route::resource('rapor-pendidikan', RaporPendidikanController::class)
        ->except(['show'])
        ->parameters(['rapor-pendidikan' => 'rapor_pendidikan']);

    Route::get('home', [HomeContentController::class, 'index'])->name('home.index');
    Route::post('home/settings', [HomeContentController::class, 'updateSettings'])->name('home.settings');
    Route::post('home/{type}', [HomeContentController::class, 'store'])->name('home.store');
    Route::patch('home/{type}/{id}', [HomeContentController::class, 'update'])->name('home.update');
    Route::delete('home/{type}/{id}', [HomeContentController::class, 'destroy'])->name('home.destroy');

    // Fase 6 — Kelola Pesan Kontak
    Route::get('kontak', [ContactMessageController::class, 'index'])->name('kontak.index');
    Route::patch('kontak/{kontak}/tandai-dibaca', [ContactMessageController::class, 'markRead'])->name('kontak.read');
    Route::delete('kontak/{kontak}', [ContactMessageController::class, 'destroy'])->name('kontak.destroy');

    // Fase 6 — Kelola Profil Sekolah
    Route::get('profil', [SchoolProfileController::class, 'edit'])->name('profil.edit');
    Route::put('profil', [SchoolProfileController::class, 'update'])->name('profil.update');

    // Fase 6 — Kelola Akun Guru & Admin
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

});

// Area guru (diproteksi role guru)
Route::prefix('guru')->name('guru.')->middleware(['auth', 'guru'])->group(function () {
    Route::get('/', App\Http\Controllers\Guru\DashboardController::class)->name('dashboard');
    Route::resource('news', App\Http\Controllers\Guru\NewsController::class)
        ->except(['show'])
        ->parameters(['news' => 'news']);

    // Fase 8 — Guru mengelola galeri (berbagi logika dengan admin)
    Route::resource('gallery', App\Http\Controllers\Guru\GalleryController::class)
        ->except(['show'])
        ->parameters(['gallery' => 'gallery']);

    // Fase 8 — Kepala Program / Kepala Jurusan / Kaprog mengelola konten jurusan miliknya
    Route::prefix('jurusan')->name('jurusan.')->group(function () {
        Route::get('/', [App\Http\Controllers\Guru\JurusanController::class, 'index'])->name('index');
        Route::get('/{jurusan}/edit', [App\Http\Controllers\Guru\JurusanController::class, 'edit'])->name('edit');
        Route::patch('/{jurusan}', [App\Http\Controllers\Guru\JurusanController::class, 'update'])->name('update');
    });

    // Fase 8g — Kustomisasi halaman pribadi guru (publik: /guru/{id})
    Route::get('/pribadi', [PersonalPageController::class, 'edit'])->name('pribadi.edit');
    Route::patch('/pribadi', [PersonalPageController::class, 'update'])->name('pribadi.update');
});

// Area siswa (diproteksi role siswa)
Route::prefix('siswa')->name('siswa.')->middleware(['auth', 'siswa'])->group(function () {
    Route::get('/', App\Http\Controllers\Siswa\DashboardController::class)->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fase 8h-r — upload gambar untuk konten berita (TinyMCE), dipakai admin & guru
    Route::post('/dashboard/upload-image', [NewsImageUploadController::class, 'store'])
        ->name('news.image-upload.store');
});

require __DIR__.'/auth.php';
