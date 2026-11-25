<?php

namespace App\Http\Controllers;

use App\Models\Favorit;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class FavoritController extends Controller
{
    private const HALAMAN_KEMBALI = [
        '/dashboard',
        '/favorit',
    ];

    private Favorit $favorit;
    private Produk $produk;

    public function __construct()
    {
        $this->favorit = new Favorit();
        $this->produk = new Produk();
    }

    public function index(Request $request)
    {
        $userId = (int) Auth::id();

        // Jika menerima POST, proses aksi favorit.
        if ($request->isMethod('post')) {
            return $this->prosesForm($request, $userId);
        }

        try {
            $daftar = $this->favorit->daftar($userId);
        } catch (Throwable $e) {
            Log::error('Gagal mengambil daftar favorit: ' . $e->getMessage());

            abort(500, 'Terjadi kesalahan saat mengambil daftar favorit.');
        }

        return view('user.favorit', [
            'produk' => $daftar,
            'flashKeranjang' => session()->pull('keranjang'),
            'flashFavorit' => session()->pull('favorit'),
        ]);
    }

    private function prosesForm(Request $request, int $userId)
    {
        // Memastikan halaman tujuan redirect aman.
        $kembali = $request->input('kembali');

        if (!is_string($kembali) || !in_array($kembali, self::HALAMAN_KEMBALI, true)) {
            $kembali = '/favorit';
        }

        // Laravel menangani CSRF melalui middleware web.
        // Token tetap disertakan pada setiap form Blade menggunakan @csrf.

        try {
            switch ($request->input('aksi')) {
                case 'tambah':
                    $this->tambah($request, $userId);
                    break;

                case 'catatan':
                    $this->ubahCatatan($request, $userId);
                    break;

                case 'hapus':
                    $this->hapus($request, $userId);
                    break;

                default:
                    $this->setFlash(false, 'Aksi tidak dikenal.');
                    break;
            }
        } catch (Throwable $e) {
            Log::error('Gagal memproses favorit: ' . $e->getMessage());

            $this->setFlash(false, 'Terjadi kesalahan saat memproses produk favorit.');
        }

        return redirect($kembali);
    }

    private function produkValid(Request $request): ?array
    {
        $id = $request->input('produk_id');

        if (!is_string($id) && !is_int($id)) {
            $this->setFlash(false, 'Produk wajib dipilih.');
            return null;
        }

        $id = (string) $id;

        if ($id === '' || !ctype_digit($id) || strlen($id) > 9) {
            $this->setFlash(false, 'Produk tidak valid.');
            return null;
        }

        $produk = $this->produk->cariById((int) $id);

        if (!$produk) {
            $this->setFlash(false, 'Produk tidak ditemukan.');
            return null;
        }

        return $produk;
    }

    private function tambah(Request $request, int $userId): void
    {
        $produk = $this->produkValid($request);

        if (!$produk) {
            return;
        }

        if ($this->favorit->tambah($userId, (int) $produk['id'])) {
            $this->setFlash(true, $produk['nama'] . ' ditambahkan ke favorit.');
        } else {
            $this->setFlash(false, $produk['nama'] . ' sudah ada di favorit.');
        }
    }

    private function ubahCatatan(Request $request, int $userId): void
    {
        $produk = $this->produkValid($request);

        if (!$produk) {
            return;
        }

        $catatan = $request->input('catatan', '');

        if (!is_string($catatan)) {
            $this->setFlash(false, 'Catatan tidak valid.');
            return;
        }

        if (mb_strlen($catatan) > Favorit::MAKS_CATATAN) {
            $this->setFlash(
                false,
                'Catatan maksimal ' . Favorit::MAKS_CATATAN . ' karakter.'
            );
            return;
        }

        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $catatan)) {
            $this->setFlash(false, 'Catatan mengandung karakter yang tidak diizinkan.');
            return;
        }

        if (!$this->favorit->ada($userId, (int) $produk['id'])) {
            $this->setFlash(false, 'Produk ini tidak ada di daftar favoritmu.');
            return;
        }

        $this->favorit->ubahCatatan(
            $userId,
            (int) $produk['id'],
            $catatan !== '' ? $catatan : null
        );

        $this->setFlash(true, 'Catatan favorit diperbarui.');
    }

    private function hapus(Request $request, int $userId): void
    {
        $produk = $this->produkValid($request);

        if (!$produk) {
            return;
        }

        if ($this->favorit->hapus($userId, (int) $produk['id'])) {
            $this->setFlash(true, $produk['nama'] . ' dihapus dari favorit.');
        } else {
            $this->setFlash(false, 'Produk ini tidak ada di daftar favoritmu.');
        }
    }

    private function setFlash(bool $ok, string $pesan): void
    {
        session()->flash('favorit', [
            'ok' => $ok,
            'pesan' => $pesan,
        ]);
    }
}
