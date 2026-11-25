<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoritController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PengingatController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\ProfilController;

/*
|--------------------------------------------------------------------------
| Daftar Route Aplikasi KlikMedis
|--------------------------------------------------------------------------
*/

// Halaman utama
Route::get('/', function () {
    return redirect('/login');
})->name('home');

// Tamu: Login dan Registrasi
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'login'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'authenticate'])
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'register'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'store'])
        ->name('register.store');

});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// User yang sudah login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [BerandaController::class, 'dashboard'])
        ->name('dashboard');

    Route::match(['GET', 'POST'], '/favorit', [FavoritController::class, 'index'])
        ->name('favorit');

    Route::match(['GET', 'POST'], '/pengingat-obat', [PengingatController::class, 'index'])
        ->name('pengingat');

    Route::match(['GET', 'POST'], '/profil', [ProfilController::class, 'index'])
        ->name('profil');

    // Keranjang -> Checkout -> Pembayaran
    Route::match(['GET', 'POST'], '/keranjang', [KeranjangController::class, 'index'])
        ->name('keranjang');

    Route::match(['GET', 'POST'], '/checkout', [CheckoutController::class, 'index'])
        ->name('checkout');

    Route::get('/pembayaran', [PembayaranController::class, 'show'])
        ->name('pembayaran');

});

// Panel Admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard Admin
    Route::match(['GET', 'POST'], '/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    // Manajemen Obat
    Route::get('/obat', [ObatController::class, 'index'])
        ->name('admin.obat');

    Route::post('/obat', [ObatController::class, 'store'])
        ->name('admin.obat.store');

    Route::get('/obat/{id}', [ObatController::class, 'show'])
        ->name('admin.obat.show');

    Route::put('/obat/{id}', [ObatController::class, 'update'])
        ->name('admin.obat.update');

    Route::delete('/obat/{id}', [ObatController::class, 'destroy'])
        ->name('admin.obat.destroy');

    // Manajemen Pesanan
    Route::match(['GET', 'POST'], '/pesanan', [PesananController::class, 'index'])
        ->name('admin.pesanan');

    // Manajemen Promo
    Route::match(['GET', 'POST'], '/promo', [PromoController::class, 'index'])
        ->name('admin.promo');

});
