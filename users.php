<?php
/**
 * Data user contoh (sementara, sebelum database dipakai).
 *
 * Nanti cukup ganti isi fungsi cari_user() dengan query ke tabel users,
 * misalnya: SELECT * FROM users WHERE email = ? LIMIT 1
 * Selama hasilnya tetap array dengan kunci yang sama (email, nama, role, password),
 * halaman lain tidak perlu diubah.
 *
 * Akun contoh:
 *   admin@mail.com  / admin12345  (admin)
 *   andi@mail.com   / andi12345   (pelanggan)
 */

function cari_user(string $email): ?array
{
    $users = [
        'admin@mail.com' => [
            'email'    => 'admin@mail.com',
            'nama'     => 'Admin SehatKita',
            'role'     => 'admin',
            'password' => '$2y$10$UOf./V6x/RbgW2TgNXDlJuPh.yYCKZgrvzk2TLQf4b5Fgttc9AsZm',
        ],
        'andi@mail.com' => [
            'email'    => 'andi@mail.com',
            'nama'     => 'Andi Pratama',
            'role'     => 'user',
            'password' => '$2y$10$nK7liyjyQuFhAB6qVNM6Uu6Gu2KwA3muIy0tRwdVX7UK3FIyHuRTO',
        ],
    ];

    $key = strtolower(trim($email));
    return $users[$key] ?? null;
}
