<?php require '../includes/session.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — SehatKita</title>
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
                    <a href="profil.php" class="nav-menu-link is-active" role="menuitem">
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

            <div class="breadcrumb"><a href="dashboard.php">Beranda</a> &nbsp;/&nbsp; Profil Saya</div>

            <nav class="content-tabs" aria-label="Bagian akun">
                <a href="#profil" class="content-tab is-active" data-tab-target="tab-profil">Profil</a>
                <a href="#pesanan" class="content-tab" data-tab-target="tab-pesanan">Pesanan Saya</a>
                <a href="#alamat" class="content-tab" data-tab-target="tab-alamat">Alamat</a>
            </nav>

            <!-- TAB: Profil -->
            <section id="tab-profil" class="account-tab is-active">
                <div class="card">
                    <div class="profile-header">
                        <div class="avatar"><?= e(inisial()) ?></div>
                        <div>
                            <h2><?= e($_SESSION['nama']) ?></h2>
                            <p>Anggota sejak Januari 2024 · <?= e($_SESSION['email']) ?></p>
                        </div>
                    </div>

                    <form class="form-grid" novalidate>
                        <div class="field">
                            <label for="p-name">Nama lengkap</label>
                            <input type="text" id="p-name" value="<?= e($_SESSION['nama']) ?>">
                        </div>
                        <div class="field">
                            <label for="p-email">Email</label>
                            <input type="email" id="p-email" value="<?= e($_SESSION['email']) ?>">
                        </div>
                        <div class="field">
                            <label for="p-phone">No. HP</label>
                            <input type="tel" id="p-phone" value="0812 3456 7890">
                        </div>
                        <div class="field">
                            <label for="p-dob">Tanggal lahir</label>
                            <input type="date" id="p-dob" value="1996-05-14">
                        </div>
                        <div class="field full">
                            <button type="submit" class="btn-primary" style="width:auto; padding:12px 26px;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB: Pesanan -->
            <section id="tab-pesanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Riwayat Pesanan</p>
                    <p class="card-subtitle">Pantau status pesanan obat dan produk kesehatanmu.</p>

                    <div class="order-row">
                        <div class="order-thumb"></div>
                        <div class="order-info">
                            <strong>#SK-20841 · 3 produk</strong>
                            <span>24 September 2026 · Rp 48.000</span>
                        </div>
                        <span class="status-pill success">Selesai</span>
                    </div>
                    <div class="order-row">
                        <div class="order-thumb"></div>
                        <div class="order-info">
                            <strong>#SK-20799 · 1 produk</strong>
                            <span>18 September 2026 · Rp 25.000</span>
                        </div>
                        <span class="status-pill pending">Dikirim</span>
                    </div>
                    <div class="order-row">
                        <div class="order-thumb"></div>
                        <div class="order-info">
                            <strong>#SK-20612 · 2 produk</strong>
                            <span>2 September 2026 · Rp 33.000</span>
                        </div>
                        <span class="status-pill success">Selesai</span>
                    </div>
                </div>
            </section>

            <!-- TAB: Alamat -->
            <section id="tab-alamat" class="account-tab">
                <div class="card">
                    <p class="card-title">Alamat Tersimpan</p>
                    <p class="card-subtitle">Digunakan sebagai alamat pengiriman saat checkout.</p>

                    <div class="address-item">
                        <div>
                            <span class="tag">Utama</span>
                            <p><strong><?= e($_SESSION['nama']) ?></strong> · 0812 3456 7890<br>
                            Jl. Melati No. 12, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
                        </div>
                        <a href="#" class="btn-outline">Ubah</a>
                    </div>
                    <div class="address-item">
                        <div>
                            <span class="tag">Kantor</span>
                            <p><strong><?= e($_SESSION['nama']) ?></strong> · 0812 3456 7890<br>
                            Jl. Ijen No. 45, Kec. Klojen, Kota Malang, Jawa Timur 65119</p>
                        </div>
                        <a href="#" class="btn-outline">Ubah</a>
                    </div>

                    <a href="#" class="btn-outline" style="margin-top:8px;">+ Tambah Alamat Baru</a>
                </div>
            </section>

        </main>
    </div>

    <script src="site.js"></script>
</body>

</html>