<?php

namespace App\Models;

// Menggunakan DB Facade untuk menjalankan query database.
use Illuminate\Support\Facades\DB;

// Class Alamat bertugas mengelola data alamat pengguna.
class Alamat
{
    // Daftar label alamat yang tersedia.
    public const LABEL = ['utama', 'kantor'];

    /**
     * Alamat bawaan untuk akun baru.
     */
    public static function bawaan(string $nama): array
    {
        // Mengembalikan dua alamat default: utama dan kantor.
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
     * Melihat seluruh alamat pengguna.
     * Jika belum lengkap, sistem membuat alamat bawaan.
     */
    public function semua(int $userId, string $nama): array // mengembalikan array, untuk mengetahui user yang sedang login, dan mengisi nama
    {
        // Mengambil alamat pengguna dari database. berdasarkan id pengguna
        $hasil = $this->baca($userId);

        // Memeriksa apakah jumlah alamat kurang dari jumlah label yang tersedia.
        if (count($hasil) < count(self::LABEL)) { 

            // Mengulang seluruh alamat bawaan.
            foreach (self::bawaan($nama) as $label => $alamat) { // maka data akan diulang dan diisi dengan alamat default
            
                // Memeriksa apakah alamat dengan label tersebut belum tersedia.
                if (!isset($hasil[$label])) { 

                    // Menambahkan alamat bawaan ke database jika belum ada.
                    DB::table('alamat')->insertOrIgnore([
                        'user_id' => $userId,
                        'label' => $label,
                        'nama' => $alamat['nama'],
                        'telp' => $alamat['telp'],
                        'alamat' => $alamat['alamat'],
                    ]);
                }
            }

            // Mengambil ulang alamat setelah proses penambahan.
            $hasil = $this->baca($userId);
        }

        // Mengembalikan seluruh alamat pengguna.
        return $hasil;
    }

    /**
     * Membaca alamat berdasarkan pengguna.
     */
    private function baca(int $userId): array
    {
        // Mengambil data alamat berdasarkan user_id.
        $baris = DB::table('alamat')
            ->select('label', 'nama', 'telp', 'alamat')
            ->where('user_id', $userId)
            ->get();

        // Menyiapkan array kosong untuk menampung hasil.
        $hasil = [];

        // Mengulang setiap baris data alamat.
        foreach ($baris as $item) {

            // Menyusun data berdasarkan label alamat.
            $hasil[$item->label] = [
                'nama' => $item->nama,
                'telp' => $item->telp,
                'alamat' => $item->alamat,
            ];
        }

        // Mengembalikan hasil dalam bentuk array.
        return $hasil;
    }

    /**
     * Mengubah satu alamat.
     */
    public function ubah(
        int $userId,
        string $label,
        string $nama,
        string $telp,
        string $alamat
    ): void {

        // Memperbarui alamat jika sudah ada,
        // atau menambahkan alamat jika belum ada.
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
