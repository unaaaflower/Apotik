<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Keranjang;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class CheckoutController extends Controller
{
    private Keranjang $keranjang;
    private Pesanan $pesanan;
    private Alamat $alamat;

    /**
     * Pilihan pengiriman asli KlikMedis.
     */
    private const OPSI_PENGIRIMAN = [
        10000 => 'JNE Reguler (2-3 hari)',
        15000 => 'JNE YES (1 hari)',
    ];

    public function __construct()
    {
        $this->keranjang = new Keranjang();
        $this->pesanan = new Pesanan();
        $this->alamat = new Alamat();
    }

    /**
     * HALAMAN CHECKOUT
     */
    public function index(Request $request)
    {
        $userId = (int) Auth::id();
        $nama = Auth::user()->nama ?? '';

        /*
         * Ambil alamat milik pengguna.
         */
        try {
            $semuaAlamat = $this->alamat->semua($userId, $nama);
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return response(
                'Terjadi kesalahan saat mengambil alamat pengiriman.',
                500
            );
        }

        /*
         * PROSES PERUBAHAN ALAMAT
         */
        if (
            $request->isMethod('post') &&
            $request->input('aksi') === 'ubah_alamat'
        ) {
            return $this->prosesUbahAlamat(
                $request,
                $userId,
                $semuaAlamat
            );
        }

        /*
         * Ambil isi keranjang.
         */
        try {
            $items = $this->keranjang->daftar($userId);

            if (!$items) {
                return redirect('/keranjang')->with('keranjang', [
                    'ok' => false,
                    'pesan' => 'Keranjang masih kosong. Tambahkan produk dulu sebelum checkout.',
                ]);
            }

            $error = '';

            /*
             * KONFIRMASI PESANAN
             */
            if (
                $request->isMethod('post') &&
                !$request->has('aksi')
            ) {
                return $this->konfirmasi(
                    $request,
                    $items,
                    $userId,
                    $semuaAlamat
                );
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return response(
                'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                500
            );
        }

        $subtotal = Keranjang::subtotal($items);

        return view('user.checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'alamat' => $semuaAlamat['utama'],
            'flash' => session()->pull('flash_alamat'),
            'errorCheckout' => $error,
            'opsiPengiriman' => self::OPSI_PENGIRIMAN,
        ]);
    }

    /**
     * PROSES PERUBAHAN ALAMAT
     */
    private function prosesUbahAlamat(
        Request $request,
        int $userId,
        array $semuaAlamat
    ) {
        $label = $request->input('label');
        $nama = trim((string) $request->input('nama', ''));
        $telp = trim((string) $request->input('telp', ''));
        $alamat = trim((string) $request->input('alamat', ''));

        $error = '';

        if (!isset($semuaAlamat[$label])) {
            $error = 'Alamat tidak ditemukan.';
        } elseif ($nama === '' || mb_strlen($nama) > 100) {
            $error = 'Nama penerima wajib diisi (maksimal 100 karakter).';
        } elseif (!preg_match('/^[0-9+\s\-]{8,20}$/', $telp)) {
            $error = 'No. HP tidak valid (8–20 karakter, hanya angka, spasi, + atau -).';
        } elseif (mb_strlen($alamat) < 10 || mb_strlen($alamat) > 300) {
            $error = 'Alamat lengkap harus 10–300 karakter.';
        }

        if ($error === '') {
            try {
                $this->alamat->ubah(
                    $userId,
                    $label,
                    $nama,
                    $telp,
                    $alamat
                );

                session()->flash('flash_alamat', [
                    'ok' => true,
                    'label' => $label,
                    'pesan' => 'Alamat berhasil diperbarui.',
                ]);

            } catch (Throwable $e) {
                Log::error($e->getMessage());

                session()->flash('flash_alamat', [
                    'ok' => false,
                    'label' => $label,
                    'pesan' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                ]);
            }
        } else {
            session()->flash('flash_alamat', [
                'ok' => false,
                'label' => $label,
                'pesan' => $error,
            ]);
        }

        return redirect('/checkout');
    }

    /**
     * KONFIRMASI PESANAN
     */
    private function konfirmasi(
        Request $request,
        array $items,
        int $userId,
        array $semuaAlamat
    ) {
        $ongkir = $request->input('shipping');

        if (
            !is_string($ongkir) ||
            $ongkir === '' ||
            !ctype_digit($ongkir) ||
            !isset(self::OPSI_PENGIRIMAN[(int) $ongkir])
        ) {
            return back()->withInput()->with(
                'errorCheckout',
                'Pilihan pengiriman tidak valid. Silakan coba lagi.'
            );
        }

        if ($request->input('payment') !== 'cod') {
            return back()->withInput()->with(
                'errorCheckout',
                'Metode pembayaran tidak valid. Silakan coba lagi.'
            );
        }

        try {
            $kode = $this->pesanan->buatDariKeranjang(
                $userId,
                $items,
                (int) $ongkir,
                self::OPSI_PENGIRIMAN[(int) $ongkir],
                $semuaAlamat['utama']
            );

            return redirect()->route('pembayaran', [
                'kode' => $kode,
            ]);

        } catch (Throwable $e) {
            Log::error($e->getMessage());

            return back()->withInput()->with(
                'errorCheckout',
                'Pesanan gagal dibuat. Silakan coba lagi.'
            );
        }
    }
}
