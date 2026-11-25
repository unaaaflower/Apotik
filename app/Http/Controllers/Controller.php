<?php

namespace App\Http\Controllers;

use App\Support\Database;
use PDO;
use PDOException;

/**
 * Controller dasar: render view, ambil input POST, dan koneksi database.
 * Controller turunan yang memakai database memanggil $this->connect() di constructor.
 */
abstract class Controller
{
    protected const PESAN_SERVER = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';

    protected PDO $pdo;

    /** Ambil koneksi database bersama (App\Support\Database). */
    protected function connect(): void
    {
        $this->pdo = Database::connection();
    }

    /** Tampilkan file di resources/views/ dengan variabel dari $data. */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require BASE_PATH . '/resources/views/' . $view . '.php';
    }

    /** Ambil nilai POST sebagai string (array/tidak ada -> ''), sudah di-trim. */
    protected function post(string $kunci): string
    {
        $v = $_POST[$kunci] ?? '';
        return is_string($v) ? trim($v) : '';
    }

    /** Catat error teknis ke log server; pengguna hanya melihat pesan umum. */
    protected function catat(PDOException $e): void
    {
        error_log($e->getMessage());
    }

    /** Error database: catat ke log, tampilkan halaman 500 (pengguna tidak melihat detail teknis). */
    protected function gagalServer(PDOException $e): void
    {
        $this->catat($e);
        abort(500, self::PESAN_SERVER);
    }
}
