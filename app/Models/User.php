<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'no_hp',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    /**
     * Cari user untuk login berdasarkan email atau username.
     */
    public function scopeCariUntukLogin($query, string $identitas)
    {
        $identitas = strtolower(trim($identitas));

        return $query->where('email', $identitas)
            ->orWhere('username', $identitas);
    }

    /**
     * Mengambil data user berdasarkan ID tanpa password.
     */
    public function cariById(int $id): ?array
    {
        $user = self::find($id);

        if (!$user) {
            return null;
        }

        return $user->only([
            'id',
            'username',
            'email',
            'nama',
            'no_hp',
            'role',
            'created_at',
        ]);
    }

    /**
     * Mengambil hash password berdasarkan ID.
     */
    public function ambilHash(int $id): ?string
    {
        $user = self::find($id);

        return $user?->password;
    }

    /**
     * Memeriksa apakah username sudah digunakan.
     */
    public function usernameTerpakai(
        string $username,
        int $kecualiId = 0
    ): bool {
        return self::where('username', strtolower($username))
            ->where('id', '<>', $kecualiId)
            ->exists();
    }

    /**
     * Memeriksa apakah email sudah digunakan.
     */
    public function emailTerpakai(
        string $email,
        int $kecualiId = 0
    ): bool {
        return self::where('email', strtolower($email))
            ->where('id', '<>', $kecualiId)
            ->exists();
    }

    /**
     * Membuat akun baru.
     * Role pengguna baru selalu user.
     */
    public function buat(
        string $nama,
        string $username,
        string $email,
        string $noHp,
        string $passwordPlain
    ): int {
        $user = self::create([
            'nama'     => $nama,
            'username' => strtolower($username),
            'email'    => strtolower($email),
            'no_hp'    => $noHp !== '' ? $noHp : null,
            'password' => Hash::make($passwordPlain),
            'role'     => 'user',
        ]);

        return (int) $user->id;
    }

    /**
     * Mengubah profil pengguna.
     */
    public function ubahProfil(
        int $id,
        string $nama,
        string $username,
        string $email,
        string $noHp
    ): void {
        self::where('id', $id)->update([
            'nama'     => $nama,
            'username' => strtolower($username),
            'email'    => strtolower($email),
            'no_hp'    => $noHp !== '' ? $noHp : null,
        ]);
    }

    /**
     * Mengubah password pengguna.
     */
    public function ubahPassword(
        int $id,
        string $passwordPlain
    ): void {
        self::where('id', $id)->update([
            'password' => Hash::make($passwordPlain),
        ]);
    }

    /**
     * Menghitung jumlah admin.
     */
    public function hitungAdmin(): int
    {
        return self::where('role', 'admin')->count();
    }

    /**
     * Menghapus pengguna berdasarkan ID.
     */
    public function hapus(int $id): bool
    {
        return self::where('id', $id)->delete() === 1;
    }

    /**
     * Mengambil daftar pelanggan untuk admin.
     */
    public function daftarPelanggan(): array
    {
        return self::where('role', 'user')
            ->orderBy('nama')
            ->get([
                'id',
                'nama',
                'username',
                'email',
            ])
            ->toArray();
    }
}
