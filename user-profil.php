<?php

require '../includes/session.php';
require_login();
require '../config/koneksi.php';
require '../controllers/ProfilController.php';

$controller = new ProfilController($pdo);
$controller->invoke();
