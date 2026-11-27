<?php
    $aktif = 'keranjang';
    $formatRupiah = fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Pembayaran — KlikMedis</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>

<body class="shop-body">

    <?php echo $__env->make('partials.topbar-user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                    <span class="step-circle">3</span> Pembayaran (COD)
                </div>
            </div>

            <div class="checkout-grid">

                
                <div>
                    <div class="card">
                        <p class="card-title">Pesanan Berhasil Dikonfirmasi</p>

                        <p class="card-subtitle">
                            Pesanan #<?php echo e($order['kode']); ?> · status:
                            <span class="status-pill pending"><?php echo e($order['status']); ?></span>
                        </p>

                        <div class="pay-total">
                            <span>Total pembayaran</span>
                            <strong><?php echo e($formatRupiah($order['total'])); ?></strong>
                        </div>

                        <p class="pay-heading">COD (Bayar di Tempat)</p>

                        <p class="card-subtitle">
                            Siapkan uang tunai sebesar
                            <strong><?php echo e($formatRupiah($order['total'])); ?></strong>
                            saat pesanan tiba. Tidak ada pembayaran yang perlu dilakukan sekarang.
                        </p>

                        <a href="<?php echo e(url('/profil')); ?>#tab-pesanan" class="btn-primary">
                            Lihat Pesanan Saya
                        </a>

                        <p class="pay-back-wrap">
                            <a href="<?php echo e(url('/dashboard')); ?>" class="pay-back">
                                ← Kembali ke beranda
                            </a>
                        </p>
                    </div>
                </div>

                
                <div>
                    <div class="card">
                        <p class="card-title">Ringkasan Pesanan</p>

                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="checkout-item">
                                <div class="item-thumb"></div>

                                <div class="item-info">
                                    <strong><?php echo e($it['nama']); ?></strong>
                                    <span>
                                        <?php echo e($it['ket']); ?> × <?php echo e((int) $it['jumlah']); ?>

                                    </span>
                                </div>

                                <div class="item-price">
                                    <?php echo e($formatRupiah((int) $it['harga'] * (int) $it['jumlah'])); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span><?php echo e($formatRupiah($order['subtotal'])); ?></span>
                        </div>

                        <div class="summary-line">
                            <span>Ongkos Kirim (<?php echo e($order['kurir']); ?>)</span>
                            <span><?php echo e($formatRupiah($order['ongkir'])); ?></span>
                        </div>

                        <div class="summary-line total">
                            <span>Total</span>
                            <span><?php echo e($formatRupiah($order['total'])); ?></span>
                        </div>

                        <p class="pay-note">
                            Dikirim ke:
                            <strong><?php echo e($order['alamat_nama']); ?></strong>
                            · <?php echo e($order['alamat_telp']); ?>

                            <br>
                            <?php echo e($order['alamat_teks']); ?>

                        </p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('js/site.js')); ?>"></script>
</body>

</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/user/pembayaran.blade.php ENDPATH**/ ?>