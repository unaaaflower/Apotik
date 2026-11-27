<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Statistik;

/**
 * CONTROLLER (admin) — Dashboard Admin: ringkasan toko.
 *
 * Mengambil data dari model Statistik.
 * Akses dijaga middleware 'admin' pada routes/web.php.
 *
 * Alur:
 * Controller -> Model -> View.
 */
class AdminController extends Controller
{
    private Statistik $statistik;

    public function __construct()
    {
        $this->statistik = new Statistik();
    }

    /**
     * Menampilkan Dashboard Admin.
     */
    public function dashboard()
    {
        $ringkasan = $this->statistik->ringkasan();

        $promo = $this->statistik->promoBerjalan();

        $pesanan = $this->statistik->pesananTerbaru();

        return view('admin.dashboard', [
            'ringkasan' => $ringkasan,
            'promo' => $promo,
            'pesanan' => $pesanan,
        ]);
    }

    /**
     * Menampilkan halaman manajemen obat.
     */
    public function obat()
    {
        return view('admin.obat');
    }
}
