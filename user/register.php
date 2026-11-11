<?php
// Titik masuk untuk halaman Daftar. Logika ada di AuthController, tampilan di views/auth/register.php.
require '../includes/session.php';
require '../config/koneksi.php';
require '../controllers/AuthController.php';

$controller = new AuthController($pdo);
$controller->register();
