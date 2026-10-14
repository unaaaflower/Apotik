<?php
require '../includes/session.php';
require_login();

$subtotal  = array_sum(array_column(checkout_items(), 'harga'));
$opsiKirim = opsi_pengiriman();

/* ---- 1) Datang dari checkout.php / langkah Konfirmasi ("Lanjut ke Pembayaran"): simpan pesanan sementara, lalu tampilkan instruksi ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['aksi'])) {
    $metode = $_POST['payment'] ?? '';
    $ongkir = (int) ($_POST['shipping'] ?? 0);

    if (!in_array($metode, ['transfer', 'ewallet'], true) || !isset($opsiKirim[$ongkir])) {
        redirect_to('checkout.php'); // data tidak valid -> kembali ke checkout
    }

    $alamatList = get_alamat();
    $_SESSION['pesanan_pending'] = [
        'id'       => 'SK-' . random_int(20900, 99999),
        'metode'   => $metode,
        'ongkir'   => $ongkir,
        'kurir'    => $opsiKirim[$ongkir],
        'subtotal' => $subtotal,
        'total'    => $subtotal + $ongkir,   // total dihitung ulang di server, bukan dari input user
        'jumlah'   => count(checkout_items()),
        'alamat'   => $alamatList['utama'],
        'tanggal'  => tanggal_id(),
        'status'   => 'Menunggu Pembayaran',
    ];
    redirect_to('pembayaran.php'); // Post/Redirect/Get: refresh tidak membuat pesanan baru
}

/* ---- 2) "Saya Sudah Bayar": pesanan masuk ke riwayat dengan status menunggu verifikasi ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'konfirmasi') {
    $pending = $_SESSION['pesanan_pending'] ?? null;
    if ($pending) {
        $pending['status'] = 'Menunggu Verifikasi';
        $_SESSION['pesanan'] = array_merge([$pending], $_SESSION['pesanan'] ?? []);
        unset($_SESSION['pesanan_pending']);
    }
    redirect_to('profil.php#tab-pesanan');
}

/* ---- 3) Tampilkan halaman pembayaran ---- */
$order = $_SESSION['pesanan_pending'] ?? null;
if (!$order) {
    redirect_to('checkout.php'); // tidak ada pesanan yang menunggu dibayar
}

$bank = [
    ['nama' => 'BCA',     'rek' => '1234 5678 90',     'an' => 'KlikMedis Apotek Online'],
    ['nama' => 'BNI',     'rek' => '0987 6543 21',     'an' => 'KlikMedis Apotek Online'],
    ['nama' => 'Mandiri', 'rek' => '1400 0123 4567 8', 'an' => 'KlikMedis Apotek Online'],
    ['nama' => 'BRI',     'rek' => '0123 01 000123 30 4', 'an' => 'KlikMedis Apotek Online'],
];

/** QR simulasi (bukan QR asli) — ganti dengan QR dari payment gateway saat sudah terhubung. */
function qr_simulasi(string $seed): string
{
    $n = 25;
    $state = crc32($seed);
    $svg = '<svg viewBox="0 0 ' . $n . ' ' . $n . '" width="200" height="200" shape-rendering="crispEdges" role="img" aria-label="QR pembayaran (simulasi)">'
         . '<rect width="' . $n . '" height="' . $n . '" fill="#fff"/>';
    $finder = function (int $x, int $y) {
        return '<rect x="' . $x . '" y="' . $y . '" width="7" height="7" fill="#1C2620"/>'
             . '<rect x="' . ($x + 1) . '" y="' . ($y + 1) . '" width="5" height="5" fill="#fff"/>'
             . '<rect x="' . ($x + 2) . '" y="' . ($y + 2) . '" width="3" height="3" fill="#1C2620"/>';
    };
    for ($y = 0; $y < $n; $y++) {
        for ($x = 0; $x < $n; $x++) {
            $inFinder = ($x < 8 && $y < 8) || ($x >= $n - 8 && $y < 8) || ($x < 8 && $y >= $n - 8);
            $state = ($state * 1103515245 + 12345) & 0x7fffffff;
            if (!$inFinder && ($state >> 16) % 2 === 0) {
                $svg .= '<rect x="' . $x . '" y="' . $y . '" width="1" height="1" fill="#1C2620"/>';
            }
        }
    }
    return $svg . $finder(0, 0) . $finder($n - 7, 0) . $finder(0, $n - 7) . '</svg>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — KlikMedis</title>
    <link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__ . '/style.css') ?>">
</head>

<body class="shop-body">

    <header class="topbar">
        <a href="dashboard.php" class="logo">
            <span class="logo-mark"><img src="logo.png" alt="Logo KlikMedis"></span>
            <span class="logo-text logo-text-dark">KlikMedis<br><small>Apotek Online</small></span>
        </a>

        <div class="topbar-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input type="text" id="site-search" name="q" placeholder="Cari obat, vitamin, atau produk kesehatan..." aria-label="Cari produk" autocomplete="off">
        </div>

        <div class="topbar-actions">
            <a href="keranjang.php" class="icon-btn is-active" aria-label="Keranjang">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="21" r="1.4" fill="currentColor"/><circle cx="17" cy="21" r="1.4" fill="currentColor"/></svg>
                <span class="badge-count">3</span>
            </a>
            <button type="button" class="icon-btn nav-toggle" id="nav-toggle" aria-label="Buka menu" aria-haspopup="true" aria-expanded="false" aria-controls="nav-menu">
                <svg class="icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
            </button>

            <nav class="nav-menu" id="nav-menu" role="menu" aria-label="Menu akun">
                <p class="nav-menu-title">Akun Saya</p>
                    <a href="dashboard.php" class="nav-menu-link" role="menuitem">

                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="3" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/></svg>

                        Beranda

                    </a>

                    <a href="favorit.php" class="nav-menu-link" role="menuitem">

                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.7"/></svg>

                        Produk Favorit

                    </a>

                    <a href="pengingat-obat.php" class="nav-menu-link" role="menuitem">

                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="13" r="8" stroke="currentColor" stroke-width="1.7"/><path d="M12 9v4l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M9 2h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>

                        Pengingat Obat

                    </a>

                    <a href="profil.php" class="nav-menu-link" role="menuitem">

                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.7"/><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>

                        Profil Saya

                    </a>

                    <a href="logout.php" class="nav-menu-link is-danger" role="menuitem">

                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9M13 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>

                        Keluar

                    </a>

            </nav>
        </div>
    </header>

    <div class="shell">
        <main class="content">

        <div class="checkout-steps">
            <div class="step is-done">
                <span class="step-circle">✓</span> Keranjang
            </div>
            <span class="step-line"></span>
            <div class="step is-done">
                <span class="step-circle">✓</span> Konfirmasi
            </div>
            <span class="step-line"></span>
            <div class="step is-active">
                <span class="step-circle">3</span> Pembayaran
            </div>
        </div>

        <div class="checkout-grid">

            <!-- Kiri: instruksi pembayaran sesuai metode yang dipilih -->
            <div>
                <div class="card">
                    <p class="card-title">Selesaikan Pembayaran</p>
                    <p class="card-subtitle">Pesanan #<?= e($order['id']) ?> · status: <span class="status-pill pending"><?= e($order['status']) ?></span></p>

                    <div class="pay-total">
                        <span>Total pembayaran</span>
                        <strong><?= e(rupiah($order['total'])) ?></strong>
                    </div>

                    <?php if ($order['metode'] === 'transfer'): ?>

                        <p class="pay-heading">Transfer Bank</p>
                        <p class="card-subtitle">Transfer tepat sesuai total ke salah satu rekening berikut.</p>
                        <?php foreach ($bank as $b): ?>
                            <div class="bank-item">
                                <div>
                                    <strong><?= e($b['nama']) ?></strong>
                                    <span>a.n. <?= e($b['an']) ?></span>
                                </div>
                                <div class="bank-rek"><?= e($b['rek']) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <p class="pay-note">Nomor rekening di atas masih contoh. Ganti dengan rekening toko yang sebenarnya di <code>pembayaran.php</code>.</p>

                    <?php else: ?>

                        <p class="pay-heading">E-Wallet</p>
                        <p class="card-subtitle">Scan QR di bawah dengan GoPay, OVO, DANA, atau ShopeePay, lalu selesaikan pembayaran.</p>
                        <div class="qr-box">
                            <?= qr_simulasi($order['id']) ?>
                            <span>QR simulasi · <?= e(rupiah($order['total'])) ?></span>
                        </div>
                        <p class="pay-note">QR ini hanya tampilan contoh (tidak bisa di-scan). Ganti dengan QR dari payment gateway saat sudah terhubung.</p>

                    <?php endif; ?>

                    <form method="post" action="pembayaran.php" class="pay-form">
                        <input type="hidden" name="aksi" value="konfirmasi">
                        <button type="submit" class="btn-primary">Saya Sudah Bayar</button>
                    </form>
                    <p class="pay-back-wrap"><a href="checkout.php" class="pay-back">← Ganti metode / kembali ke konfirmasi</a></p>
                </div>
            </div>

            <!-- Kanan: ringkasan pesanan -->
            <div>
                <div class="card">
                    <p class="card-title">Ringkasan Pesanan</p>

                    <?php foreach (checkout_items() as $it): ?>
                        <div class="checkout-item">
                            <div class="item-thumb"></div>
                            <div class="item-info">
                                <strong><?= e($it['nama']) ?></strong>
                                <span><?= e($it['ket']) ?></span>
                            </div>
                            <div class="item-price"><?= e(rupiah($it['harga'])) ?></div>
                        </div>
                    <?php endforeach; ?>

                    <div class="summary-line"><span>Subtotal</span><span><?= e(rupiah($order['subtotal'])) ?></span></div>
                    <div class="summary-line"><span>Ongkos Kirim (<?= e($order['kurir']) ?>)</span><span><?= e(rupiah($order['ongkir'])) ?></span></div>
                    <div class="summary-line total"><span>Total</span><span><?= e(rupiah($order['total'])) ?></span></div>

                    <p class="pay-note">
                        Dikirim ke: <strong><?= e($order['alamat']['nama']) ?></strong> · <?= e($order['alamat']['telp']) ?><br>
                        <?= e($order['alamat']['alamat']) ?>
                    </p>
                </div>
            </div>

        </div>

        </main>
    </div>

<?php require '../includes/footer.php'; ?>

    <script src="site.js?v=<?= filemtime(__DIR__ . '/site.js') ?>"></script>
</body>

</html>
