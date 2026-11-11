<?php
// Titik masuk untuk halaman Profil. Logika ada di ProfilController, tampilan di views/user/profil.php.
require '../includes/session.php';
require_login();
require '../config/koneksi.php';
require '../controllers/ProfilController.php';

$controller = new ProfilController($pdo);
$controller->invoke();
