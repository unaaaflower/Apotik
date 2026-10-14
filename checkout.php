<?php
require '../includes/session.php';
require_login();
proses_ubah_alamat('checkout.php');
$alamatList = get_alamat();
$alamat = $alamatList['utama'];
$flash = ambil_flash_alamat();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi — KlikMedis</title>
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

        <!-- Content -->
        <main class="content">

        <div class="checkout-steps">
            <div class="step is-done">
                <span class="step-circle">✓</span> Keranjang
            </div>
            <span class="step-line"></span>
            <div class="step is-active">
                <span class="step-circle">2</span> Konfirmasi
            </div>
            <span class="step-line"></span>
            <div class="step">
                <span class="step-circle">3</span> Pembayaran
            </div>
        </div>

        <!-- Form pembayaran: field-nya ada di beberapa kartu, dihubungkan lewat atribut form="checkout-form" -->
        <form id="checkout-form" method="post" action="pembayaran.php"></form>

        <div class="checkout-grid" id="checkout-grid">

            <!-- Left: address + shipping + payment -->
            <div>
                <div class="card">
                    <div class="card-head">
                        <p class="card-title">Alamat Pengiriman</p>
                        <a href="#" class="btn-outline" data-addr-edit>Ubah</a>
                    </div>
                    <p data-addr-view style="margin:0; font-size:13px; color:var(--ink-soft); line-height:1.6;">
                        <strong style="color:var(--ink);"><?= e($alamat['nama']) ?></strong> · <?= e($alamat['telp']) ?><br>
                        <?= e($alamat['alamat']) ?>
                    </p>
                    <?php if ($flash && $flash['label'] === 'utama'): ?>
                        <div class="form-message<?= $flash['ok'] ? '' : ' is-error' ?>" role="status"><?= e($flash['pesan']) ?></div>
                    <?php endif; ?>
                    <form method="post" action="checkout.php" class="addr-form" data-addr-form <?= ($flash && !$flash['ok'] && $flash['label'] === 'utama') ? '' : 'hidden' ?>>
                        <input type="hidden" name="aksi" value="ubah_alamat">
                        <input type="hidden" name="label" value="utama">
                        <div class="field">
                            <label for="a-nama">Nama penerima</label>
                            <input type="text" id="a-nama" name="nama" value="<?= e($alamat['nama']) ?>" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label for="a-telp">No. HP</label>
                            <input type="tel" id="a-telp" name="telp" value="<?= e($alamat['telp']) ?>" maxlength="20" required>
                        </div>
                        <div class="field field-textarea">
                            <label for="a-alamat">Alamat lengkap</label>
                            <textarea id="a-alamat" name="alamat" rows="3" maxlength="300" required><?= e($alamat['alamat']) ?></textarea>
                        </div>
                        <div class="addr-actions">
                            <button type="submit" class="btn-primary" style="width:auto;">Simpan Alamat</button>
                            <button type="button" class="btn-outline" data-addr-cancel>Batal</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <p class="card-title">Metode Pengiriman</p>
                    <label class="shipping-option">
                        <input type="radio" name="shipping" value="10000" form="checkout-form" checked>
                        <div class="ship-info">
                            <strong>JNE Reguler (2-3 hari)</strong>
                            <span>Estimasi tiba 25–26 September</span>
                        </div>
                        <span class="ship-price">Rp 10.000</span>
                    </label>
                    <label class="shipping-option">
                        <input type="radio" name="shipping" value="15000" form="checkout-form">
                        <div class="ship-info">
                            <strong>JNE YES (1 hari)</strong>
                            <span>Estimasi tiba besok</span>
                        </div>
                        <span class="ship-price">Rp 15.000</span>
                    </label>
                </div>

                <div class="card">
                    <p class="card-title">Metode Pembayaran</p>
                    <label class="shipping-option">
                        <input type="radio" name="payment" value="transfer" form="checkout-form" checked>
                        <div class="ship-info">
                            <strong>Transfer Bank</strong>
                            <span>BCA, BNI, Mandiri, BRI</span>
                        </div>
                    </label>
                    <label class="shipping-option">
                        <input type="radio" name="payment" value="ewallet" form="checkout-form">
                        <div class="ship-info">
                            <strong>E-Wallet</strong>
                            <span>GoPay, OVO, DANA, ShopeePay</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Right: order summary -->
            <div>
                <div class="card">
                    <p class="card-title">Ringkasan Pesanan</p>

                    <div class="checkout-item">
                        <div class="item-thumb"></div>
                        <div class="item-info">
                            <strong>Paracetamol 500 mg</strong>
                            <span>10 Tablet × 1</span>
                        </div>
                        <div class="item-price">Rp 5.000</div>
                    </div>
                    <div class="checkout-item">
                        <div class="item-thumb"></div>
                        <div class="item-info">
                            <strong>Vitamin C 1000 mg</strong>
                            <span>10 Tablet × 1</span>
                        </div>
                        <div class="item-price">Rp 25.000</div>
                    </div>
                    <div class="checkout-item">
                        <div class="item-thumb"></div>
                        <div class="item-info">
                            <strong>Minyak Telon</strong>
                            <span>60 ml × 1</span>
                        </div>
                        <div class="item-price">Rp 18.000</div>
                    </div>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span id="sum-subtotal">Rp 48.000</span>
                    </div>
                    <div class="summary-line">
                        <span>Ongkos Kirim</span>
                        <span id="sum-shipping">Rp 10.000</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <span id="sum-total">Rp 58.000</span>
                    </div>

                    <button type="submit" class="btn-primary" id="pay-btn" form="checkout-form">Lanjut ke Pembayaran</button>
                    <div class="form-message" id="checkout-message" role="status" aria-live="polite"></div>
                </div>
            </div>

        </div>

    </main>

    </div>

<?php require '../includes/footer.php'; ?>

    <script src="site.js?v=<?= filemtime(__DIR__ . '/site.js') ?>"></script>
</body>

</html>
