<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

// MODEL KERANJANG
// Mengelola data keranjang pengguna, mulai dari melihat,
// menghitung, menambah, mengubah, hingga menghapus produk.
class Keranjang
{
    // Menentukan batas maksimal jumlah produk dalam keranjang.
    public const MAKS_JUMLAH = 99;

    /**
     * Method subtotal() digunakan untuk menghitung total harga
     * seluruh produk berdasarkan harga dikali jumlah.
     * Bersifat static sehingga dapat dipanggil langsung melalui nama class.
     */
    public static function subtotal(array $items): int
    {
        $total = 0;

        // Menghitung harga setiap produk lalu menjumlahkannya.
        foreach ($items as $it) {
            $total += (int) $it['harga'] * (int) $it['jumlah'];
        }

        return $total;
    }

    /**
     * Method daftar() digunakan untuk mengambil seluruh isi keranjang
     * milik pengguna beserta informasi produknya dari database.
     */
    public function daftar(int $userId): array
    {
        return DB::table('keranjang as k')
            // JOIN menggabungkan tabel keranjang dengan tabel produk
            // agar informasi nama, harga, dan satuan produk ikut diambil.
            ->join('produk as p', 'p.id', '=', 'k.produk_id')

            // Membatasi data agar hanya menampilkan keranjang milik user tersebut.
            ->where('k.user_id', $userId)

            ->select(
                'k.id',
                'k.produk_id',
                'k.jumlah',
                'p.nama',
                'p.ket',
                'p.satuan',
                'p.harga'
            )
            ->orderBy('k.id')
            ->get()

            // Mengubah setiap objek hasil query menjadi array PHP.
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Method hitungBaris() digunakan untuk menghitung jumlah baris
     * produk yang tersimpan di keranjang milik pengguna.
     * Hasilnya adalah jumlah jenis item, bukan total kuantitas produk.
     */
    public function hitungBaris(int $userId): int
    {
        return (int) DB::table('keranjang')
            ->where('user_id', $userId)
            ->count();
    }

    /**
     * Method jumlahItem() digunakan untuk mengambil jumlah produk
     * berdasarkan ID item dan ID pemilik keranjang.
     * Mengembalikan null jika item tidak ditemukan.
     */
    public function jumlahItem(int $userId, int $itemId): ?int
    {
        $jumlah = DB::table('keranjang')
            ->where('id', $itemId)
            ->where('user_id', $userId)
            ->value('jumlah');

        // Operator ternary: jika data tidak ditemukan, hasil null.
        // Jika ditemukan, nilai jumlah dikembalikan sebagai integer.
        return $jumlah === null ? null : (int) $jumlah;
    }

    /**
     * Method tambah() digunakan untuk memasukkan produk ke keranjang.
     * Jika produk yang sama sudah ada, jumlahnya akan ditambahkan,
     * tetapi tidak boleh melebihi batas maksimal 99.
     */
    public function tambah(int $userId, int $produkId, int $jumlah = 1): void
    {
        // UPSERT: memasukkan data baru atau memperbarui data
        // jika kombinasi user_id dan produk_id sudah tersedia.
        DB::table('keranjang')->upsert(
            [
                [
                    'user_id' => $userId,
                    'produk_id' => $produkId,
                    'jumlah' => min($jumlah, self::MAKS_JUMLAH),
                ],
            ],
            ['user_id', 'produk_id'],

            // DB::raw menjalankan ekspresi SQL untuk menjumlahkan
            // jumlah lama dengan jumlah baru, lalu membatasi hasil maksimal 99.
            [
                'jumlah' => DB::raw(
                    'LEAST(jumlah + VALUES(jumlah), ' . self::MAKS_JUMLAH . ')'
                ),
            ]
        );
    }

    /**
     * Method ubahJumlah() digunakan untuk memperbarui jumlah produk
     * tertentu dalam keranjang milik pengguna.
     * Mengembalikan false jika item tidak ditemukan.
     */
    public function ubahJumlah(int $userId, int $itemId, int $jumlah): bool
    {
        // Memastikan item benar-benar ada dan dimiliki oleh user tersebut.
        if ($this->jumlahItem($userId, $itemId) === null) {
            return false;
        }

        DB::table('keranjang')
            ->where('id', $itemId)
            ->where('user_id', $userId)
            ->update([
                'jumlah' => $jumlah,
            ]);

        return true;
    }

    /**
     * Method hapus() digunakan untuk menghapus satu produk
     * dari keranjang berdasarkan ID item dan ID pemiliknya.
     */
    public function hapus(int $userId, int $itemId): bool
    {
        return DB::table('keranjang')
            ->where('id', $itemId)
            ->where('user_id', $userId)

            // Mengembalikan true jika tepat satu baris berhasil dihapus.
            ->delete() === 1;
    }
}
