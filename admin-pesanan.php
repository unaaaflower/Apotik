<?php require '../includes/session.php'; require_admin(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan — Admin KlikMedis</title>
    <link rel="stylesheet" href="admin-style.css?v=<?= filemtime(__DIR__ . '/admin-style.css') ?>">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <!-- Main -->
        <div>
            <header class="admin-topbar">
                <a href="admin-dashboard.php" class="admin-logo">
                    <span class="admin-logo-mark"><img src="logo.png" alt="Logo KlikMedis"></span>
                    <span class="admin-logo-text">KlikMedis<br><small>Admin Panel</small></span>
                </a>
                <span class="admin-topbar-divider" aria-hidden="true"></span>
                <div>
                    <div class="admin-page-title">Kelola Pesanan</div>
                    <div class="admin-breadcrumb">Dashboard / Kelola Pesanan</div>
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
                    <a href="admin-dashboard.php" class="admin-nav-link" role="menuitem">
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
                    <a href="admin-pesanan.php" class="admin-nav-link is-active" role="menuitem">
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
                        <h1>Daftar Pesanan</h1>
                        <p>Tambah, lihat, ubah status, atau hapus pesanan pelanggan.</p>
                    </div>
                </div>

                <div class="admin-card">

                    <div class="admin-toolbar">
                        <div class="admin-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            <input type="text" id="pesanan-search" placeholder="Cari kode atau nama pelanggan...">
                        </div>
                        <select class="admin-filter" id="pesanan-filter-status">
                            <option value="">Semua Status</option>
                            <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Dikirim">Dikirim</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                        <button type="button" class="admin-btn-primary" id="btn-tambah-pesanan">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                            Tambah Pesanan
                        </button>
                    </div>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="pesanan-tbody">
                                <!-- Rows are rendered by admin.js -->
                            </tbody>
                        </table>
                    </div>

                </div>

            </main>
        </div>

    </div>

    <!-- Modal: Tambah / Ubah Pesanan -->
    <div class="admin-modal-overlay" id="pesanan-modal-overlay">
        <div class="admin-modal">
            <div class="admin-modal-head">
                <div>
                    <h2 id="pesanan-modal-title">Tambah Pesanan</h2>
                    <p>Lengkapi detail pesanan di bawah ini.</p>
                </div>
                <button type="button" class="admin-modal-close" id="pesanan-modal-close" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>

            <form id="pesanan-form">
                <input type="hidden" id="pesanan-id">

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="pesanan-kode">Kode Pesanan</label>
                        <input type="text" id="pesanan-kode" placeholder="Otomatis jika dikosongkan">
                    </div>
                    <div class="admin-field">
                        <label for="pesanan-tanggal">Tanggal</label>
                        <input type="date" id="pesanan-tanggal" required>
                    </div>
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="pesanan-pelanggan">Nama Pelanggan</label>
                        <input type="text" id="pesanan-pelanggan" placeholder="Contoh: Andi Pratama" required>
                    </div>
                    <div class="admin-field">
                        <label for="pesanan-email">Email</label>
                        <input type="text" id="pesanan-email" placeholder="andi@mail.com">
                    </div>
                </div>

                <div class="admin-field">
                    <label for="pesanan-produk">Produk</label>
                    <textarea id="pesanan-produk" placeholder="Satu produk per baris, contoh: Paracetamol 500 mg x2" required></textarea>
                </div>

                <div class="admin-field">
                    <label for="pesanan-alamat">Alamat Pengiriman</label>
                    <textarea id="pesanan-alamat" placeholder="Alamat lengkap penerima"></textarea>
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="pesanan-total">Total (Rp)</label>
                        <input type="number" id="pesanan-total" min="0" placeholder="0" required>
                    </div>
                    <div class="admin-field">
                        <label for="pesanan-pembayaran">Metode Pembayaran</label>
                        <select id="pesanan-pembayaran">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="COD">COD</option>
                            <option value="Kartu Kredit">Kartu Kredit</option>
                        </select>
                    </div>
                </div>

                <div class="admin-field">
                    <label for="pesanan-status">Status Pesanan</label>
                    <select id="pesanan-status">
                        <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Dikirim">Dikirim</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="admin-modal-actions">
                    <button type="button" class="admin-btn-ghost" id="pesanan-modal-cancel">Batal</button>
                    <button type="submit" class="admin-btn-primary" id="pesanan-modal-submit">Simpan Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Detail Pesanan -->
    <div class="admin-modal-overlay" id="pesanan-detail-overlay">
        <div class="admin-modal">
            <div class="admin-modal-head">
                <div>
                    <h2 id="pesanan-detail-title">Detail Pesanan</h2>
                    <p id="pesanan-detail-sub"></p>
                </div>
                <button type="button" class="admin-modal-close" id="pesanan-detail-close" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>
            <div id="pesanan-detail-body" class="admin-detail-list"></div>
            <div class="admin-modal-actions">
                <button type="button" class="admin-btn-ghost" id="pesanan-detail-tutup">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Toast feedback -->
    <div class="admin-toast" id="admin-toast" role="status" aria-live="polite"></div>

    <script src="admin.js"></script>
</body>

</html>
