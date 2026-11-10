<?php require '../includes/session.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — KlikMedis</title>
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
            <a href="keranjang.php" class="icon-btn" aria-label="Keranjang">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="21" r="1.4" fill="currentColor"/><circle cx="17" cy="21" r="1.4" fill="currentColor"/></svg>
                <span class="badge-count">3</span>
            </a>
            <button type="button" class="icon-btn nav-toggle" id="nav-toggle" aria-label="Buka menu" aria-haspopup="true" aria-expanded="false" aria-controls="nav-menu">
                <svg class="icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
            </button>

            <nav class="nav-menu" id="nav-menu" role="menu" aria-label="Menu akun">
                <p class="nav-menu-title">Akun Saya</p>
                    <a href="dashboard.php" class="nav-menu-link is-active" role="menuitem">

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

            <div class="page-heading">
                <div>
                    <h1>Selamat datang, <?= e(nama_depan()) ?> 👋</h1>
                    <p>Yuk cek promo hari ini dan lengkapi kebutuhan kesehatanmu.</p>
                </div>
            </div>

            <div class="promo-banner">
                <div>
                    <h3>Diskon Spesial Produk Vitamin</h3>
                    <p>Jaga daya tahan tubuh, mulai dari sekarang! Diskon hingga 30%.</p>
                </div>
            </div>

            <div class="section-heading">
                <h2>Semua Produk</h2>
            </div>

            <div class="product-grid">

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Paracetamol 500 mg</div>
                    <div class="product-meta">10 Tablet</div>
                    <div class="price-row">
                        <div class="product-price">Rp 5.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Vitamin C 1000 mg</div>
                    <div class="product-meta">10 Tablet</div>
                    <div class="price-row">
                        <div class="product-price">Rp 25.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Minyak Telon</div>
                    <div class="product-meta">60 ml</div>
                    <div class="price-row">
                        <div class="product-price">Rp 18.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Antangin JRG</div>
                    <div class="product-meta">12 Sachet</div>
                    <div class="price-row">
                        <div class="product-price">Rp 15.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Cetirizine 10 mg</div>
                    <div class="product-meta">10 Tablet</div>
                    <div class="price-row">
                        <div class="product-price">Rp 12.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

                <div class="product-card">
                    <div class="product-thumb"></div>
                    <div class="product-name">Hansaplast Plester</div>
                    <div class="product-meta">20 pcs</div>
                    <div class="price-row">
                        <div class="product-price">Rp 28.000</div>
                        <button class="fav-btn" type="button" aria-label="Tambah ke favorit" aria-pressed="false" data-fav-toggle><svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></button>
                    </div>
                    <button class="btn-add" type="button">+ Keranjang</button>
                </div>

            </div>

        </main>
    </div>

<?php require '../includes/footer.php'; ?>

    <script src="site.js?v=<?= filemtime(__DIR__ . '/site.js') ?>"></script>
</body>

</html>