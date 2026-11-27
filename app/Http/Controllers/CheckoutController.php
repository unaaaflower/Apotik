<?php

namespace App\Http\Controllers; 
use App\Models\Alamat; // Mengimpor Model Alamat untuk mengambil dan mengubah alamat pengguna.
use App\Models\Keranjang; // Mengimpor Model Keranjang untuk mengambil isi keranjang dan menghitung subtotal.
use App\Models\Pesanan; // Mengimpor Model Pesanan untuk membuat pesanan baru.
use Illuminate\Http\Request; // Mengimpor Request untuk membaca data yang dikirim melalui form.
use Illuminate\Support\Facades\Auth; // Mengimpor Auth untuk mengambil identitas pengguna yang login.
use Illuminate\Support\Facades\Log; // Mengimpor Log untuk mencatat kesalahan pada server.
use Throwable; // Mengimpor Throwable agar berbagai jenis kesalahan dapat ditangkap oleh catch.

class CheckoutController extends Controller //membuat class bernama CheckoutController yang mewarisi kemampuan dari class Controller
{
    // Menyiapkan objek Model yang akan digunakan untuk menyimpan objek pada class co controller
    private Keranjang $keranjang; //nama properti, mengambil produk yang akan dubeli
    private Pesanan $pesanan; // membuat dan menyimpan pesanan
    private Alamat $alamat; // mengambil dan mengubah alamat

    /**
     * Pilihan pengiriman 
     */
    // Menentukan pilihan pengiriman beserta biaya dan nama layanannya.
    // Nilai biaya digunakan sebagai key, sedangkan nama layanan sebagai value.
    private const OPSI_PENGIRIMAN = [ // membuat konstanta yang hanya bisa di akses pada class ini, supaya nilainya tetap dan tidak bisa diubah, nama konstanta
        10000 => 'JNE Reguler (2-3 hari)', // array asosiatif, key, value
        15000 => 'JNE YES (1 hari)',
    ];

    public function __construct() // method yang dijalankan otomatis ketika objek controller dibuat. (dideklarasikan)
    {
        // Membuat objek Model agar method di dalamnya dapat digunakan. (diinisialisasi)
        $this->keranjang = new Keranjang(); //membuat objek model
        $this->pesanan = new Pesanan();
        $this->alamat = new Alamat();
    }

    /**
     * HALAMAN CHECKOUT
     */
    // Menampilkan halaman checkout dan menangani permintaan GET maupun POST.
    public function index(Request $request) // mengirimkan objek request (get/post) ke method indek, agar method bisa membaca data dari user
    {
        // Mengambil ID pengguna yang sedang login.
        $userId = (int) Auth::id();

        // Mengambil nama pengguna yang sedang login.
        $nama = Auth::user()->nama ?? ''; // jika tidak tersedia isi dengan null

        /*
         * Ambil alamat milik pengguna.
         */
        try {
            // Mengambil seluruh data alamat milik pengguna melalui Model Alamat.
            $semuaAlamat = $this->alamat->semua($userId, $nama); // melalui method semua pada model
        } catch (Throwable $e) {
            // Mencatat pesan kesalahan ke log server jika pengambilan alamat gagal.
            Log::error($e->getMessage());

            // Mengirim respons error HTTP 500 kepada pengguna.
            return response(
                'Terjadi kesalahan saat mengambil alamat pengiriman.',
                500
            );
        }

        /*
         * PROSES PERUBAHAN ALAMAT
         */
        // Memeriksa apakah pengguna mengirim form POST untuk mengubah alamat.
        if (
            $request->isMethod('post') &&
            $request->input('aksi') === 'ubah_alamat'
        ) {
            // Menyerahkan proses perubahan alamat ke method khusus dengan mengirimkan 3 argumen
            return $this->prosesUbahAlamat(
                $request, // data user
                $userId,
                $semuaAlamat
            );
        }

        /*
         * Ambil isi keranjang.
         */
        try {
            // Mengambil semua produk dalam keranjang milik pengguna.
            $items = $this->keranjang->daftar($userId);

            // Jika keranjang kosong, pengguna dikembalikan ke halaman keranjang.
            if (!$items) {
                return redirect('/keranjang')->with('keranjang', [
                    'ok' => false,
                    'pesan' => 'Keranjang masih kosong. Tambahkan produk dulu sebelum checkout.',
                ]);
            }

            // Mengambil pesan kesalahan checkout dari session jika tersedia.
            $error = session('errorCheckout', '');

            /*
             * KONFIRMASI PESANAN
             */
            // Jika request POST tidak memiliki input aksi,
            // permintaan dianggap sebagai konfirmasi pesanan.
            if (
                $request->isMethod('post') && // dan
                !$request->has('aksi') // artinya tidak ada aksi misal ubah alamat, maka yang terjadi adalah bisa melakukan konfirmasi pesananan
            ) {
                // Menyerahkan proses pembuatan pesanan ke method konfirmasi().
                return $this->konfirmasi(
                    $request,
                    $items,
                    $userId,
                    $semuaAlamat
                );
            }
        } catch (Throwable $e) {
            // Mencatat kesalahan server ke log.
            Log::error($e->getMessage());

            // Mengirim respons error HTTP 500.
            return response(
                'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                500
            );
        }

        // Menghitung subtotal seluruh produk dalam keranjang.
        $subtotal = Keranjang::subtotal($items);

        // Mengirim data yang diperlukan untuk menampilkan halaman checkout yang berisikan data berdasarkan isian user
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
     * METHOD PROSES PERUBAHAN ALAMAT
     */
    // Memproses perubahan informasi penerima dan alamat pengiriman.
    private function prosesUbahAlamat(Request $request,  int $userId, array $semuaAlamat) { // digunakan untuk menerima, data input form, id, dan daftar almat)
       
    // Mengambil jenis alamat yang ingin diubah.
        $label = $request->input('label'); // utama atau kantor, 

        // Mengambil nama penerima, mengubahnya menjadi string, lalu menghapus spasi di awal dan akhir.
        $nama = trim((string) $request->input('nama', ''));

        // Mengambil nomor telepon penerima dan membersihkan spasi di tepinya.
        $telp = trim((string) $request->input('telp', ''));

        // Mengambil alamat lengkap dan membersihkan spasi di tepinya.
        $alamat = trim((string) $request->input('alamat', ''));

        // Menyiapkan variabel pesan kesalahan.
        $error = '';

        // Memeriksa apakah label alamat benar-benar tersedia milik pengguna.
        if (!isset($semuaAlamat[$label])) { // digunakan untuk mengecek apakah alamat tesebut tersedia misal label diisi dengan alamat utama
            $error = 'Alamat tidak ditemukan.';

        // Memeriksa apakah nama kosong atau melebihi 100 karakter.
        } elseif ($nama === '' || mb_strlen($nama) > 100) {
            $error = 'Nama penerima wajib diisi (maksimal 100 karakter).';

        // Memeriksa format dan panjang nomor telepon.
        } elseif (!preg_match('/^[0-9+\s\-]{8,20}$/', $telp)) {
            $error = 'No. HP tidak valid (8–20 karakter, hanya angka, spasi, + atau -).';

        // Memastikan alamat terdiri dari 10 sampai 300 karakter.
        } elseif (mb_strlen($alamat) < 10 || mb_strlen($alamat) > 300) {
            $error = 'Alamat lengkap harus 10–300 karakter.';
        }

        // Jika tidak ditemukan kesalahan validasi, lanjutkan penyimpanan alamat.
        if ($error === '') {
            try {
                // Memanggil Model Alamat untuk memperbarui alamat pengguna. jadi memangi data yang sudah diperbarui
                $this->alamat->ubah(
                    $userId,
                    $label,
                    $nama,
                    $telp,
                    $alamat
                );

                // Menyimpan pesan sukses sementara di session.
                session()->flash('flash_alamat', [
                    'ok' => true,
                    'label' => $label,
                    'pesan' => 'Alamat berhasil diperbarui.',
                ]);

            } catch (Throwable $e) {
                // Mencatat kesalahan jika proses perubahan alamat gagal.
                Log::error($e->getMessage());

                // Menyiapkan pesan gagal untuk ditampilkan di halaman checkout.
                session()->flash('flash_alamat', [
                    'ok' => false,
                    'label' => $label,
                    'pesan' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                ]);
            }
        } else {
            // Jika validasi gagal, simpan pesan kesalahan ke session.
            session()->flash('flash_alamat', [
                'ok' => false,
                'label' => $label,
                'pesan' => $error,
            ]);
        }

        // Kembali ke halaman checkout agar pengguna dapat melihat hasil proses.
        return redirect('/checkout');
    }

    /**
     * KONFIRMASI PESANAN
     */
    // Memvalidasi pilihan pengiriman dan pembayaran,
    // kemudian meminta Model Pesanan membuat pesanan baru.
    private function konfirmasi( // digunakan untuk memproses konfirmasi checkout sebelum pesanan dibuat
        Request $request, //Menerima data permintaan dari pengguna, termasuk pilihan pengiriman dan pembayaran.
        array $items, //Menerima daftar produk yang ada di keranjang.
        int $userId, //Menerima ID pengguna yang sedang login.
        array $semuaAlamat //Menerima daftar alamat pengguna.
    ) {
        // Mengambil biaya pengiriman yang dipilih pengguna dari form.
        $ongkir = $request->input('shipping');

        // Memastikan pilihan ongkir berupa angka dan tersedia dalam daftar pengiriman.
        if (
            !is_string($ongkir) || // memastikan nilai yang dikirim berupa string '10000'
            $ongkir === '' || // tidak berupa null
            !ctype_digit($ongkir) || // harus berupa digit angka
            !isset(self::OPSI_PENGIRIMAN[(int) $ongkir]) // memastikan kesesuaian pilihan ongkir pengguna pada menu keranjang
        ) {
            // Jika pilihan pengiriman tidak valid, kembali dengan pesan kesalahan.
            return back()->withInput()->with(
                'errorCheckout',
                'Pilihan pengiriman tidak valid. Silakan coba lagi.'
            );
        }

        // Memastikan metode pembayaran yang dikirim adalah COD.
        if ($request->input('payment') !== 'cod') {
            return back()->withInput()->with(
                'errorCheckout',
                'Metode pembayaran tidak valid. Silakan coba lagi.'
            );
        }

        try {
            // Meminta Model Pesanan membuat pesanan berdasarkan isi keranjang.
            $kode = $this->pesanan->buatDariKeranjang(
                $userId, // ID pengguna pemilik pesanan.
                $items, // Daftar produk yang dipesan.
                (int) $ongkir, // Biaya pengiriman yang dipilih.
                self::OPSI_PENGIRIMAN[(int) $ongkir], // Nama layanan pengiriman.
                $semuaAlamat['utama'] // Data alamat utama untuk pesanan.
            );

            // Mengarahkan pengguna ke halaman pembayaran dengan membawa kode pesanan.
            return redirect()->route('pembayaran', [
                'kode' => $kode, // dengan membawa kode pesanan
            ]);

        } catch (Throwable $e) {
            // Mencatat kesalahan jika pembuatan pesanan gagal.
            Log::error($e->getMessage());

            // Kembali ke halaman sebelumnya dengan pesan kegagalan.
            return back()->withInput()->with(
                'errorCheckout',
                'Pesanan gagal dibuat. Silakan coba lagi.'
            );
        }
    }
}
