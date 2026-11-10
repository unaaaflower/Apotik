<?php
/**
 * Validasi input sisi server (validasi JavaScript hanya kemudahan, bukan pengaman).
 * Setiap fungsi mengembalikan pesan error, atau '' bila input valid.
 */

function validasi_nama(string $nama): string
{
    $panjang = mb_strlen($nama);
    if ($panjang < 3 || $panjang > 100) {
        return 'Nama lengkap harus 3–100 karakter.';
    }
    return '';
}

function validasi_username(string $username): string
{
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        return 'Username 3–30 karakter, hanya huruf, angka, dan garis bawah (_).';
    }
    return '';
}

function validasi_email(string $email): string
{
    if (mb_strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Masukkan alamat email yang valid.';
    }
    return '';
}

/** $wajib = false: boleh kosong (dipakai di Ubah Profil). */
function validasi_no_hp(string $noHp, bool $wajib = true): string
{
    if ($noHp === '') {
        return $wajib ? 'Nomor HP wajib diisi.' : '';
    }
    if (!preg_match('/^[0-9+\s\-]{9,15}$/', $noHp)) {
        return 'Nomor HP tidak valid (9–15 karakter: angka, spasi, + atau -).';
    }
    return '';
}

function validasi_password(string $password): string
{
    if (strlen($password) < 8) {
        return 'Kata sandi minimal 8 karakter.';
    }
    if (strlen($password) > 72) {   // bcrypt hanya membaca 72 byte pertama
        return 'Kata sandi maksimal 72 karakter.';
    }
    return '';
}

/** Tanggal lahir opsional, format YYYY-MM-DD, tidak boleh di masa depan. */
function validasi_tanggal_lahir(string $tanggal): string
{
    if ($tanggal === '') {
        return '';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$dt || $dt->format('Y-m-d') !== $tanggal || $dt > new DateTime('today')) {
        return 'Tanggal lahir tidak valid.';
    }
    return '';
}
