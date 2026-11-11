<?php
// Titik masuk (front controller) untuk halaman Masuk. Logika ada di AuthController, tampilan di views/auth/login.php.
require '../includes/session.php';
require '../config/koneksi.php';
require '../controllers/AuthController.php';

$controller = new AuthController($pdo);
$controller->login();
