<?php

namespace App\Http\Controllers;

// Menggunakan model Pesanan untuk mengambil data pesanan dan item pesanan.
use App\Models\Pesanan;

// Menggunakan Request untuk membaca parameter dari URL.
use Illuminate\Http\Request;

// Mengambil informasi pengguna yang sedang login.
use Illuminate\Support\Facades\Auth;

// Mencatat error ke dalam file log Laravel.
use Illuminate\Support\Facades\Log;

// Menangani berbagai jenis exception/error.
use Throwable;

class PembayaranController extends Controller
{
    // Properti untuk menyimpan objek model Pesanan.
    private Pesanan $pesanan;

    // Constructor dijalankan otomatis ketika controller dibuat.
    public function __construct()
    {
        // Membuat objek dari model Pesanan. diinisialsasi
        $this->pesanan = new Pesanan();
    }

    // Menampilkan halaman pembayaran.
    public function show(Request $request) // menggunakan metode get atau post
    {
        // Mengambil ID pengguna yang sedang login.
        $userId = (int) Auth::id();

        // Mengambil parameter 'kode' dari query URL.
        $kode = $request->query('kode'); // contphnya http://localhost:8000/pembayaran?kode=SK-0001

        // Memastikan kode berupa string, lalu menghapus spasi di awal dan akhir.
        // Jika bukan string, kode diisi string kosong.
        $kode = is_string($kode) ? trim($kode) : '';

        // Memastikan format kode pesanan sesuai pola SK- diikuti 4 sampai 10 angka.
        if (!preg_match('/^SK-\d{4,10}$/', $kode)) {
            return $this->pesananTidakDitemukan();
        }

        try {
            // Mencari pesanan berdasarkan kode dan ID pemiliknya.
            $order = $this->pesanan->cariByKode($userId, $kode);

            // Jika pesanan tidak ditemukan, arahkan pengguna kembali.
            if (!$order) {
                return $this->pesananTidakDitemukan();
            }

            // Mengambil daftar barang berdasarkan ID pesanan.
            $items = $this->pesanan->items((int) $order['id']);

        } catch (Throwable $e) {
            // Mencatat pesan error untuk kebutuhan pemeriksaan oleh developer.
            Log::error('Gagal menampilkan halaman pembayaran: ' . $e->getMessage());

            // Menampilkan respons error 500 kepada pengguna.
            abort(500, 'Terjadi kesalahan saat menampilkan pembayaran.');
        }

        // Mengirim data pesanan dan barang ke halaman pembayaran.
        return view('user.pembayaran', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    // Menangani kondisi ketika pesanan tidak ditemukan atau tidak dapat diakses.
    private function pesananTidakDitemukan()
    {
        // Mengarahkan pengguna ke tab pesanan pada halaman profil
        // sekaligus mengirim pesan sementara melalui session.
        return redirect('/profil#tab-pesanan')->with('pesanan', [
            'ok' => false,
            'pesan' => 'Pesanan tidak ditemukan.',
        ]);
    }
}
