<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * MODEL (hanya baca) — ringkasan angka untuk Dashboard Admin.
 * Mengambil data dari tabel milik fitur lain
 * (produk, promo, pesanan, users) tanpa mengubahnya.
 *
 * Menggunakan Laravel Query Builder.
 */
class Statistik
{
    /**
     * Mengambil satu angka dari database.
     */
    private function angka(string $tabel, string $kolom = '*'): int
    {
        return (int) DB::table($tabel)->count($kolom);
    }

    /**
     * Angka untuk kartu ringkasan.
     */
    public function ringkasan(): array
    {
        // Tabel promo belum tersedia di database.
        $promoAktif = 0;
        $promoBerakhir = 0;

        // Jika tabel promo tersedia, hitung seperti kode asli.
        if (Schema::hasTable('promo')) {
            $promoAktif = DB::table('promo')
                ->where('aktif', 1)
                ->whereDate('mulai', '<=', today())
                ->whereDate('selesai', '>=', today())
                ->count();

            $promoBerakhir = DB::table('promo')
                ->where('aktif', 1)
                ->whereBetween('selesai', [
                    today(),
                    today()->addDays(7),
                ])
                ->count();
        }

        return [
            'produk' => $this->angka('produk'),

            'promo_aktif' => $promoAktif,

            'promo_berakhir' => $promoBerakhir,

            'pesanan_hari' => DB::table('pesanan')
                ->whereDate('created_at', today())
                ->count(),

            'pesanan_proses' => DB::table('pesanan')
                ->where('status', 'Diproses')
                ->count(),

            'pengguna' => DB::table('users')
                ->where('role', 'user')
                ->count(),
        ];
    }

    /**
     * Promo yang sedang berjalan hari ini.
     */
    public function promoBerjalan(int $batas = 5): array
    {
        // Tabel promo belum tersedia.
        if (!Schema::hasTable('promo')) {
            return [];
        }

        return DB::table('promo')
            ->select('nama', 'kode', 'jenis', 'nilai', 'selesai')
            ->where('aktif', 1)
            ->whereDate('mulai', '<=', today())
            ->whereDate('selesai', '>=', today())
            ->orderBy('selesai')
            ->limit(max(0, $batas))
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Pesanan terbaru.
     */
    public function pesananTerbaru(int $batas = 5): array
    {
        return DB::table('pesanan')
            ->select(
                'kode',
                'alamat_nama',
                'total',
                'status',
                'created_at'
            )
            ->orderByDesc('id')
            ->limit(max(0, $batas))
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }
}
