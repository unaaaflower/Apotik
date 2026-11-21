<?php /** VIEW — halaman error 405. Dipanggil lewat abort(405). */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>405 — Metode tidak diizinkan</title>
</head>
<body style="font-family:sans-serif;text-align:center;padding:80px 20px">
    <h1>405 — Metode tidak diizinkan</h1>
    <p>Permintaan ini tidak didukung untuk halaman tersebut.</p>
    <p><a href="<?= e(url('/')) ?>">Kembali ke beranda</a></p>
</body>
</html>
