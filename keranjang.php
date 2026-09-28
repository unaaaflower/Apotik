<?php require '../includes/session.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang — SehatKita</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="shop-body">

    <header class="topbar">
        <a href="dashboard.php" class="logo">
            <span class="logo-mark"><img src="logo.png" alt="Logo SehatKita"></span>
            <span class="logo-text logo-text-dark">SehatKita<br><small>Apotek Online</small></span>
        </a>

        <div class="topbar-search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input type="text" placeholder="Cari obat, vitamin, atau produk kesehatan...">
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
            <div class="step is-active">
                <span class="step-circle">1</span> Keranjang
            </div>
            <span class="step-line"></span>
            <div class="step">
                <span class="step-circle">2</span> Pembayaran
            </div>
            <span class="step-line"></span>
            <div class="step">
                <span class="step-circle">3</span> Konfirmasi
            </div>
        </div>

        <div class="checkout-grid">

            <!-- Left: cart items -->
            <div>
                <div class="card">
                    <div class="page-heading" style="margin-bottom:10px;">
                        <p class="card-title" style="margin:0;">Item di Keranjang</p>
                        <a href="dashboard.php" class="btn-outline">+ Tambah Produk</a>
                    </div>

                    <div class="cart-note">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M4 10h16" stroke="currentColor" stroke-width="1.6"/></svg>
                        Ubah jumlah atau hapus produk sebelum lanjut ke pembayaran.
                    </div>

                    <div id="cart-items">
                        <div class="checkout-item" data-cart-item data-price="5000">
                            <div class="item-thumb"></div>
                            <div class="item-info">
                                <strong>Paracetamol 500 mg</strong>
                                <span>10 Tablet · Rp 5.000 / strip</span>
                            </div>
                            <div class="item-controls">
                                <div class="qty-stepper">
                                    <button type="button" data-qty-minus aria-label="Kurangi jumlah">−</button>
                                    <span data-qty-value>1</span>
                                    <button type="button" data-qty-plus aria-label="Tambah jumlah">+</button>
                                </div>
                                <div class="item-price" data-item-total>Rp 5.000</div>
                                <button type="button" class="item-remove" data-cart-remove aria-label="Hapus produk">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="checkout-item" data-cart-item data-price="25000">
                            <div class="item-thumb"></div>
                            <div class="item-info">
                                <strong>Vitamin C 1000 mg</strong>
                                <span>10 Tablet · Rp 25.000 / botol</span>
                            </div>
                            <div class="item-controls">
                                <div class="qty-stepper">
                                    <button type="button" data-qty-minus aria-label="Kurangi jumlah">−</button>
                                    <span data-qty-value>1</span>
                                    <button type="button" data-qty-plus aria-label="Tambah jumlah">+</button>
                                </div>
                                <div class="item-price" data-item-total>Rp 25.000</div>
                                <button type="button" class="item-remove" data-cart-remove aria-label="Hapus produk">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="checkout-item" data-cart-item data-price="18000">
                            <div class="item-thumb"></div>
                            <div class="item-info">
                                <strong>Minyak Telon</strong>
                                <span>60 ml · Rp 18.000 / botol</span>
                            </div>
                            <div class="item-controls">
                                <div class="qty-stepper">
                                    <button type="button" data-qty-minus aria-label="Kurangi jumlah">−</button>
                                    <span data-qty-value>1</span>
                                    <button type="button" data-qty-plus aria-label="Tambah jumlah">+</button>
                                </div>
                                <div class="item-price" data-item-total>Rp 18.000</div>
                                <button type="button" class="item-remove" data-cart-remove aria-label="Hapus produk">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state, shown by JS when cart is emptied -->
                    <div class="empty-state" id="cart-empty" style="display:none;">
                        <span class="empty-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <h3>Keranjang kamu kosong</h3>
                        <p>Yuk cari obat atau produk kesehatan yang kamu butuhkan.</p>
                        <a href="dashboard.php" class="btn-outline" style="margin-top:6px;">Mulai Belanja</a>
                    </div>
                </div>
            </div>

            <!-- Right: order summary -->
            <div>
                <div class="card">
                    <p class="card-title">Ringkasan Belanja</p>

                    <div class="summary-line">
                        <span>Subtotal (<span id="sum-count">3</span> produk)</span>
                        <span id="sum-subtotal">Rp 48.000</span>
                    </div>
                    <div class="summary-line">
                        <span>Estimasi Ongkos Kirim</span>
                        <span>Rp 10.000</span>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <span id="sum-total">Rp 58.000</span>
                    </div>

                    <a href="checkout.php" class="btn-primary" style="display:block; text-align:center; text-decoration:none;" id="cart-checkout-btn">Lanjut ke Pembayaran</a>
                </div>
            </div>

        </div>

    </main>

    </div>

    <script src="site.js"></script>
</body>

</html>
