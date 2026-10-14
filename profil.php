<?php
require '../includes/session.php';
require_login();
proses_ubah_alamat('profil.php#tab-alamat');
proses_foto_profil('profil.php');
$fotoUrl = foto_profil_url();
$flashFoto = ambil_flash_foto();
$alamatList = get_alamat();
$flash = ambil_flash_alamat();
$pesananBaru = $_SESSION['pesanan'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — KlikMedis</title>
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

            <!-- TAB: Profil -->
            <section id="tab-profil" class="account-tab is-active">
                <div class="card">
                    <div class="profile-header">
                        <div class="avatar<?= $fotoUrl ? ' has-photo' : '' ?>">
                            <?php if ($fotoUrl): ?>
                                <img src="<?= e($fotoUrl) ?>" alt="Foto profil <?= e($_SESSION['nama']) ?>" id="avatar-img">
                            <?php else: ?>
                                <span id="avatar-initial"><?= e(inisial()) ?></span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h2><?= e($_SESSION['nama']) ?></h2>
                            <p>Anggota sejak Januari 2024 · <?= e($_SESSION['email']) ?></p>
                        </div>
                    </div>

                    <form method="post" action="profil.php" enctype="multipart/form-data" class="photo-form" id="photo-form">
                        <input type="hidden" name="aksi" value="upload_foto">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" hidden>
                        <label for="foto" class="btn-outline">Pilih Foto</label>
                        <span class="photo-file-name" id="photo-file-name">JPG, PNG, atau WEBP · maks. 2 MB</span>
                        <button type="submit" class="btn-primary" id="photo-submit" style="width:auto;" disabled>Upload</button>
                        <?php if ($fotoUrl): ?>
                            <button type="submit" class="btn-outline" form="photo-delete-form">Hapus Foto</button>
                        <?php endif; ?>
                    </form>
                    <?php if ($fotoUrl): ?>
                    <form method="post" action="profil.php" id="photo-delete-form" hidden>
                        <input type="hidden" name="aksi" value="hapus_foto">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    </form>
                    <?php endif; ?>
                    <?php if ($flashFoto): ?>
                        <div class="form-message<?= $flashFoto['ok'] ? '' : ' is-error' ?>" role="status" style="text-align:left;"><?= e($flashFoto['pesan']) ?></div>
                    <?php endif; ?>

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
                            <button type="submit" class="btn-primary" style="width:auto;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB: Pesanan -->
            <section id="tab-pesanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Riwayat Pesanan</p>
                    <p class="card-subtitle">Pantau status pesanan obat dan produk kesehatanmu.</p>

                    <?php foreach ($pesananBaru as $ps): ?>
                    <div class="order-row">
                        <div class="order-thumb"></div>
                        <div class="order-info">
                            <strong>#<?= e($ps['id']) ?> · <?= (int) $ps['jumlah'] ?> produk</strong>
                            <span><?= e($ps['tanggal']) ?> · <?= e(rupiah((int) $ps['total'])) ?> · <?= $ps['metode'] === 'ewallet' ? 'E-Wallet' : 'Transfer Bank' ?></span>
                        </div>
                        <span class="status-pill pending"><?= e($ps['status']) ?></span>
                    </div>
                    <?php endforeach; ?>

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

                    <?php
                    $labelAlamat = ['utama' => 'Utama', 'kantor' => 'Kantor'];
                    foreach ($labelAlamat as $kunci => $namaLabel):
                        $al = $alamatList[$kunci];
                        $tampilForm = ($flash && !$flash['ok'] && $flash['label'] === $kunci);
                    ?>
                    <div class="address-item" data-addr-card>
                        <div style="flex:1; min-width:0;">
                            <span class="tag"><?= e($namaLabel) ?></span>
                            <p data-addr-view><strong><?= e($al['nama']) ?></strong> · <?= e($al['telp']) ?><br>
                            <?= e($al['alamat']) ?></p>
                            <?php if ($flash && $flash['label'] === $kunci): ?>
                                <div class="form-message<?= $flash['ok'] ? '' : ' is-error' ?>" role="status" style="text-align:left;"><?= e($flash['pesan']) ?></div>
                            <?php endif; ?>
                            <form method="post" action="profil.php" class="addr-form" data-addr-form <?= $tampilForm ? '' : 'hidden' ?>>
                                <input type="hidden" name="aksi" value="ubah_alamat">
                                <input type="hidden" name="label" value="<?= e($kunci) ?>">
                                <div class="field">
                                    <label for="a-nama-<?= e($kunci) ?>">Nama penerima</label>
                                    <input type="text" id="a-nama-<?= e($kunci) ?>" name="nama" value="<?= e($al['nama']) ?>" maxlength="100" required>
                                </div>
                                <div class="field">
                                    <label for="a-telp-<?= e($kunci) ?>">No. HP</label>
                                    <input type="tel" id="a-telp-<?= e($kunci) ?>" name="telp" value="<?= e($al['telp']) ?>" maxlength="20" required>
                                </div>
                                <div class="field field-textarea">
                                    <label for="a-alamat-<?= e($kunci) ?>">Alamat lengkap</label>
                                    <textarea id="a-alamat-<?= e($kunci) ?>" name="alamat" rows="3" maxlength="300" required><?= e($al['alamat']) ?></textarea>
                                </div>
                                <div class="addr-actions">
                                    <button type="submit" class="btn-primary" style="width:auto;">Simpan Alamat</button>
                                    <button type="button" class="btn-outline" data-addr-cancel>Batal</button>
                                </div>
                            </form>
                        </div>
                        <a href="#" class="btn-outline" data-addr-edit>Ubah</a>
                    </div>
                    <?php endforeach; ?>

                    <a href="#" class="btn-outline">+ Tambah Alamat Baru</a>
                </div>
            </section>

        </main>
    </div>

<?php require '../includes/footer.php'; ?>

    <script src="site.js?v=<?= filemtime(__DIR__ . '/site.js') ?>"></script>
</body>

</html>