<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PesananController extends Controller
{
    private const MAKS_BARIS_ITEM = 20;
    private const MAKS_JUMLAH = 99;

    private Pesanan $pesanan;
    private Produk $produk;
    private User $user;

    public function __construct()
    {
        $this->pesanan = new Pesanan();
        $this->produk = new Produk();
        $this->user = new User();
    }

    public function index(Request $request)
    {
        $galat = '';
        $isian = null;

        try {
            if ($request->isMethod('post')) {
                [$galat, $isian] = $this->prosesForm($request);

                if ($galat === '') {
                    return redirect()->route('admin.pesanan');
                }
            }

            $q = mb_substr(trim((string) $request->query('q', '')), 0, 50);
            $status = mb_substr(trim((string) $request->query('status', '')), 0, 30);

            if (!in_array($status, Pesanan::STATUS, true)) {
                $status = '';
            }

            $daftar = $this->pesanan->daftarAdmin($q, $status);
            $items = $this->pesanan->itemsBanyak(array_column($daftar, 'id'));
            $katalog = $this->produk->semua();
            $pelanggan = $this->user->daftarPelanggan();

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            abort(500, 'Terjadi kesalahan pada server.');
        }

        return view('admin.pesanan', [
            'daftar' => $daftar,
            'items' => $items,
            'katalog' => $katalog,
            'pelanggan' => $pelanggan,
            'opsi' => $this->opsiPengiriman(),
            'statuses' => Pesanan::STATUS,
            'q' => $q,
            'status' => $status,
            'flash' => session('pesanan_admin'),
            'galat' => $galat,
            'isian' => $isian,
        ]);
    }

    private function opsiPengiriman(): array
    {
        return [
            10000 => 'JNE Reguler (2-3 hari)',
            15000 => 'JNE YES (1 hari)',
        ];
    }

    private function prosesForm(Request $request): array
    {
        $aksi = $request->input('aksi');

        switch ($aksi) {
            case 'tambah':
            case 'ubah':
                return $this->simpan($request, $aksi);

            case 'hapus':
                $this->hapus($request);
                break;

            default:
                session()->flash('pesanan_admin', [
                    'success' => false,
                    'message' => 'Aksi tidak dikenal.',
                ]);
        }

        return ['', null];
    }

    private function simpan(Request $request, string $aksi): array
    {
        $in = [
            'aksi' => $aksi,
            'id' => (string) $request->input('id', ''),
            'user_id' => (string) $request->input('user_id', ''),
            'shipping' => (string) $request->input('shipping', ''),
            'status' => (string) $request->input('status', ''),
            'alamat_nama' => trim((string) $request->input('alamat_nama', '')),
            'alamat_telp' => trim((string) $request->input('alamat_telp', '')),
            'alamat_teks' => trim((string) $request->input('alamat_teks', '')),
            'items' => $this->bacaBaris($request),
        ];

        $hasil = $this->validasi($in);

        if (is_string($hasil)) {
            return [$hasil, $in];
        }

        [
            'lama' => $lama,
            'baris' => $baris,
            'opsi' => $kurir,
            'ongkir' => $ongkir,
        ] = $hasil;

        if ($aksi === 'tambah') {
            $kode = $this->pesanan->buatAdmin(
                (int) $in['user_id'],
                $baris,
                $kurir,
                $ongkir,
                $in['status'],
                $this->alamat($in)
            );

            session()->flash('pesanan_admin', [
                'success' => true,
                'message' => 'Pesanan #' . $kode . ' berhasil ditambahkan.',
            ]);
        } else {
            $this->pesanan->ubahAdmin(
                (int) $lama['id'],
                (int) $in['user_id'],
                $baris,
                $kurir,
                $ongkir,
                $in['status'],
                $this->alamat($in)
            );

            session()->flash('pesanan_admin', [
                'success' => true,
                'message' => 'Pesanan #' . $lama['kode'] . ' berhasil diperbarui.',
            ]);
        }

        return ['', null];
    }

    private function alamat(array $in): array
    {
        return [
            'nama' => $in['alamat_nama'],
            'telp' => $in['alamat_telp'],
            'alamat' => $in['alamat_teks'],
        ];
    }

    private function bacaBaris(Request $request): array
    {
        $ids = $request->input('produk_id', []);
        $jumlah = $request->input('jumlah', []);

        if (!is_array($ids) || !is_array($jumlah)) {
            return [['produk_id' => '#', 'jumlah' => '#']];
        }

        $baris = [];

        foreach (array_values($ids) as $i => $id) {
            $j = $jumlah[$i] ?? '';

            $id = is_string($id) ? trim($id) : '#';
            $j = is_string($j) ? trim($j) : '#';

            if ($id === '' && $j === '') {
                continue;
            }

            $baris[] = [
                'produk_id' => $id,
                'jumlah' => $j,
            ];
        }

        return $baris;
    }

    private function validasi(array $in)
    {
        $lama = null;

        if ($in['aksi'] === 'ubah') {
            if (
                $in['id'] === '' ||
                !ctype_digit($in['id']) ||
                strlen($in['id']) > 9
            ) {
                return 'Pesanan yang diubah tidak valid.';
            }

            $lama = $this->pesanan->cariById((int) $in['id']);

            if (!$lama) {
                return 'Pesanan tidak ditemukan (mungkin sudah dihapus).';
            }
        }

        if ($in['user_id'] === '') {
            return 'Pelanggan wajib dipilih.';
        }

        if (
            !ctype_digit($in['user_id']) ||
            strlen($in['user_id']) > 9 ||
            !$this->user->cariById((int) $in['user_id'])
        ) {
            return 'Pelanggan tidak valid.';
        }

        $opsi = $this->opsiPengiriman();

        if ($in['shipping'] === '') {
            return 'Pengiriman wajib dipilih.';
        }

        if (
            !ctype_digit($in['shipping']) ||
            !isset($opsi[(int) $in['shipping']])
        ) {
            return 'Pilihan pengiriman tidak valid.';
        }

        if ($in['status'] === '') {
            return 'Status pesanan wajib dipilih.';
        }

        if (!in_array($in['status'], Pesanan::STATUS, true)) {
            return 'Status pesanan tidak valid.';
        }

        if ($in['alamat_nama'] === '') {
            return 'Nama penerima wajib diisi.';
        }

        if (
            mb_strlen($in['alamat_nama']) < 3 ||
            mb_strlen($in['alamat_nama']) > 100
        ) {
            return 'Nama penerima harus 3–100 karakter.';
        }

        if ($in['alamat_telp'] === '') {
            return 'No. HP penerima wajib diisi.';
        }

        if (!preg_match('/^[0-9+\s\-]{8,20}$/', $in['alamat_telp'])) {
            return 'No. HP tidak valid (8–20 karakter: angka, spasi, + atau -).';
        }

        if ($in['alamat_teks'] === '') {
            return 'Alamat pengiriman wajib diisi.';
        }

        if (
            mb_strlen($in['alamat_teks']) < 10 ||
            mb_strlen($in['alamat_teks']) > 300
        ) {
            return 'Alamat pengiriman harus 10–300 karakter.';
        }

        foreach (['alamat_nama', 'alamat_telp', 'alamat_teks'] as $kolom) {
            if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $in[$kolom])) {
                return 'Isian mengandung karakter yang tidak diizinkan.';
            }
        }

        if (!$in['items']) {
            return 'Tambahkan minimal satu produk.';
        }

        if (count($in['items']) > self::MAKS_BARIS_ITEM) {
            return 'Maksimal ' . self::MAKS_BARIS_ITEM . ' jenis produk per pesanan.';
        }

        $ids = [];

        foreach ($in['items'] as $b) {
            if ($b['produk_id'] === '') {
                return 'Pilih produk untuk setiap baris.';
            }

            if (!ctype_digit($b['produk_id']) || strlen($b['produk_id']) > 9) {
                return 'Produk tidak valid.';
            }

            if ($b['jumlah'] === '') {
                return 'Jumlah produk wajib diisi.';
            }

            if (!ctype_digit($b['jumlah'])) {
                return 'Jumlah harus berupa angka bulat (tanpa huruf, tanda minus, atau koma).';
            }

            if (
                strlen($b['jumlah']) > 3 ||
                (int) $b['jumlah'] < 1 ||
                (int) $b['jumlah'] > self::MAKS_JUMLAH
            ) {
                return 'Jumlah per produk harus antara 1 dan ' . self::MAKS_JUMLAH . '.';
            }

            $ids[] = (int) $b['produk_id'];
        }

        if (count($ids) !== count(array_unique($ids))) {
            return 'Produk yang sama dipilih lebih dari satu kali. Gabungkan dalam satu baris.';
        }

        $katalog = [];

        foreach ($this->produk->daftarByIds($ids) as $p) {
            $katalog[(int) $p['id']] = $p;
        }

        $hargaLama = $lama
            ? array_column(
                $this->pesanan->items((int) $lama['id']),
                'harga',
                'nama'
            )
            : [];

        $baris = [];

        foreach ($in['items'] as $b) {
            $p = $katalog[(int) $b['produk_id']] ?? null;

            if (!$p) {
                return 'Produk tidak ditemukan.';
            }

            $baris[] = [
                'nama' => $p['nama'],
                'ket' => $p['ket'],
                'harga' => (int) ($hargaLama[$p['nama']] ?? $p['harga']),
                'jumlah' => (int) $b['jumlah'],
            ];
        }

        return [
            'lama' => $lama,
            'baris' => $baris,
            'opsi' => $opsi[(int) $in['shipping']],
            'ongkir' => (int) $in['shipping'],
        ];
    }

    private function hapus(Request $request): void
    {
        $id = (string) $request->input('id', '');

        if ($id === '' || !ctype_digit($id) || strlen($id) > 9) {
            session()->flash('pesanan_admin', [
                'success' => false,
                'message' => 'Pesanan tidak valid.',
            ]);
            return;
        }

        $ada = $this->pesanan->cariById((int) $id);

        if (!$ada || !$this->pesanan->hapusAdmin((int) $id)) {
            session()->flash('pesanan_admin', [
                'success' => false,
                'message' => 'Pesanan tidak ditemukan (mungkin sudah dihapus).',
            ]);
            return;
        }

        session()->flash('pesanan_admin', [
            'success' => true,
            'message' => 'Pesanan #' . $ada['kode'] . ' berhasil dihapus.',
        ]);
    }
}