<?php /** VIEW — halaman error 404. Dipanggil lewat abort(404). */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Halaman tidak ditemukan</title>
</head>
<body style="font-family:sans-serif;text-align:center;padding:80px 20px">
    <h1>404 — Halaman tidak ditemukan</h1>
    <p>Alamat yang kamu buka tidak tersedia.</p>
    <p><a href="<?= e(url('/')) ?>">Kembali ke beranda</a></p>
</body>
</html>
