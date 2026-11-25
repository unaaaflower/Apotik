<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

/**
 * MODEL — keranjang belanja (tabel `keranjang`).
 * Setiap query dibatasi user_id milik pengguna yang login.
 */
class Keranjang
{
    public const MAKS_JUMLAH = 99;

    /**
     * Total harga isi keranjang:
     * jumlah x harga tiap baris.
     */
    public static function subtotal(array $items): int
    {
        $total = 0;

        foreach ($items as $it) {
            $total += (int) $it['harga'] * (int) $it['jumlah'];
        }

        return $total;
    }

    /**
     * LIHAT: seluruh isi keranjang user beserta data produknya.
     */
    public function daftar(int $userId): array
    {
        return DB::table('keranjang as k')
            ->join('produk as p', 'p.id', '=', 'k.produk_id')
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
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Jumlah baris produk di keranjang.
     */
    public function hitungBaris(int $userId): int
    {
        return (int) DB::table('keranjang')
            ->where('user_id', $userId)
            ->count();
    }

    /**
     * Mengambil jumlah produk tertentu.
     */
    public function jumlahItem(int $userId, int $itemId): ?int
    {
        $jumlah = DB::table('keranjang')
            ->where('id', $itemId)
            ->where('user_id', $userId)
            ->value('jumlah');

        return $jumlah === null ? null : (int) $jumlah;
    }

    /**
     * TAMBAH:
     * Produk baru masuk.
     * Jika sudah ada, jumlah ditambah maksimal 99.
     */
    public function tambah(int $userId, int $produkId, int $jumlah = 1): void
    {
        DB::table('keranjang')->upsert(
            [
                [
                    'user_id' => $userId,
                    'produk_id' => $produkId,
                    'jumlah' => min($jumlah, self::MAKS_JUMLAH),
                ],
            ],
            ['user_id', 'produk_id'],
            [
                'jumlah' => DB::raw(
                    'LEAST(jumlah + VALUES(jumlah), ' . self::MAKS_JUMLAH . ')'
                ),
            ]
        );
    }

    /**
     * UBAH:
     * Mengatur jumlah baru.
     * False jika item tidak ada atau bukan milik user.
     */
    public function ubahJumlah(int $userId, int $itemId, int $jumlah): bool
    {
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
     * HAPUS: satu item keranjang.
     */
    public function hapus(int $userId, int $itemId): bool
    {
        return DB::table('keranjang')
            ->where('id', $itemId)
            ->where('user_id', $userId)
            ->delete() === 1;
    }
}
