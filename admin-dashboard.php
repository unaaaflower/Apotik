<?php require '../includes/session.php'; require_admin(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — SehatKita</title>
    <link rel="stylesheet" href="admin-style.css">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <!-- Main -->
        <div>
            <header class="admin-topbar">
                <a href="admin-dashboard.php" class="admin-logo">
                    <span class="admin-logo-mark"><img src="logo.png" alt="Logo SehatKita"></span>
                    <span class="admin-logo-text">SehatKita<br><small>Admin Panel</small></span>
                </a>
                <span class="admin-topbar-divider" aria-hidden="true"></span>
                <div>
                    <div class="admin-page-title">Dashboard</div>
                    <div class="admin-breadcrumb">Ringkasan performa toko hari ini</div>
                </div>
                <div class="admin-topbar-actions">
                    <button type="button" class="admin-icon-btn admin-nav-toggle" id="admin-nav-toggle" aria-label="Buka menu" aria-haspopup="true" aria-expanded="false" aria-controls="admin-nav-menu">
                        <svg class="icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                        <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>
                    </button>
                    <nav class="admin-nav-menu" id="admin-nav-menu" role="menu" aria-label="Menu admin">
                    <div class="admin-account" role="presentation">
                        <div class="admin-avatar"><?= e(inisial()) ?></div>
                        <div>
                            <div class="admin-account-name"><?= e($_SESSION['nama']) ?></div>
                            <div class="admin-account-role">Super Admin</div>
                        </div>
                    </div>
                    <div class="admin-nav-divider"></div>
                    <p class="admin-nav-title">Menu Utama</p>
                    <a href="admin-dashboard.php" class="admin-nav-link is-active" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="3" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/><rect x="13" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/></svg>
                        Dashboard
                    </a>
                    <a href="admin-obat.php" class="admin-nav-link" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        Kelola Obat
                    </a>
                    <a href="admin-promo.php" class="admin-nav-link" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M20.6 12.3 12.3 20.6a2 2 0 0 1-2.8 0l-6-6a2 2 0 0 1 0-2.9L11.8 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v6.1c0 .5-.2 1-.6 1.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="15.5" cy="8.5" r="1.6" stroke="currentColor" stroke-width="1.7"/></svg>
                        Kelola Promo
                    </a>
                    <p class="admin-nav-title">Lainnya</p>
                    <a href="#" class="admin-nav-link" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.7"/><path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        Pengguna
                    </a>
                    <a href="#" class="admin-nav-link" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Pesanan
                    </a>
                    <div class="admin-nav-divider"></div>
                    <a href="../user/logout.php" class="admin-nav-link is-danger" role="menuitem">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9M13 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Keluar
                    </a>
                    </nav>
                </div>
            </header>

            <main class="admin-main">

                <div class="admin-page-heading">
                    <div>
                        <h1>Selamat datang kembali, Admin 👋</h1>
                        <p>Berikut ringkasan aktivitas apotek SehatKita hari ini.</p>
                    </div>
                    <a href="admin-obat.php" class="admin-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                        Tambah Obat
                    </a>
                </div>

                <div class="admin-stat-grid">
                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Total Obat</div>
                            <div class="stat-value">184</div>
                            <div class="stat-delta up">+6 minggu ini</div>
                        </div>
                        <span class="admin-stat-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        </span>
                    </div>
                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Stok Menipis</div>
                            <div class="stat-value">7</div>
                            <div class="stat-delta down">Perlu direstok</div>
                        </div>
                        <span class="admin-stat-icon danger">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M10.3 3.9 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Promo Aktif</div>
                            <div class="stat-value">5</div>
                            <div class="stat-delta up">2 berakhir minggu ini</div>
                        </div>
                        <span class="admin-stat-icon gold">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M20.6 12.3 12.3 20.6a2 2 0 0 1-2.8 0l-6-6a2 2 0 0 1 0-2.9L11.8 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v6.1c0 .5-.2 1-.6 1.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Pesanan Hari Ini</div>
                            <div class="stat-value">32</div>
                            <div class="stat-delta up">+12% dari kemarin</div>
                        </div>
                        <span class="admin-stat-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                </div>

                <div class="admin-grid-2">

                    <div class="admin-card">
                        <p class="admin-card-title">Obat dengan Stok Menipis</p>
                        <p class="admin-card-subtitle">Segera lakukan restok agar tidak kehabisan.</p>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="admin-cell-product">
                                                <span class="admin-cell-thumb">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>
                                                </span>
                                                <div><strong>Cetirizine 10 mg</strong><span>10 Tablet/strip</span></div>
                                            </div>
                                        </td>
                                        <td>Obat Bebas</td>
                                        <td>4</td>
                                        <td><span class="badge danger">Stok Kritis</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="admin-cell-product">
                                                <span class="admin-cell-thumb">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 3h6v5l3 4v6a3 3 0 0 1-3 3H9a3 3 0 0 1-3-3v-6l3-4V3Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                                                </span>
                                                <div><strong>Vitamin C 1000 mg</strong><span>10 Tablet/botol</span></div>
                                            </div>
                                        </td>
                                        <td>Vitamin</td>
                                        <td>9</td>
                                        <td><span class="badge warning">Menipis</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="admin-cell-product">
                                                <span class="admin-cell-thumb">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="4" y="6" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.6"/></svg>
                                                </span>
                                                <div><strong>Antangin JRG</strong><span>12 Sachet/box</span></div>
                                            </div>
                                        </td>
                                        <td>Obat Bebas</td>
                                        <td>6</td>
                                        <td><span class="badge warning">Menipis</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="admin-obat.php" class="admin-btn-outline" style="margin-top:14px;">Kelola Semua Obat</a>
                    </div>

                    <div class="admin-card">
                        <p class="admin-card-title">Promo Berjalan</p>
                        <p class="admin-card-subtitle">Promo yang sedang aktif untuk pengguna.</p>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Promo</th>
                                        <th>Diskon</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Diskon Vitamin</strong></td>
                                        <td>30%</td>
                                        <td><span class="badge success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gratis Ongkir</strong></td>
                                        <td>Rp 10.000</td>
                                        <td><span class="badge success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Member Baru</strong></td>
                                        <td>15%</td>
                                        <td><span class="badge warning">Segera Berakhir</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a href="admin-promo.php" class="admin-btn-outline" style="margin-top:14px;">Kelola Semua Promo</a>
                    </div>

                </div>

            </main>
        </div>

    </div>

    <script src="admin.js"></script>
</body>

</html>
