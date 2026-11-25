<?php

namespace App\Models;

use DateTime;
use Illuminate\Support\Facades\DB;

class PengingatObat
{
    public const FREKUENSI = [1, 2, 3, 4];
    public const MAKS_PER_USER = 20;

    /**
     * LIHAT: semua pengingat milik pengguna,
     * diurutkan dari yang terbaru.
     */
    public function daftar(int $userId): array
    {
        return DB::table('pengingat_obat')
            ->selectRaw(
                'id, nama, dosis, frekuensi,
                TIME_FORMAT(jam_mulai, "%H:%i") AS jam_mulai,
                durasi_hari, tanggal_mulai, dokter, catatan, aktif'
            )
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
    }

    /**
     * Mencari pengingat berdasarkan ID
     * sekaligus memastikan pemiliknya benar.
     */
    public function cariMilik(int $id, int $userId): ?array
    {
        $hasil = DB::table('pengingat_obat')
            ->select('id')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        return $hasil ? (array) $hasil : null;
    }

    /**
     * Menghitung jumlah pengingat milik pengguna.
     */
    public function hitung(int $userId): int
    {
        return DB::table('pengingat_obat')
            ->where('user_id', $userId)
            ->count();
    }

    /**
     * TAMBAH pengingat baru.
     * Mengembalikan ID pengingat yang baru dibuat.
     */
    public function tambah(int $userId, array $d): int
    {
        return (int) DB::table('pengingat_obat')->insertGetId([
            'user_id' => $userId,
            'nama' => $d['nama'],
            'dosis' => $d['dosis'],
            'frekuensi' => $d['frekuensi'],
            'jam_mulai' => $d['jam_mulai'],
            'durasi_hari' => $d['durasi_hari'],
            'tanggal_mulai' => now()->toDateString(),
            'dokter' => $d['dokter'] ?? null,
            'catatan' => $d['catatan'] ?? null,
            'aktif' => 1,
        ]);
    }

    /**
     * UBAH pengingat milik pengguna.
     */
    public function ubah(int $id, int $userId, array $d): void
    {
        DB::table('pengingat_obat')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'nama' => $d['nama'],
                'dosis' => $d['dosis'],
                'frekuensi' => $d['frekuensi'],
                'jam_mulai' => $d['jam_mulai'],
                'durasi_hari' => $d['durasi_hari'],
                'dokter' => $d['dokter'] ?? null,
                'catatan' => $d['catatan'] ?? null,
            ]);
    }

    /**
     * Mengaktifkan atau menjeda pengingat.
     */
    public function setAktif(int $id, int $userId, bool $aktif): void
    {
        DB::table('pengingat_obat')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'aktif' => $aktif ? 1 : 0,
            ]);
    }

    /**
     * MENGHAPUS pengingat.
     * Mengembalikan true jika berhasil menghapus satu baris.
     */
    public function hapus(int $id, int $userId): bool
    {
        return DB::table('pengingat_obat')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete() === 1;
    }

    /**
     * Menghitung jadwal minum obat dalam sehari.
     *
     * Contoh:
     * jam mulai 07:00, frekuensi 3
     * hasil: 07:00, 15:00, 23:00
     */
    public static function jadwal(string $jamMulai, int $frekuensi): array
    {
        [$h, $m] = array_map('intval', explode(':', $jamMulai));

        $selang = intdiv(24, max(1, $frekuensi));

        $hasil = [];

        for ($i = 0; $i < $frekuensi; $i++) {
            $hasil[] = sprintf(
                '%02d:%02d',
                ($h + $i * $selang) % 24,
                $m
            );
        }

        return $hasil;
    }

    /**
     * Menghitung sisa hari pengobatan.
     * Nilai 0 berarti pengobatan sudah selesai.
     */
    public static function sisaHari(string $tanggalMulai, int $durasi): int
    {
        $berjalan = (new DateTime($tanggalMulai))
            ->diff(new DateTime('today'))->days;

        return max(0, $durasi - (int) $berjalan);
    }
}
