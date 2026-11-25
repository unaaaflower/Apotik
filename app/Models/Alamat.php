<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Alamat
{
    public const LABEL = ['utama', 'kantor'];

    /**
     * Alamat bawaan untuk akun baru.
     */
    public static function bawaan(string $nama): array
    {
        return [
            'utama' => [
                'nama' => $nama,
                'telp' => '0812 3456 7890',
                'alamat' => 'Jl. Melati No. 12, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141',
            ],
            'kantor' => [
                'nama' => $nama,
                'telp' => '0812 3456 7890',
                'alamat' => 'Jl. Ijen No. 45, Kec. Klojen, Kota Malang, Jawa Timur 65119',
            ],
        ];
    }

    /**
     * LIHAT seluruh alamat pengguna.
     * Alamat yang belum tersedia dibuat menggunakan nilai bawaan.
     */
    public function semua(int $userId, string $nama): array
    {
        $hasil = $this->baca($userId);

        if (count($hasil) < count(self::LABEL)) {
            foreach (self::bawaan($nama) as $label => $alamat) {
                if (!isset($hasil[$label])) {
                    DB::table('alamat')->insertOrIgnore([
                        'user_id' => $userId,
                        'label' => $label,
                        'nama' => $alamat['nama'],
                        'telp' => $alamat['telp'],
                        'alamat' => $alamat['alamat'],
                    ]);
                }
            }

            $hasil = $this->baca($userId);
        }

        return $hasil;
    }

    /**
     * Membaca alamat berdasarkan pengguna.
     */
    private function baca(int $userId): array
    {
        $baris = DB::table('alamat')
            ->select('label', 'nama', 'telp', 'alamat')
            ->where('user_id', $userId)
            ->get();

        $hasil = [];

        foreach ($baris as $item) {
            $hasil[$item->label] = [
                'nama' => $item->nama,
                'telp' => $item->telp,
                'alamat' => $item->alamat,
            ];
        }

        return $hasil;
    }

    /**
     * UBAH satu alamat.
     */
    public function ubah(
        int $userId,
        string $label,
        string $nama,
        string $telp,
        string $alamat
    ): void {
        DB::table('alamat')->updateOrInsert(
            [
                'user_id' => $userId,
                'label' => $label,
            ],
            [
                'nama' => $nama,
                'telp' => $telp,
                'alamat' => $alamat,
            ]
        );
    }
}
