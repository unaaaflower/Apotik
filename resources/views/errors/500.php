<?php /** VIEW — halaman error 500. Dipanggil lewat abort(500). */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Kesalahan server</title>
</head>
<body style="font-family:sans-serif;text-align:center;padding:80px 20px">
    <h1>500 — Kesalahan server</h1>
    <p>Terjadi kesalahan pada server. Silakan coba lagi nanti.</p>
    <p><a href="<?= e(url('/')) ?>">Kembali ke beranda</a></p>
</body>
</html>
