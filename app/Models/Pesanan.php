<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use RuntimeException;

class Pesanan
{
    /** Satu-satunya metode pembayaran saat ini: bayar di tempat. */
    public const METODE = 'cod';

    public const STATUS_AWAL = 'Diproses';

    /** Status yang boleh dipilih admin. */
    public const STATUS = [
        'Menunggu Pembayaran',
        'Diproses',
        'Dikirim',
        'Selesai',
        'Dibatalkan',
    ];

    /**
     * Buat pesanan dari isi keranjang user.
     * Pesanan, rincian barang, dan pengosongan keranjang
     * dilakukan dalam satu transaksi.
     */
    public function buatDariKeranjang(
        int $userId,
        array $items,
        int $ongkir,
        string $kurir,
        array $alamat
    ): string {
        $subtotal = Keranjang::subtotal($items);

        for ($coba = 0; $coba < 5; $coba++) {
            $kode = 'SK-' . random_int(10000, 99999);

            try {
                DB::transaction(function () use (
                    $userId,
                    $items,
                    $ongkir,
                    $kurir,
                    $alamat,
                    $subtotal,
                    $kode
                ) {
                    $pesananId = DB::table('pesanan')->insertGetId([
                        'kode' => $kode,
                        'user_id' => $userId,
                        'kurir' => $kurir,
                        'ongkir' => $ongkir,
                        'subtotal' => $subtotal,
                        'total' => $subtotal + $ongkir,
                        'metode' => self::METODE,
                        'status' => self::STATUS_AWAL,
                        'alamat_nama' => $alamat['nama'],
                        'alamat_telp' => $alamat['telp'],
                        'alamat_teks' => $alamat['alamat'],
                    ]);

                    $this->simpanItems((int) $pesananId, $items);

                    DB::table('keranjang')
                        ->where('user_id', $userId)
                        ->delete();
                });

                return $kode;

            } catch (QueryException $e) {
                if ((string) $e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Gagal membuat kode pesanan unik.');
    }

    /**
     * Pesanan milik user.
     * Null bila tidak ada atau milik pengguna lain.
     */
    public function cariByKode(int $userId, string $kode): ?array
    {
        $pesanan = DB::table('pesanan')
            ->where('kode', $kode)
            ->where('user_id', $userId)
            ->first();

        return $pesanan ? (array) $pesanan : null;
    }

    /**
     * Mengambil rincian barang berdasarkan ID pesanan.
     */
    public function items(int $pesananId): array
    {
        return DB::table('pesanan_item')
            ->select('nama', 'ket', 'harga', 'jumlah')
            ->where('pesanan_id', $pesananId)
            ->orderBy('id')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Riwayat pesanan pengguna, terbaru dahulu.
     * Jumlah = banyak jenis produk.
     */
    public function daftarUser(int $userId): array
    {
        return DB::table('pesanan as p')
            ->select(
                'p.kode',
                'p.total',
                'p.metode',
                'p.status',
                'p.created_at'
            )
            ->selectSub(
                DB::table('pesanan_item as i')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('i.pesanan_id', 'p.id'),
                'jumlah'
            )
            ->where('p.user_id', $userId)
            ->orderByDesc('p.id')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * LIHAT (admin):
     * Semua pesanan beserta nama pelanggan.
     */
    public function daftarAdmin(string $q = '', string $status = ''): array
    {
        $query = DB::table('pesanan as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->select(
                'p.id',
                'p.kode',
                'p.user_id',
                'p.kurir',
                'p.ongkir',
                'p.subtotal',
                'p.total',
                'p.metode',
                'p.status',
                'p.alamat_nama',
                'p.alamat_telp',
                'p.alamat_teks',
                'p.created_at',
                'u.nama as pelanggan',
                'u.email as email'
            );

        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';

            $query->where(function ($subQuery) use ($like) {
                $subQuery->where('p.kode', 'LIKE', $like)
                    ->orWhere('u.nama', 'LIKE', $like)
                    ->orWhere('u.username', 'LIKE', $like);
            });
        }

        if ($status !== '') {
            $query->where('p.status', $status);
        }

        return $query
            ->orderByDesc('p.id')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Rincian barang untuk banyak pesanan sekaligus.
     * Format: [pesanan_id => [item, item, ...]]
     */
    public function itemsBanyak(array $ids): array
    {
        $ids = array_values(
            array_unique(
                array_map('intval', $ids)
            )
        );

        if (!$ids) {
            return [];
        }

        $baris = DB::table('pesanan_item')
            ->select('pesanan_id', 'nama', 'ket', 'harga', 'jumlah')
            ->whereIn('pesanan_id', $ids)
            ->orderBy('id')
            ->get();

        $hasil = [];

        foreach ($baris as $item) {
            $item = (array) $item;

            $hasil[(int) $item['pesanan_id']][] = $item;
        }

        return $hasil;
    }

    /**
     * Mencari pesanan berdasarkan ID.
     */
    public function cariById(int $id): ?array
    {
        $pesanan = DB::table('pesanan')
            ->where('id', $id)
            ->first();

        return $pesanan ? (array) $pesanan : null;
    }

    /**
     * TAMBAH (admin):
     * Satu pesanan beserta rincian barangnya.
     */
    public function buatAdmin(
        int $userId,
        array $baris,
        string $kurir,
        int $ongkir,
        string $status,
        array $alamat
    ): string {
        $subtotal = self::hitungSubtotal($baris);

        for ($coba = 0; $coba < 5; $coba++) {
            $kode = 'SK-' . random_int(10000, 99999);

            try {
                DB::transaction(function () use (
                    $userId,
                    $baris,
                    $kurir,
                    $ongkir,
                    $status,
                    $alamat,
                    $subtotal,
                    $kode
                ) {
                    $pesananId = DB::table('pesanan')->insertGetId([
                        'kode' => $kode,
                        'user_id' => $userId,
                        'kurir' => $kurir,
                        'ongkir' => $ongkir,
                        'subtotal' => $subtotal,
                        'total' => $subtotal + $ongkir,
                        'metode' => self::METODE,
                        'status' => $status,
                        'alamat_nama' => $alamat['nama'],
                        'alamat_telp' => $alamat['telp'],
                        'alamat_teks' => $alamat['alamat'],
                    ]);

                    $this->simpanItems((int) $pesananId, $baris);
                });

                return $kode;

            } catch (QueryException $e) {
                if ((string) $e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }

        throw new RuntimeException('Gagal membuat kode pesanan unik.');
    }

    /**
     * UBAH (admin):
     * Memperbarui pesanan dan mengganti seluruh rincian barang.
     */
    public function ubahAdmin(
        int $id,
        int $userId,
        array $baris,
        string $kurir,
        int $ongkir,
        string $status,
        array $alamat
    ): void {
        $subtotal = self::hitungSubtotal($baris);

        DB::transaction(function () use (
            $id,
            $userId,
            $baris,
            $kurir,
            $ongkir,
            $status,
            $alamat,
            $subtotal
        ) {
            DB::table('pesanan')
                ->where('id', $id)
                ->update([
                    'user_id' => $userId,
                    'kurir' => $kurir,
                    'ongkir' => $ongkir,
                    'subtotal' => $subtotal,
                    'total' => $subtotal + $ongkir,
                    'status' => $status,
                    'alamat_nama' => $alamat['nama'],
                    'alamat_telp' => $alamat['telp'],
                    'alamat_teks' => $alamat['alamat'],
                ]);

            DB::table('pesanan_item')
                ->where('pesanan_id', $id)
                ->delete();

            $this->simpanItems($id, $baris);
        });
    }

    /**
     * HAPUS (admin).
     * Rincian barang mengikuti aturan ON DELETE CASCADE.
     */
    public function hapusAdmin(int $id): bool
    {
        return DB::table('pesanan')
            ->where('id', $id)
            ->delete() === 1;
    }

    /**
     * Menyimpan rincian barang pesanan.
     */
    private function simpanItems(int $pesananId, array $baris): void
    {
        foreach ($baris as $b) {
            DB::table('pesanan_item')->insert([
                'pesanan_id' => $pesananId,
                'nama' => $b['nama'],
                'ket' => $b['ket'],
                'harga' => (int) $b['harga'],
                'jumlah' => (int) $b['jumlah'],
            ]);
        }
    }

    /**
     * Menghitung subtotal.
     */
    private static function hitungSubtotal(array $baris): int
    {
        $total = 0;

        foreach ($baris as $b) {
            $total += (int) $b['harga'] * (int) $b['jumlah'];
        }

        return $total;
    }
}
