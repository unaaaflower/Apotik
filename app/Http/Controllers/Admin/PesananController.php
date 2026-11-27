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
    // Batas maksimal jenis produk dan jumlah setiap produk dalam satu pesanan.
    private const MAKS_BARIS_ITEM = 20;
    private const MAKS_JUMLAH = 99;

    // Menyiapkan model yang digunakan untuk mengelola data pesanan, produk, dan pengguna.
    private Pesanan $pesanan;
    private Produk $produk;
    private User $user;

    // Constructor dijalankan ketika controller dibuat.
    public function __construct()
    {
        $this->pesanan = new Pesanan();
        $this->produk = new Produk();
        $this->user = new User();
    }

    // Menampilkan halaman daftar pesanan sekaligus memproses permintaan dari admin.
    public function index(Request $request)
    {
        $galat = '';
        $isian = null;

        try {
            // Memeriksa apakah ada data yang dikirim melalui form.
            if ($request->isMethod('post')) {
                [$galat, $isian] = $this->prosesForm($request);

                // Jika tidak ada kesalahan, kembali ke halaman pesanan.
                if ($galat === '') {
                    return redirect()->route('admin.pesanan');
                }
            }

            // Mengambil kata kunci pencarian dan filter status dari URL.
            $q = mb_substr(trim((string) $request->query('q', '')), 0, 50);
            $status = mb_substr(trim((string) $request->query('status', '')), 0, 30);

            // Memastikan status yang dipilih sesuai dengan pilihan yang tersedia.
            if (!in_array($status, Pesanan::STATUS, true)) {
                $status = '';
            }

            // Mengambil data pesanan, rincian produk, katalog produk, dan daftar pelanggan.
            $daftar = $this->pesanan->daftarAdmin($q, $status);
            $items = $this->pesanan->itemsBanyak(array_column($daftar, 'id'));
            $katalog = $this->produk->semua();
            $pelanggan = $this->user->daftarPelanggan();

        } catch (\Throwable $e) {
            // Mencatat kesalahan ke log jika terjadi masalah saat mengambil data.
            Log::error($e->getMessage());
            abort(500, 'Terjadi kesalahan pada server.');
        }

        // Mengirim data yang sudah diambil ke halaman admin.pesanan.
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

    // Menyediakan pilihan jasa pengiriman beserta biaya ongkos kirimnya.
    private function opsiPengiriman(): array
    {
        return [
            10000 => 'JNE Reguler (2-3 hari)',
            15000 => 'JNE YES (1 hari)',
        ];
    }

    // Menentukan proses yang dijalankan berdasarkan aksi dari form admin.
    private function prosesForm(Request $request): array
    {
        $aksi = $request->input('aksi');

        // Memeriksa apakah admin memilih tambah, ubah, atau hapus pesanan.
        switch ($aksi) {
            case 'tambah':
            case 'ubah':
                // Aksi tambah dan ubah diproses melalui method simpan().
                return $this->simpan($request, $aksi);

            case 'hapus':
                // Memanggil method hapus() untuk menghapus pesanan.
                $this->hapus($request);
                break;

            default:
                // Menampilkan pesan jika aksi yang dikirim tidak dikenali.
                session()->flash('pesanan_admin', [
                    'success' => false,
                    'message' => 'Aksi tidak dikenal.',
                ]);
        }

        return ['', null];
    }

    // Mengolah data dari form untuk menambahkan atau mengubah pesanan.
    private function simpan(Request $request, string $aksi): array
    {
        // Mengambil seluruh input yang dikirim dari form admin.
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

        // Memeriksa apakah seluruh data yang dimasukkan sudah sesuai ketentuan.
        $hasil = $this->validasi($in);

        // Jika validasi gagal, mengembalikan pesan kesalahan dan isian form.
        if (is_string($hasil)) {
            return [$hasil, $in];
        }

        // Mengambil data hasil validasi untuk digunakan dalam proses penyimpanan.
        [
            'lama' => $lama,
            'baris' => $baris,
            'opsi' => $kurir,
            'ongkir' => $ongkir,
        ] = $hasil;

        // Jika aksi tambah, sistem akan membuat pesanan baru.
        if ($aksi === 'tambah') {
            $kode = $this->pesanan->buatAdmin(
                (int) $in['user_id'],
                $baris,
                $kurir,
                $ongkir,
                $in['status'],
                $this->alamat($in)
            );

            // Menampilkan pemberitahuan bahwa pesanan berhasil ditambahkan.
            session()->flash('pesanan_admin', [
                'success' => true,
                'message' => 'Pesanan #' . $kode . ' berhasil ditambahkan.',
            ]);
        } else {
            // Jika bukan tambah, sistem akan memperbarui pesanan yang sudah ada.
            $this->pesanan->ubahAdmin(
                (int) $lama['id'],
                (int) $in['user_id'],
                $baris,
                $kurir,
                $ongkir,
                $in['status'],
                $this->alamat($in)
            );

            // Menampilkan pemberitahuan bahwa pesanan berhasil diperbarui.
            session()->flash('pesanan_admin', [
                'success' => true,
                'message' => 'Pesanan #' . $lama['kode'] . ' berhasil diperbarui.',
            ]);
        }

        return ['', null];
    }

    // Mengelompokkan informasi penerima dan alamat pengiriman menjadi satu array.
    private function alamat(array $in): array
    {
        return [
            'nama' => $in['alamat_nama'],
            'telp' => $in['alamat_telp'],
            'alamat' => $in['alamat_teks'],
        ];
    }

    // Membaca data produk dan jumlahnya dari baris-baris pada form pesanan.
    private function bacaBaris(Request $request): array
    {
        // Mengambil ID produk dan jumlah yang dikirim melalui form.
        $ids = $request->input('produk_id', []);
        $jumlah = $request->input('jumlah', []);

        // Memastikan data produk dan jumlah berbentuk array.
        if (!is_array($ids) || !is_array($jumlah)) {
            return [['produk_id' => '#', 'jumlah' => '#']];
        }

        $baris = [];

        // Mengulang setiap produk untuk mencocokkan ID dengan jumlahnya.
        foreach (array_values($ids) as $i => $id) {
            $j = $jumlah[$i] ?? '';

            // Membersihkan spasi dan memastikan data berbentuk teks.
            $id = is_string($id) ? trim($id) : '#';
            $j = is_string($j) ? trim($j) : '#';

            // Melewati baris jika ID produk dan jumlah sama-sama kosong.
            if ($id === '' && $j === '') {
                continue;
            }

            // Menyimpan ID produk dan jumlah ke dalam array.
            $baris[] = [
                'produk_id' => $id,
                'jumlah' => $j,
            ];
        }

        return $baris;
    }

    // Memeriksa kelengkapan dan kebenaran seluruh data sebelum disimpan.
    private function validasi(array $in)
    {
        $lama = null;

        // Jika sedang mengubah pesanan, sistem akan memeriksa ID pesanan lama.
        if ($in['aksi'] === 'ubah') {
            if (
                $in['id'] === '' ||
                !ctype_digit($in['id']) ||
                strlen($in['id']) > 9
            ) {
                return 'Pesanan yang diubah tidak valid.';
            }

            // Mencari pesanan lama berdasarkan ID.
            $lama = $this->pesanan->cariById((int) $in['id']);

            if (!$lama) {
                return 'Pesanan tidak ditemukan (mungkin sudah dihapus).';
            }
        }

        // Memastikan pelanggan sudah dipilih.
        if ($in['user_id'] === '') {
            return 'Pelanggan wajib dipilih.';
        }

        // Memastikan ID pelanggan berupa angka dan terdaftar di database.
        if (
            !ctype_digit($in['user_id']) ||
            strlen($in['user_id']) > 9 ||
            !$this->user->cariById((int) $in['user_id'])
        ) {
            return 'Pelanggan tidak valid.';
        }

        // Mengambil daftar pilihan jasa pengiriman.
        $opsi = $this->opsiPengiriman();

        // Memastikan admin sudah memilih jasa pengiriman.
        if ($in['shipping'] === '') {
            return 'Pengiriman wajib dipilih.';
        }

        // Memastikan pilihan pengiriman tersedia dalam daftar.
        if (
            !ctype_digit($in['shipping']) ||
            !isset($opsi[(int) $in['shipping']])
        ) {
            return 'Pilihan pengiriman tidak valid.';
        }

        // Memastikan status pesanan sudah dipilih.
        if ($in['status'] === '') {
            return 'Status pesanan wajib dipilih.';
        }

        // Memeriksa apakah status sesuai dengan pilihan yang tersedia.
        if (!in_array($in['status'], Pesanan::STATUS, true)) {
            return 'Status pesanan tidak valid.';
        }

        // Memastikan nama penerima sudah diisi.
        if ($in['alamat_nama'] === '') {
            return 'Nama penerima wajib diisi.';
        }

        // Membatasi panjang nama penerima antara 3 sampai 100 karakter.
        if (
            mb_strlen($in['alamat_nama']) < 3 ||
            mb_strlen($in['alamat_nama']) > 100
        ) {
            return 'Nama penerima harus 3–100 karakter.';
        }

        // Memastikan nomor HP penerima sudah diisi.
        if ($in['alamat_telp'] === '') {
            return 'No. HP penerima wajib diisi.';
        }

        // Memeriksa format nomor HP agar hanya berisi karakter yang diperbolehkan.
        if (!preg_match('/^[0-9+\s\-]{8,20}$/', $in['alamat_telp'])) {
            return 'No. HP tidak valid (8–20 karakter: angka, spasi, + atau -).';
        }

        // Memastikan alamat pengiriman sudah diisi.
        if ($in['alamat_teks'] === '') {
            return 'Alamat pengiriman wajib diisi.';
        }

        // Membatasi panjang alamat antara 10 sampai 300 karakter.
        if (
            mb_strlen($in['alamat_teks']) < 10 ||
            mb_strlen($in['alamat_teks']) > 300
        ) {
            return 'Alamat pengiriman harus 10–300 karakter.';
        }

        // Memeriksa agar data alamat tidak mengandung karakter yang tidak diizinkan.
        foreach (['alamat_nama', 'alamat_telp', 'alamat_teks'] as $kolom) {
            if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $in[$kolom])) {
                return 'Isian mengandung karakter yang tidak diizinkan.';
            }
        }

        // Memastikan pesanan memiliki minimal satu produk.
        if (!$in['items']) {
            return 'Tambahkan minimal satu produk.';
        }

        // Membatasi jumlah jenis produk dalam satu pesanan.
        if (count($in['items']) > self::MAKS_BARIS_ITEM) {
            return 'Maksimal ' . self::MAKS_BARIS_ITEM . ' jenis produk per pesanan.';
        }

        $ids = [];

        // Memeriksa setiap produk dan jumlah yang dimasukkan admin.
        foreach ($in['items'] as $b) {
            if ($b['produk_id'] === '') {
                return 'Pilih produk untuk setiap baris.';
            }

            // Memastikan ID produk hanya berisi angka.
            if (!ctype_digit($b['produk_id']) || strlen($b['produk_id']) > 9) {
                return 'Produk tidak valid.';
            }

            if ($b['jumlah'] === '') {
                return 'Jumlah produk wajib diisi.';
            }

            // Memastikan jumlah produk berupa angka bulat.
            if (!ctype_digit($b['jumlah'])) {
                return 'Jumlah harus berupa angka bulat (tanpa huruf, tanda minus, atau koma).';
            }

            // Memastikan jumlah produk berada di antara 1 sampai batas maksimal.
            if (
                strlen($b['jumlah']) > 3 ||
                (int) $b['jumlah'] < 1 ||
                (int) $b['jumlah'] > self::MAKS_JUMLAH
            ) {
                return 'Jumlah per produk harus antara 1 dan ' . self::MAKS_JUMLAH . '.';
            }

            // Mengumpulkan ID produk untuk diperiksa agar tidak ada data ganda.
            $ids[] = (int) $b['produk_id'];
        }

        // Menolak produk yang sama jika dimasukkan lebih dari satu kali.
        if (count($ids) !== count(array_unique($ids))) {
            return 'Produk yang sama dipilih lebih dari satu kali. Gabungkan dalam satu baris.';
        }

        $katalog = [];

        // Mengambil informasi produk dari database berdasarkan ID yang dipilih.
        foreach ($this->produk->daftarByIds($ids) as $p) {
            $katalog[(int) $p['id']] = $p;
        }

        // Mengambil harga dari pesanan sebelumnya jika admin sedang mengubah pesanan.
        $hargaLama = $lama
            ? array_column(
                $this->pesanan->items((int) $lama['id']),
                'harga',
                'nama'
            )
            : [];

        $baris = [];

        // Menyusun kembali rincian produk yang sudah lolos pemeriksaan.
        foreach ($in['items'] as $b) {
            $p = $katalog[(int) $b['produk_id']] ?? null;

            if (!$p) {
                return 'Produk tidak ditemukan.';
            }

            // Menyimpan nama, keterangan, harga, dan jumlah setiap produk.
            $baris[] = [
                'nama' => $p['nama'],
                'ket' => $p['ket'],
                'harga' => (int) ($hargaLama[$p['nama']] ?? $p['harga']),
                'jumlah' => (int) $b['jumlah'],
            ];
        }

        // Mengembalikan data yang sudah valid untuk diproses oleh method simpan().
        return [
            'lama' => $lama,
            'baris' => $baris,
            'opsi' => $opsi[(int) $in['shipping']],
            'ongkir' => (int) $in['shipping'],
        ];
    }

    // Menghapus pesanan berdasarkan ID yang dikirim dari form admin.
    private function hapus(Request $request): void
    {
        // Mengambil ID pesanan yang akan dihapus.
        $id = (string) $request->input('id', '');

        // Memastikan ID pesanan berupa angka dan memiliki panjang yang sesuai.
        if ($id === '' || !ctype_digit($id) || strlen($id) > 9) {
            session()->flash('pesanan_admin', [
                'success' => false,
                'message' => 'Pesanan tidak valid.',
            ]);
            return;
        }

        // Mencari pesanan terlebih dahulu sebelum melakukan penghapusan.
        $ada = $this->pesanan->cariById((int) $id);

        // Memeriksa apakah pesanan ditemukan dan berhasil dihapus.
        if (!$ada || !$this->pesanan->hapusAdmin((int) $id)) {
            session()->flash('pesanan_admin', [
                'success' => false,
                'message' => 'Pesanan tidak ditemukan (mungkin sudah dihapus).',
            ]);
            return;
        }

        // Menampilkan pemberitahuan jika pesanan berhasil dihapus.
        session()->flash('pesanan_admin', [
            'success' => true,
            'message' => 'Pesanan #' . $ada['kode'] . ' berhasil dihapus.',
        ]);
    }
}