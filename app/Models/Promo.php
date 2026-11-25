<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class Promo
{
    public const JENIS = [
        'persen' => 'Persentase (%)',
        'nominal' => 'Nominal (Rp)',
    ];

    public const TARGET = [
        'Semua Pengguna',
        'Member Baru',
        'Pengguna Setia',
    ];

    public function daftar(string $q = '', string $status = ''): array
    {
        $query = DB::table('promo');

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->where('nama', 'like', "%{$q}%")
                    ->orWhere('kode', 'like', "%{$q}%");
            });
        }

        if ($status === 'Aktif') {
            $query->where('aktif', 1);
        } elseif ($status === 'Nonaktif') {
            $query->where('aktif', 0);
        }

        return $query->orderByDesc('id')->get()->map(
            fn ($item) => (array) $item
        )->all();
    }

    public function cariById(int $id): ?array
    {
        $data = DB::table('promo')->where('id', $id)->first();

        return $data ? (array) $data : null;
    }

    public function kodeTerpakai(string $kode, int $kecualiId = 0): bool
    {
        return DB::table('promo')
            ->where('kode', $kode)
            ->where('id', '<>', $kecualiId)
            ->exists();
    }

    public function tambah(array $data): int
    {
        return (int) DB::table('promo')->insertGetId($data);
    }

    public function ubah(int $id, array $data): void
    {
        DB::table('promo')
            ->where('id', $id)
            ->update($data);
    }

    public function hapus(int $id): bool
    {
        return DB::table('promo')
            ->where('id', $id)
            ->delete() === 1;
    }
}
