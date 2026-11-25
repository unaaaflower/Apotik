<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PembayaranController extends Controller
{
    private Pesanan $pesanan;

    public function __construct()
    {
        $this->pesanan = new Pesanan();
    }

    public function show(Request $request)
    {
        $userId = (int) Auth::id();

        $kode = $request->query('kode');

        $kode = is_string($kode) ? trim($kode) : '';

        // Memastikan format kode pesanan benar.
        if (!preg_match('/^SK-\d{4,10}$/', $kode)) {
            return $this->pesananTidakDitemukan();
        }

        try {
            // Mengambil pesanan berdasarkan kode dan pemiliknya.
            $order = $this->pesanan->cariByKode($userId, $kode);

            if (!$order) {
                return $this->pesananTidakDitemukan();
            }

            // Mengambil daftar barang dalam pesanan.
            $items = $this->pesanan->items((int) $order['id']);

        } catch (Throwable $e) {
            Log::error('Gagal menampilkan halaman pembayaran: ' . $e->getMessage());

            abort(500, 'Terjadi kesalahan saat menampilkan pembayaran.');
        }

        return view('user.pembayaran', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    private function pesananTidakDitemukan()
    {
        return redirect('/profil#tab-pesanan')->with('pesanan', [
            'ok' => false,
            'pesan' => 'Pesanan tidak ditemukan.',
        ]);
    }
}
