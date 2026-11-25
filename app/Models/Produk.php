<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Produk
{
    /**
     * Mencari satu produk berdasarkan ID.
     */
    public function cariById(int $id): ?array
    {
        $produk = DB::table('produk')
            ->select('id', 'nama', 'ket', 'satuan', 'harga')
            ->where('id', $id)
            ->first();

        return $produk ? (array) $produk : null;
    }

    /**
     * Mengambil seluruh produk katalog.
     */
    public function semua(): array
    {
        return DB::table('produk')
            ->select('id', 'nama', 'ket', 'satuan', 'harga')
            ->orderBy('id')
            ->get()
            ->map(fn ($produk) => (array) $produk)
            ->all();
    }

    /**
     * Mengambil beberapa produk berdasarkan daftar ID.
     * Urutan mengikuti ID produk.
     */
    public function daftarByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if (!$ids) {
            return [];
        }

        return DB::table('produk')
            ->select('id', 'nama', 'ket', 'satuan', 'harga')
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get()
            ->map(fn ($produk) => (array) $produk)
            ->all();
    }

    /**
     * Mengambil seluruh data produk untuk halaman Admin.
     */
    public function semuaUntukAdmin(): array
    {
        return DB::table('produk')
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
                'deskripsi'
            )
            ->orderBy('id')
            ->get()
            ->map(fn ($produk) => (array) $produk)
            ->all();
    }

    /**
     * Mencari produk Admin berdasarkan nama dan kategori.
     */
    public function cariUntukAdmin(
        string $keyword = '',
        string $kategori = ''
    ): array {
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
            'deskripsi'
        );

        if ($keyword !== '') {
            $query->where('nama', 'like', '%' . $keyword . '%');
        }

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        return $query
            ->orderBy('id')
            ->get()
            ->map(fn ($produk) => (array) $produk)
            ->all();
    }

    /**
     * Menambahkan produk baru.
     */
    public function tambah(array $data): int
    {
        return DB::table('produk')->insertGetId([
            'nama' => $data['nama'],
            'ket' => $data['ket'],
            'satuan' => $data['satuan'],
            'harga' => $data['harga'],
            'kategori' => $data['kategori'],
            'stok' => $data['stok'],
            'butuh_resep' => $data['butuh_resep'],
            'aktif' => $data['aktif'],
            'deskripsi' => $data['deskripsi'],
        ]);
    }

    /**
     * Mengubah data produk berdasarkan ID.
     */
    public function ubah(int $id, array $data): int
    {
        return DB::table('produk')
            ->where('id', $id)
            ->update([
                'nama' => $data['nama'],
                'ket' => $data['ket'],
                'satuan' => $data['satuan'],
                'harga' => $data['harga'],
                'kategori' => $data['kategori'],
                'stok' => $data['stok'],
                'butuh_resep' => $data['butuh_resep'],
                'aktif' => $data['aktif'],
                'deskripsi' => $data['deskripsi'],
            ]);
    }

    /**
     * Menghapus produk berdasarkan ID.
     */
    public function hapus(int $id): int
    {
        return DB::table('produk')
            ->where('id', $id)
            ->delete();
    }
}
