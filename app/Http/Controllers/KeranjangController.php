<?php

namespace App\Http\Controllers;

// Memanggil model Keranjang untuk mengelola data keranjang pengguna.
use App\Models\Keranjang;

// Memanggil model Produk untuk mencari informasi produk berdasarkan ID.
use App\Models\Produk;

// Memanggil Request untuk membaca data yang dikirim pengguna melalui form.
use Illuminate\Http\Request;

// Memanggil Auth untuk mengambil identitas pengguna yang sedang login.
use Illuminate\Support\Facades\Auth;

// Mendefinisikan controller yang menangani seluruh proses fitur keranjang.
class KeranjangController extends Controller
{
    // Menentukan estimasi ongkos kirim sebesar Rp10.000.
    // Kata const berarti nilainya tetap dan tidak diubah selama aplikasi berjalan.
    public const ONGKIR_ESTIMASI = 10000; //digunakan untuk menentukaan ongkir sebesar 10k, untuk const sendirii digunakan supaya nilai 10k tidak berubah selama apk berjalan

    /**
     * Halaman yang boleh menjadi tujuan kembali.
     */
    // Daftar alamat halaman yang diizinkan sebagai tujuan redirect setelah proses keranjang.
    // Pembatasan ini mencegah pengguna memasukkan alamat tujuan sembarangan.
    private const HALAMAN_KEMBALI = [
        '/dashboard',
        '/favorit',
        '/keranjang',
    ];

    // Menyiapkan properti untuk menyimpan objek model Keranjang.
    private Keranjang $keranjang; // contohnya mengambil,menambah, mengubah dan menghapus produk

    // Menyiapkan properti untuk menyimpan objek model Produk.
    private Produk $produk; // mencari produk dalam by id

    // method  dijalankan otomatis ketika objek controller dibuat. (dideklarasikan)
    public function __construct()
    {
        // Membuat objek model Keranjang dan produk agar bisa digunakan oleh method dalam controller. (diinisialisasi)
        $this->keranjang = new Keranjang();
        $this->produk = new Produk();
    }

    /**
     * HALAMAN KERANJANG
     */
    public function index(Request $request) // mengirikan objek request ke method indek, agar method bisa membaca data dari user
    {
        $userId = (int) Auth::id(); // digunakan untuk mmengambil id pengguna yang sedang llogin lalu di simpan di $user dengan tipe int

        if ($request->isMethod('post')) {  // Memeriksa apakah halaman diakses menggunakan metode POST.
       
            return $this->prosesForm($request, $userId);  // Meneruskan proses form ke method prosesForm().  berisi data requst dan id
        }

        
        $items = $this->keranjang->daftar($userId); // mengambil daftar produk keranjang berdasarkan id lalu. Proses pengambilan data dilakukan melalui method daftar() pada model Keranjang.

        $subtotal = Keranjang::subtotal($items); // menghitung subtotal pada item produk pada keranjang lalu. Perhitungan dilakukan menggunakan method static subtotal() pada model Keranjang.

        // Mengirim data ke halaman tampilan user.keranjang.
        return view('user.keranjang', [
            'items' => $items, // Mengirim daftar produk keranjang.
            'subtotal' => $subtotal, // Mengirim hasil perhitungan subtotal.

            // Jika keranjang berisi produk, ongkir diisi Rp10.000.
            // Jika keranjang kosong, ongkir diisi 0.
            'ongkir' => $items ? self::ONGKIR_ESTIMASI : 0, 

            // Mengambil pesan flash session dengan key 'keranjang'.
            // pull() mengambil sekaligus menghapus pesan dari session.
            'flash' => session()->pull('keranjang'), // contohnya menampilkan pesan bahwa "produk berhasil ditambahkan"
        ]);
    }

    /**
     * PROSES FORM
     */
    private function prosesForm(Request $request, int $userId) //sebagai pengatur: apakah pengguna ingin menambah produk, mengubah jumlah produk, atau menghapus produk.
    {
        $kembali = $request->input('kembali'); // digunaka untuk mengambil nilai input kembali dari form dan menentukan halaman selanjutnya untuk kembali

        // Memeriksa apakah nilai kembali berupa string
        // dan termasuk dalam daftar halaman yang diizinkan.
        if (
            !is_string($kembali) ||
            !in_array($kembali, self::HALAMAN_KEMBALI, true)
        ) {
            // Jika tidak valid, pengguna diarahkan kembali ke halaman keranjang.
            $kembali = '/keranjang';
        }

        // Memeriksa nilai input 'aksi' untuk menentukan proses yang harus dijalankan.
        switch ($request->input('aksi')) {

            case 'tambah':
                // Jika aksi bernilai tambah, jalankan method tambah().
                $this->tambah($request, $userId);
                break; // Menghentikan pemeriksaan switch.

            case 'ubah':
                // Jika aksi bernilai ubah, jalankan method ubah().
                $this->ubah($request, $userId);
                break;

            case 'hapus':
                // Jika aksi bernilai hapus, jalankan method hapus().
                $this->hapus($request, $userId);
                break;

            default:
                // Jika aksi tidak sesuai dengan pilihan yang tersedia,
                $this->setFlash(
                    false,
                    'Aksi tidak dikenal.'
                );
        }

        // Mengarahkan pengguna kembali ke halaman yang telah ditentukan.
        return redirect($kembali);
    }

    /**
     * TAMBAH PRODUK
     */
    private function tambah(Request $request, int $userId): void
    {
        // Mengambil ID produk yang dikirim melalui form.
        $id = $request->input('produk_id'); // artinya jika user menambahkan produk obat id 5 maka akan diambil

        // Memeriksa apakah ID produk berupa string, tidak kosong,
        // hanya berisi angka, dan panjangnya tidak lebih dari 9 karakter.
        if (
            !is_string($id) ||
            $id === '' ||
            !ctype_digit($id) ||
            strlen($id) > 9
        ) {
            // Jika ID tidak memenuhi pemeriksaan, tampilkan pesan kesalahan.
            $this->setFlash(false, 'Produk tidak valid.');
            return; // Menghentikan proses tambah produk.
        }

        // Mencari data produk berdasarkan ID yang sudah diperiksa.
        // (int) mengubah ID menjadi integer sebelum dikirim ke model.
        $produk = $this->produk->cariById((int) $id);

        // Memeriksa apakah produk ditemukan di database.
        if (!$produk) {

            // Jika tidak ditemukan, tampilkan pesan kesalahan.
            $this->setFlash(false, 'Produk tidak ditemukan.');
            return; // Menghentikan proses karena produk tidak tersedia.
        }

        // Parameter kedua: ID produk yang akan ditambahkan.
        // Parameter ketiga: jumlah awal produk, yaitu 1.
        $this->keranjang->tambah(  // Memanggil method tambah() pada model Keranjang.
            $userId,
            (int) $produk['id'],
            1
        ); // digunakan untuk memasukan produk ke dalam keranjang

        $this->setFlash(
            true,
            $produk['nama'] . ' ditambahkan ke keranjang.' //Menampilkan pesan berhasil dengan nama produk yang ditambahkan.
        );
    }

    /**
     * UBAH JUMLAH
     * Jumlah harus berada antara 1 sampai 99.
     */
    private function ubah(Request $request, int $userId): void
    {
        // Mengambil ID item keranjang yang jumlahnya ingin diubah.
        $itemId = $request->input('item_id');

        // Mengambil jumlah produk baru yang dikirim melalui form.
        $jumlah = $request->input('jumlah');

        // Pemeriksaan pertama: memastikan ID item berupa angka yang valid.
        if (
            !is_string($itemId) ||  // Memastikan itemId bertipe string
            $itemId === '' ||    // Memastikan itemId tidak kosong
            !ctype_digit($itemId) ||   // Memastikan itemId hanya berisi angka
            strlen($itemId) > 9   // Membatasi panjang itemId maksimal 9 karakter
        ) {
            // Jika ID item tidak valid, tampilkan pesan kesalahan.
            $this->setFlash(
                false,
                'Item keranjang tidak valid.'
            );

        // Pemeriksaan kedua: memastikan jumlah sudah diisi.
        } elseif (
            !is_string($jumlah) ||
            $jumlah === ''
        ) {
            // Jika jumlah kosong atau bukan string, tampilkan pesan.
            $this->setFlash(
                false,
                'Jumlah wajib diisi.'
            );

        // Pemeriksaan ketiga: memastikan jumlah hanya berisi angka bulat.
        } elseif (!ctype_digit($jumlah)) {

            // Menolak huruf, angka negatif, dan angka desimal.
            $this->setFlash(
                false,
                'Jumlah harus berupa angka bulat (tanpa huruf, tanda minus, atau koma).'
            );

        // Pemeriksaan keempat: membatasi panjang dan rentang jumlah produk.
        } elseif (
            strlen($jumlah) > 3 || //Memastikan jumlah maksimal 3 karakter
            (int) $jumlah < 1 ||
            (int) $jumlah > Keranjang::MAKS_JUMLAH
        ) {
            // Jika jumlah di luar batas, tampilkan pesan kesalahan.
            // MAKS_JUMLAH merupakan konstanta batas maksimal pada model Keranjang.
            $this->setFlash(
                false,
                'Jumlah harus antara 1 dan ' .
                Keranjang::MAKS_JUMLAH .
                '. Untuk membuang produk, pakai tombol hapus.'
            );

        // Jika validasi lolos pada seleksi kondisi diatas maka, coba perbarui jumlah produk.
        // Method ubahJumlah() menerima ID pengguna, ID item, dan jumlah baru.
        // Jika hasilnya false, berarti item tidak ditemukan atau tidak berhasil diubah.
        } elseif (
            !$this->keranjang->ubahJumlah(
                $userId,
                (int) $itemId,
                (int) $jumlah // jumlah produk baru
            )
        ) {
            // Menampilkan pesan jika item tidak ditemukan.
            $this->setFlash(
                false,
                'Item keranjang tidak ditemukan.'
            );

        } else {
            // Jika seluruh proses berhasil, tampilkan pesan berhasil.
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
        // Mengambil ID item keranjang yang ingin dihapus.
        $itemId = $request->input('item_id');

        // Memastikan ID item berupa string angka yang valid.
        if (
            !is_string($itemId) ||
            $itemId === '' ||
            !ctype_digit($itemId) ||
            strlen($itemId) > 9
        ) {
            // Jika ID tidak valid, tampilkan pesan kesalahan.
            $this->setFlash(
                false,
                'Item keranjang tidak valid.'
            );

        // Jika ID valid, jalankan method hapus() pada model Keranjang.
        } elseif (
            !$this->keranjang->hapus(
                $userId, // digunaakan untuk membatasi penghapusan berdasarkan id pengguna. jadi penghapusan hanya terjadi di keranjangnya sendiri
                (int) $itemId
            )
        ) {
            // Menampilkan pesan jika item tidak ditemukan.
            $this->setFlash(
                false,
                'Item keranjang tidak ditemukan.'
            );

        } else {
            // Jika penghapusan berhasil, tampilkan pesan berhasil.
            $this->setFlash(
                true,
                'Produk dihapus dari keranjang.'
            );
        }
    }

    /**
     * PESAN FLASH SESSION
     */
    private function setFlash(bool $ok, string $pesan): void //Fungsinya hanya mengatur pesan pemberitahuan sementara untuk pengguna.
    {
        // 'ok' menunjukkan status berhasil atau gagal.
        // 'pesan' berisi informasi yang akan ditampilkan kepada pengguna.
        session()->flash('keranjang', [
            'ok' => $ok,
            'pesan' => $pesan,
        ]);
    }
}