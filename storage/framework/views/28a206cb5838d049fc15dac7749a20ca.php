<?php
    $badgeStatus = [
        'Menunggu Pembayaran' => 'warning',
        'Diproses' => 'warning',
        'Dikirim' => 'success',
        'Selesai' => 'success',
        'Dibatalkan' => 'danger',
    ];

    $aktifAdmin = 'dashboard';
    $judul = 'Dashboard';
    $breadcrumb = 'Ringkasan performa toko hari ini';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Dashboard Admin — KlikMedis</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <div>
            <?php echo $__env->make('partials.admin-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="admin-main">

                <div class="admin-page-heading">
                    <div>
                        <h1>Selamat datang kembali, Admin 👋</h1>
                        <p>Berikut ringkasan aktivitas apotek KlikMedis hari ini.</p>
                    </div>

                    <a href="<?php echo e(url('/admin/obat')); ?>" class="admin-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                        Tambah Obat
                    </a>
                </div>

                
                <div class="admin-stat-grid">

                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Total Obat</div>
                            <div class="stat-value"><?php echo e((int) $ringkasan['produk']); ?></div>
                            <div class="stat-delta up">Produk di katalog</div>
                        </div>

                        <span class="admin-stat-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>

                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Pesanan Diproses</div>
                            <div class="stat-value"><?php echo e((int) $ringkasan['pesanan_proses']); ?></div>

                            <div class="stat-delta<?php echo e($ringkasan['pesanan_proses'] > 0 ? ' down' : ' up'); ?>">
                                <?php echo e($ringkasan['pesanan_proses'] > 0 ? 'Perlu ditindaklanjuti' : 'Semua beres'); ?>

                            </div>
                        </div>

                        <span class="admin-stat-icon danger">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 9v4M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10.3 3.9 1.9 18a2 2 0 0 0 1.7 3h16.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>

                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Promo Aktif</div>
                            <div class="stat-value"><?php echo e((int) $ringkasan['promo_aktif']); ?></div>
                            <div class="stat-delta up">
                                <?php echo e((int) $ringkasan['promo_berakhir']); ?> berakhir minggu ini
                            </div>
                        </div>

                        <span class="admin-stat-icon gold">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M20.6 12.3 12.3 20.6a2 2 0 0 1-2.8 0l-6-6a2 2 0 0 1 0-2.9L11.8 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v6.1c0 .5-.2 1-.6 1.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>

                    <div class="admin-stat-card">
                        <div>
                            <div class="stat-label">Pesanan Hari Ini</div>
                            <div class="stat-value"><?php echo e((int) $ringkasan['pesanan_hari']); ?></div>
                            <div class="stat-delta up">
                                <?php echo e((int) $ringkasan['pengguna']); ?> pengguna terdaftar
                            </div>
                        </div>

                        <span class="admin-stat-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>

                </div>

                
                <div class="admin-card">

                    <p class="admin-card-title">Pesanan Terbaru</p>
                    <p class="admin-card-subtitle">Lima pesanan terakhir yang masuk.</p>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Penerima</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if(!$pesanan): ?>
                                    <tr class="admin-empty-row">
                                        <td colspan="4">Belum ada pesanan.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php $__currentLoopData = $pesanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><strong><?php echo e($p['kode']); ?></strong></td>
                                        <td><?php echo e($p['alamat_nama']); ?></td>
                                        <td>Rp<?php echo e(number_format((int) $p['total'], 0, ',', '.')); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($badgeStatus[$p['status']] ?? 'neutral'); ?>">
                                                <?php echo e($p['status']); ?>

                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <a href="<?php echo e(url('/admin/pesanan')); ?>" class="admin-btn-outline">
                        Kelola Semua Pesanan
                    </a>

                </div>

            </main>
        </div>

    </div>

    <div class="admin-toast" id="admin-toast" role="status" aria-live="polite"></div>

    <script src="<?php echo e(asset('js/admin.js')); ?>"></script>

</body>
</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>