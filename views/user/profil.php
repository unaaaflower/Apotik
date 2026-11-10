<?php

$aktif = 'profil';

$tampilPesan = static function (string $bagian) use ($pesan): void {
    $p = $pesan[$bagian] ?? null;
    if ($p) {
        echo '<div class="form-message' . ($p['ok'] ? '' : ' is-error') . '" role="status" style="text-align:left;">'
           . e($p['pesan']) . '</div>';
    }
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — KlikMedis</title>
    <link rel="stylesheet" href="style.css?v=<?= asset_ver('user/style.css') ?>">
</head>

<body class="shop-body">

<?php require __DIR__ . '/../partials/topbar-user.php'; ?>

    <div class="shell">

        <!-- Content -->
        <main class="content">

            <!-- TAB: Profil (Ubah Profil) -->
            <section id="tab-profil" class="account-tab is-active">
                <div class="card">
                    <div class="profile-header">
                        <div class="avatar"><?= e(inisial()) ?></div>
                        <div>
                            <h2><?= e($user['nama']) ?></h2>
                            <p>Anggota sejak <?= e(tanggal_id(strtotime($user['created_at']))) ?> · @<?= e($user['username']) ?> · <?= e($user['email']) ?></p>
                        </div>
                    </div>

                    <form class="form-grid" method="post" action="profil.php" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="ubah_profil">

                        <div class="field">
                            <label for="p-name">Nama lengkap</label>
                            <input type="text" id="p-name" name="nama" value="<?= e($isian['nama']) ?>" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label for="p-username">Username</label>
                            <input type="text" id="p-username" name="username" value="<?= e($isian['username']) ?>" maxlength="30" required>
                        </div>
                        <div class="field">
                            <label for="p-email">Email</label>
                            <input type="email" id="p-email" name="email" value="<?= e($isian['email']) ?>" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label for="p-phone">No. HP</label>
                            <input type="tel" id="p-phone" name="no_hp" value="<?= e($isian['no_hp']) ?>" maxlength="15">
                        </div>
                        <div class="field">
                            <label for="p-dob">Tanggal lahir</label>
                            <input type="date" id="p-dob" name="tanggal_lahir" value="<?= e($isian['tanggal_lahir']) ?>" max="<?= e(date('Y-m-d')) ?>">
                        </div>
                        <div class="field full">
                            <button type="submit" class="btn-primary" style="width:auto;">Simpan Perubahan</button>
                            <?php $tampilPesan('profil'); ?>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB: Keamanan (Ubah kata sandi) -->
            <section id="tab-keamanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Keamanan</p>
                    <p class="card-subtitle">Ganti kata sandi akunmu secara berkala.</p>

                    <form class="form-grid" method="post" action="profil.php#tab-keamanan" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="ubah_password">

                        <div class="field full">
                            <label for="pw-lama">Kata sandi saat ini</label>
                            <input type="password" id="pw-lama" name="password_lama" autocomplete="current-password" required>
                        </div>
                        <div class="field">
                            <label for="pw-baru">Kata sandi baru</label>
                            <input type="password" id="pw-baru" name="password_baru" autocomplete="new-password" minlength="8" maxlength="72" required>
                            <span class="field-hint">Minimal 8 karakter</span>
                        </div>
                        <div class="field">
                            <label for="pw-konfirmasi">Konfirmasi kata sandi baru</label>
                            <input type="password" id="pw-konfirmasi" name="konfirmasi" autocomplete="new-password" minlength="8" maxlength="72" required>
                        </div>
                        <div class="field full">
                            <button type="submit" class="btn-primary" style="width:auto;">Ubah Kata Sandi</button>
                            <?php $tampilPesan('password'); ?>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB: Pesanan -->
            <section id="tab-pesanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Riwayat Pesanan</p>
                    <p class="card-subtitle">Pantau status pesanan obat dan produk kesehatanmu.</p>
                    <?php if ($flashPesanan): ?>
                        <div class="form-message" role="status" style="text-align:left;"><?= e($flashPesanan['pesan']) ?></div>
                    <?php endif; ?>

                    <?php foreach ($pesananBaru as $ps): ?>
                    <div class="order-row">
                        <div class="order-thumb"></div>
                        <div class="order-info">
                            <strong>#<?= e($ps['id']) ?> · <?= (int) $ps['jumlah'] ?> produk</strong>
                            <span><?= e($ps['tanggal']) ?> · <?= e(rupiah((int) $ps['total'])) ?> · COD</span>
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
                        $tampilForm = ($flashAlamat && !$flashAlamat['ok'] && $flashAlamat['label'] === $kunci);
                    ?>
                    <div class="address-item" data-addr-card>
                        <div style="flex:1; min-width:0;">
                            <span class="tag"><?= e($namaLabel) ?></span>
                            <p data-addr-view><strong><?= e($al['nama']) ?></strong> · <?= e($al['telp']) ?><br>
                            <?= e($al['alamat']) ?></p>
                            <?php if ($flashAlamat && $flashAlamat['label'] === $kunci): ?>
                                <div class="form-message<?= $flashAlamat['ok'] ? '' : ' is-error' ?>" role="status" style="text-align:left;"><?= e($flashAlamat['pesan']) ?></div>
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

            <!-- TAB: Hapus Akun -->
            <section id="tab-hapus" class="account-tab">
                <div class="card">
                    <p class="card-title">Hapus Akun</p>
                    <p class="card-subtitle">Akun dan data profilmu akan dihapus permanen dan tidak bisa dikembalikan.</p>

                    <form class="form-grid" method="post" action="profil.php#tab-hapus" novalidate
                        onsubmit="return confirm('Yakin hapus akun? Tindakan ini tidak bisa dibatalkan.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="aksi" value="hapus_akun">

                        <div class="field full">
                            <label for="hapus-password">Masukkan kata sandi untuk konfirmasi</label>
                            <input type="password" id="hapus-password" name="password" autocomplete="current-password" required>
                        </div>
                        <div class="field full">
                            <button type="submit" class="btn-danger">Hapus Akun Saya</button>
                            <?php $tampilPesan('hapus'); ?>
                        </div>
                    </form>
                </div>
            </section>

        </main>
    </div>

<?php require dirname(__DIR__, 2) . '/includes/footer.php'; ?>

    <script src="site.js?v=<?= asset_ver('user/site.js') ?>"></script>
</body>

</html>
