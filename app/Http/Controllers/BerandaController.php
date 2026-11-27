<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Pengumuman;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    private Produk $produk;
    private Favorit $favorit;
    private Pengumuman $pengumuman;

    
    public function __construct()
{
    $this->produk = new Produk();
    $this->favorit = new Favorit();
}

    public function dashboard()
    {
        $daftar = $this->produk->semua();

        $favoritId = $this->favorit->idProduk(
            (int) Auth::id()
        );

       $pengumuman = [];

        return view('user.dashboard', [
            'produk' => $daftar,
            'favoritId' => $favoritId,
            'pengumuman' => $pengumuman,
            'flashKeranjang' => session()->pull('flash.keranjang'),
            'flashFavorit' => session()->pull('flash.favorit'),
        ]);
    }
}
