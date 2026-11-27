<?php

namespace App\Models; // Menentukan bahwa class Produk berada di folder App\Models.

use Illuminate\Support\Facades\DB; // Mengimpor DB Facade untuk menjalankan query ke database.

class Produk
{
    /**
     * Mencari satu produk berdasarkan ID.
     */
    // Mengambil satu produk berdasarkan ID.
    // Mengembalikan array jika ditemukan atau null jika tidak ditemukan.
    public function cariById(int $id): ?array
    {
        $produk = DB::table('produk') // Mengakses tabel produk.
            ->select('id', 'nama', 'ket', 'satuan', 'harga') // Memilih kolom yang diperlukan.
            ->where('id', $id) // Mencari produk dengan ID yang sesuai.
            ->first(); // Mengambil satu baris data pertama yang ditemukan.

        return $produk ? (array) $produk : null; // Mengubah objek menjadi array atau mengembalikan null.
    }

    /**
     * Mengambil seluruh produk katalog.
     */
    // Mengambil seluruh produk untuk ditampilkan pada katalog.
    public function semua(): array
    {
        return DB::table('produk') // Mengakses tabel produk.
            ->select('id', 'nama', 'ket', 'satuan', 'harga', 'foto') // Mengambil kolom untuk tampilan katalog.
            ->orderBy('id') // Mengurutkan produk berdasarkan ID.
            ->get() // Mengambil seluruh hasil query.
            ->map(fn ($produk) => (array) $produk) // Mengubah setiap objek produk menjadi array.
            ->all(); // Mengubah Collection menjadi array PHP biasa.
    }

    /**
     * Mengambil beberapa produk berdasarkan daftar ID.
     * Urutan mengikuti ID produk.
     */
    // Mengambil produk tertentu berdasarkan kumpulan ID yang diberikan.
    public function daftarByIds(array $ids): array
    {
        
        $ids = array_values(array_unique(array_map('intval', $ids))); //Kode tersebut digunakan untuk mengubah seluruh ID menjadi integer, menghapus ID yang duplikat, dan merapikan indeks array.

        // Jika daftar ID kosong, langsung mengembalikan array kosong.
        if (!$ids) {
            return [];
        }

        return DB::table('produk') // Mengakses tabel produk.
            ->select('id', 'nama', 'ket', 'satuan', 'harga', 'foto') // Memilih data produk yang diperlukan.
            ->whereIn('id', $ids) // Mengambil produk yang ID-nya terdapat dalam daftar $ids.
            ->orderBy('id') // Mengurutkan berdasarkan ID produk.
            ->get() // Mengambil hasil query.
            ->map(fn ($produk) => (array) $produk) // Mengubah setiap objek menjadi array.
            ->all(); // Menghasilkan array PHP biasa.
    }

    /**
     * Mengambil seluruh data produk untuk halaman Admin.
     */
    // Mengambil data produk dengan informasi lengkap untuk pengelolaan Admin.
    public function semuaUntukAdmin(): array
    {
        return DB::table('produk') // Mengakses tabel produk.
            ->select(
                'id',
                'nama',
                'ket',
                'satuan',
                'harga',
                'kategori',
                'stok',
                'butuh_resep',
                'aktif',
                'deskripsi',
                'foto'
            ) // Memilih kolom produk yang dibutuhkan halaman Admin.
            ->orderBy('id') // Mengurutkan data berdasarkan ID.
            ->get() // Mengambil seluruh hasil query.
            ->map(fn ($produk) => (array) $produk) // Mengubah objek menjadi array.
            ->all(); // Mengubah Collection menjadi array biasa.
    }

    /**
     * Mencari produk Admin berdasarkan nama dan kategori.
     */
    // Mencari produk berdasarkan kata kunci nama dan/atau kategori.
    // Parameter memiliki nilai default string kosong.
    public function cariUntukAdmin(
        string $keyword = '',
        string $kategori = ''
    ): array {
        // Membuat query awal dengan memilih kolom produk yang diperlukan.
        $query = DB::table('produk')->select(
            'id',
            'nama',
            'ket',
            'satuan',
            'harga',
            'kategori',
            'stok',
            'butuh_resep',
            'aktif',
            'deskripsi',
            'foto'
        );

        // Jika kata kunci nama diisi, lakukan pencarian berdasarkan nama.
        if ($keyword !== '') {
            $query->where('nama', 'like', '%' . $keyword . '%');
        }

        // Jika kategori diisi, tampilkan produk dari kategori tersebut.
        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        return $query
            ->orderBy('id') // Mengurutkan hasil berdasarkan ID.
            ->get() // Menjalankan query dan mengambil hasilnya.
            ->map(fn ($produk) => (array) $produk) // Mengubah objek menjadi array.
            ->all(); // Menghasilkan array PHP biasa.
    }

    /**
     * Menambahkan produk baru.
     */
    // Menyimpan produk baru ke tabel produk.
    // Data diterima dalam bentuk array dari Controller.
    public function tambah(array $data): int
    {
        return DB::table('produk')->insertGetId([ //mengambil ID dari produk yang berhasil ditambahkan.
            'nama' => $data['nama'], // Menyimpan nama produk.
            'ket' => $data['ket'], // Menyimpan keterangan produk.
            'satuan' => $data['satuan'], // Menyimpan satuan produk.
            'harga' => $data['harga'], // Menyimpan harga produk.
            'kategori' => $data['kategori'], // Menyimpan kategori produk.
            'stok' => $data['stok'], // Menyimpan jumlah stok.
            'butuh_resep' => $data['butuh_resep'], // Menyimpan informasi apakah produk membutuhkan resep.
            'aktif' => $data['aktif'], // Menyimpan status aktif produk.
            'deskripsi' => $data['deskripsi'], // Menyimpan deskripsi produk.
            'foto' => $data['foto'] ?? null, // Menyimpan foto atau null jika foto tidak diberikan.
        ]);
    }

    /**
     * Mengubah data produk berdasarkan ID.
     */
    // Memperbarui data produk yang sudah ada berdasarkan ID.
    public function ubah(int $id, array $data): int
    {
        // Menyiapkan kolom dan nilai baru yang akan diperbarui.
        $perubahan = [
            'nama' => $data['nama'], // $data dari method tambah yang berahsal dari parameter array yang diberi nama $data
            'ket' => $data['ket'],
            'satuan' => $data['satuan'],
            'harga' => $data['harga'],
            'kategori' => $data['kategori'],
            'stok' => $data['stok'],
            'butuh_resep' => $data['butuh_resep'],
            'aktif' => $data['aktif'],
            'deskripsi' => $data['deskripsi'],
        ];

        // Foto hanya diperbarui jika Admin memberikan foto baru.
        if (array_key_exists('foto', $data)) {
            $perubahan['foto'] = $data['foto'];
        }

        return DB::table('produk') // Mengakses tabel produk.
            ->where('id', $id) // Menentukan produk yang ingin diubah.
            ->update($perubahan); // Menyimpan perubahan ke database.
    }

    /**
     * Menghapus produk berdasarkan ID.
     */
    // Menghapus produk dari tabel produk berdasarkan ID.
    public function hapus(int $id): int
    {
        return DB::table('produk') // Mengakses tabel produk.
            ->where('id', $id) // Menentukan produk yang akan dihapus.
            ->delete(); // Menghapus data dan mengembalikan jumlah baris yang terhapus.
    }
}
