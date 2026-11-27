<?php

namespace App\Support;

use PDO;
use PDOException;

/**
 * Koneksi database (PDO) — satu koneksi dipakai bersama. Pengaturan dari .env lewat config/database.php.
 */
final class Database
{
    private static ?PDO $pdo = null;

    /**
     * @param bool $abortJikaGagal true: tampilkan halaman 500 bila gagal (bawaan);
     *                             false: lempar PDOException agar pemanggil bisa menanganinya sendiri.
     */
    public static function connection(bool $abortJikaGagal = true): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $c   = config('database');
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $c['host'], $c['port'], $c['database'], $c['charset']);

        try {
            self::$pdo = new PDO($dsn, $c['username'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (!$abortJikaGagal) {
                throw $e;
            }
            error_log($e->getMessage());
            abort(500, 'Koneksi basis data gagal.');
        }

        return self::$pdo;
    }
}
