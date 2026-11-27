<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Favorit
{
    public const MAKS_CATATAN = 100;

    /**
     * Mengambil daftar produk favorit milik pengguna.
     */
   
public function daftar(int $userId): array
{
    return DB::table('favorit as f')
        ->join('produk as p', 'p.id', '=', 'f.produk_id')
        ->where('f.user_id', $userId)
        ->orderByDesc('f.id')
        ->get([
            'p.id',
            'p.nama',
            'p.ket',
            'p.satuan',
            'p.harga',
            'p.foto',
            'f.catatan',
        ])
        ->map(fn ($item) => (array) $item)
        ->all();
}

    /**
     * Mengambil ID produk yang sudah difavoritkan pengguna.
     */
    public function idProduk(int $userId): array
    {
        return DB::table('favorit')
            ->where('user_id', $userId)
            ->pluck('produk_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Memeriksa apakah produk sudah masuk favorit.
     */
    public function ada(int $userId, int $produkId): bool
    {
        return DB::table('favorit')
            ->where('user_id', $userId)
            ->where('produk_id', $produkId)
            ->exists();
    }

    /**
     * Menambahkan produk ke favorit.
     * False jika produk sudah ada.
     */
    public function tambah(int $userId, int $produkId): bool
    {
        return DB::table('favorit')->insertOrIgnore([
            'user_id' => $userId,
            'produk_id' => $produkId,
        ]) === 1;
    }

    /**
     * Mengubah catatan favorit.
     * Nilai null berarti menghapus catatan.
     */
    public function ubahCatatan(
        int $userId,
        int $produkId,
        ?string $catatan
    ): void {
        DB::table('favorit')
            ->where('user_id', $userId)
            ->where('produk_id', $produkId)
            ->update([
                'catatan' => $catatan,
            ]);
    }

    /**
     * Menghapus produk dari favorit.
     */
    public function hapus(int $userId, int $produkId): bool
    {
        return DB::table('favorit')
            ->where('user_id', $userId)
            ->where('produk_id', $produkId)
            ->delete() === 1;
    }
}
