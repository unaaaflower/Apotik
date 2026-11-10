<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../includes/validasi.php';

/**
 * Controller dasar. Controller turunan (AuthController, ProfilController)
 * mewarisi koneksi, model User, dan method render().
 */
abstract class Controller
{
    protected PDO $pdo;
    protected User $user;

    public function __construct(PDO $pdo)
    {
        $this->pdo  = $pdo;
        $this->user = new User($pdo);
    }

    /** Tampilkan file di folder views/ dengan variabel dari $data. */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require dirname(__DIR__) . '/views/' . $view . '.php';
    }

    /** Catat error teknis ke log server; pengguna hanya melihat pesan umum. */
    protected function catat(PDOException $e): void
    {
        error_log($e->getMessage());
    }
}
