<?php

// Mengimpor Route agar kita bisa mendefinisikan URL aplikasi Laravel.
use Illuminate\Support\Facades\Route;

// Mengimpor controller untuk mengelola fitur admin.
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PromoController;

// Mengimpor controller untuk autentikasi pengguna.
use App\Http\Controllers\AuthController;

// Mengimpor controller untuk halaman beranda/dashboard.
use App\Http\Controllers\BerandaController;

// Mengimpor controller untuk proses checkout.
use App\Http\Controllers\CheckoutController;

// Mengimpor controller untuk fitur favorit.
use App\Http\Controllers\FavoritController;

// Mengimpor controller untuk fitur keranjang.
use App\Http\Controllers\KeranjangController;

// Mengimpor controller untuk pengingat obat.
use App\Http\Controllers\PengingatController;

// Mengimpor controller untuk halaman pembayaran.
use App\Http\Controllers\PembayaranController;

// Mengimpor controller untuk profil pengguna.
use App\Http\Controllers\ProfilController;

// Mengimpor middleware untuk mencegah akses kembali ke halaman
// yang seharusnya tidak dapat dibuka setelah logout.
use App\Http\Middleware\PreventBackHistory;


/*
|--------------------------------------------------------------------------
| Daftar Route Aplikasi KlikMedis
|--------------------------------------------------------------------------
*/

// HALAMAN UTAMA
// Ketika pengguna membuka URL utama (/),
// arahkan pengguna ke halaman login.
Route::get('/', function () {
    return redirect('/login');
})->name('home');


// T A M U : LOGIN DAN REGISTRASI
// Middleware guest membatasi route ini untuk pengguna yang belum login.
Route::middleware('guest')->group(function () {

    // GET /login: menampilkan halaman login.
    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    // POST /login: memproses data login yang dikirim pengguna.
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->name('login.process');

    // GET /register: menampilkan halaman registrasi.
    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');

    // POST /register: menyimpan data pengguna baru.
    Route::post('/register', [AuthController::class, 'store'])
        ->name('register.store');

});


// LOGOUT
// Logout hanya bisa dilakukan oleh pengguna yang sudah login.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// USER YANG SUDAH LOGIN
// Semua route dalam group ini wajib melewati middleware auth
// dan PreventBackHistory.
Route::middleware([
    'auth',
    PreventBackHistory::class
])->group(function () {

    // Menampilkan dashboard pengguna.
    Route::get('/dashboard', [BerandaController::class, 'dashboard'])
        ->name('dashboard');

    // GET dan POST digunakan untuk melihat dan memproses data favorit.
    Route::match(['GET', 'POST'], '/favorit', [FavoritController::class, 'index'])
        ->name('favorit');

    // GET dan POST digunakan untuk melihat dan memproses pengingat obat.
    Route::match(['GET', 'POST'], '/pengingat-obat', [PengingatController::class, 'index'])
        ->name('pengingat');

    // GET dan POST digunakan untuk melihat dan memproses profil.
    Route::match(['GET', 'POST'], '/profil', [ProfilController::class, 'index'])
        ->name('profil');


    // ALUR KERANJANG -> CHECKOUT -> PEMBAYARAN

    // GET: menampilkan keranjang.
    // POST: memproses tambah, ubah, atau hapus produk.
    Route::match(['GET', 'POST'], '/keranjang', [KeranjangController::class, 'index'])
        ->name('keranjang'); // nama route

    // GET: menampilkan halaman checkout.
    // POST: memproses perubahan alamat atau konfirmasi pesanan.
    Route::match(['GET', 'POST'], '/checkout', [CheckoutController::class, 'index'])
        ->name('checkout');

    // GET: menampilkan halaman pembayaran atau hasil konfirmasi.
    Route::get('/pembayaran', [PembayaranController::class, 'show'])
        ->name('pembayaran');

});


// PANEL ADMIN
// prefix('admin') membuat semua URL dalam group diawali /admin.
// Middleware auth memastikan pengguna sudah login.
// Middleware admin memastikan pengguna memiliki hak akses admin.
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // DASHBOARD ADMIN
    // GET dan POST ditangani oleh method dashboard().
    Route::match(['GET', 'POST'], '/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');


    // MANAJEMEN OBAT

    // Menampilkan daftar obat.
    Route::get('/obat', [ObatController::class, 'index'])
        ->name('admin.obat');

    // Menambahkan obat baru.
    Route::post('/obat', [ObatController::class, 'store'])
        ->name('admin.obat.store');

    // Menampilkan detail obat berdasarkan ID.
    Route::get('/obat/{id}', [ObatController::class, 'show'])
        ->name('admin.obat.show');

    // Memperbarui data obat berdasarkan ID.
    Route::put('/obat/{id}', [ObatController::class, 'update'])
        ->name('admin.obat.update');

    // Menghapus obat berdasarkan ID.
    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])
        ->name('admin.obat.destroy');


    // MANAJEMEN PESANAN
    // GET dan POST ditangani oleh method index().
    Route::match(['GET', 'POST'], '/pesanan', [PesananController::class, 'index'])
        ->name('admin.pesanan');


    // MANAJEMEN PROMO
    // GET dan POST ditangani oleh method index().
    Route::match(['GET', 'POST'], '/promo', [PromoController::class, 'index'])
        ->name('admin.promo');

});
