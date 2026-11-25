<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public const ONGKIR_ESTIMASI = 10000;

    /**
     * Halaman yang boleh menjadi tujuan kembali.
     */
    private const HALAMAN_KEMBALI = [
        '/dashboard',
        '/favorit',
        '/keranjang',
    ];

    private Keranjang $keranjang;
    private Produk $produk;

    public function __construct()
    {
        $this->keranjang = new Keranjang();
        $this->produk = new Produk();
    }

    /**
     * HALAMAN KERANJANG
     */
    public function index(Request $request)
    {
        $userId = (int) Auth::id();

        if ($request->isMethod('post')) {
            return $this->prosesForm($request, $userId);
        }

        $items = $this->keranjang->daftar($userId);

        $subtotal = Keranjang::subtotal($items);

        return view('user.keranjang', [
            'items' => $items,
            'subtotal' => $subtotal,
            'ongkir' => $items ? self::ONGKIR_ESTIMASI : 0,
            'flash' => session()->pull('keranjang'),
        ]);
    }

    /**
     * PROSES FORM
     */
    private function prosesForm(Request $request, int $userId)
    {
        $kembali = $request->input('kembali');

        if (
            !is_string($kembali) ||
            !in_array($kembali, self::HALAMAN_KEMBALI, true)
        ) {
            $kembali = '/keranjang';
        }

        switch ($request->input('aksi')) {
            case 'tambah':
                $this->tambah($request, $userId);
                break;

            case 'ubah':
                $this->ubah($request, $userId);
                break;

            case 'hapus':
                $this->hapus($request, $userId);
                break;

            default:
                $this->setFlash(
                    false,
                    'Aksi tidak dikenal.'
                );
        }

        return redirect($kembali);
    }

    /**
     * TAMBAH PRODUK
     */
    private function tambah(Request $request, int $userId): void
    {
        $id = $request->input('produk_id');

        if (
            !is_string($id) ||
            $id === '' ||
            !ctype_digit($id) ||
            strlen($id) > 9
        ) {
            $this->setFlash(false, 'Produk tidak valid.');
            return;
        }

        $produk = $this->produk->cariById((int) $id);

        if (!$produk) {
            $this->setFlash(false, 'Produk tidak ditemukan.');
            return;
        }

        $this->keranjang->tambah(
            $userId,
            (int) $produk['id'],
            1
        );

        $this->setFlash(
            true,
            $produk['nama'] . ' ditambahkan ke keranjang.'
        );
    }

    /**
     * UBAH JUMLAH
     * Jumlah harus berada antara 1 sampai 99.
     */
    private function ubah(Request $request, int $userId): void
    {
        $itemId = $request->input('item_id');
        $jumlah = $request->input('jumlah');

        if (
            !is_string($itemId) ||
            $itemId === '' ||
            !ctype_digit($itemId) ||
            strlen($itemId) > 9
        ) {
            $this->setFlash(
                false,
                'Item keranjang tidak valid.'
            );
        } elseif (
            !is_string($jumlah) ||
            $jumlah === ''
        ) {
            $this->setFlash(
                false,
                'Jumlah wajib diisi.'
            );
        } elseif (!ctype_digit($jumlah)) {
            $this->setFlash(
                false,
                'Jumlah harus berupa angka bulat (tanpa huruf, tanda minus, atau koma).'
            );
        } elseif (
            strlen($jumlah) > 3 ||
            (int) $jumlah < 1 ||
            (int) $jumlah > Keranjang::MAKS_JUMLAH
        ) {
            $this->setFlash(
                false,
                'Jumlah harus antara 1 dan ' .
                Keranjang::MAKS_JUMLAH .
                '. Untuk membuang produk, pakai tombol hapus.'
            );
        } elseif (
            !$this->keranjang->ubahJumlah(
                $userId,
                (int) $itemId,
                (int) $jumlah
            )
        ) {
            $this->setFlash(
                false,
                'Item keranjang tidak ditemukan.'
            );
        } else {
            $this->setFlash(
                true,
                'Jumlah produk diperbarui.'
            );
        }
    }

    /**
     * HAPUS ITEM
     */
    private function hapus(Request $request, int $userId): void
    {
        $itemId = $request->input('item_id');

        if (
            !is_string($itemId) ||
            $itemId === '' ||
            !ctype_digit($itemId) ||
            strlen($itemId) > 9
        ) {
            $this->setFlash(
                false,
                'Item keranjang tidak valid.'
            );
        } elseif (
            !$this->keranjang->hapus(
                $userId,
                (int) $itemId
            )
        ) {
            $this->setFlash(
                false,
                'Item keranjang tidak ditemukan.'
            );
        } else {
            $this->setFlash(
                true,
                'Produk dihapus dari keranjang.'
            );
        }
    }

    /**
     * PESAN FLASH SESSION
     */
    private function setFlash(bool $ok, string $pesan): void
    {
        session()->flash('keranjang', [
            'ok' => $ok,
            'pesan' => $pesan,
        ]);
    }
}
