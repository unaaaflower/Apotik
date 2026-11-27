<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Dashboard — KlikMedis</title>

    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>

<body class="shop-body">

    <?php
        $aktif = 'beranda';

        $namaLengkap = trim(auth()->user()->nama ?? '');
        $bagianNama = preg_split('/\s+/', $namaLengkap);
        $namaDepan = $bagianNama[0] ?? '';
    ?>

    <?php echo $__env->make('partials.topbar-user', ['aktif' => $aktif], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="shell">

        <!-- Content -->
        <main class="content">

            <div class="page-heading">
                <div>
                    <h1>Selamat datang, <?php echo e($namaDepan); ?> 👋</h1>
                    <p>Yuk cek promo hari ini dan lengkapi kebutuhan kesehatanmu.</p>
                </div>
            </div>

            <?php if(!empty($pengumuman)): ?>
                <div class="pengumuman-list" aria-label="Pengumuman">

                    <?php $__currentLoopData = $pengumuman; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="pengumuman-item is-<?php echo e($g['tipe']); ?>">

                            <span class="pengumuman-tag">
                                <?php echo e(\App\Models\Pengumuman::TIPE[$g['tipe']] ?? 'Info'); ?>

                            </span>

                            <div>
                                <strong><?php echo e($g['judul']); ?></strong>
                                <p><?php echo e($g['isi']); ?></p>
                            </div>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            <?php endif; ?>

            <div class="promo-banner">
                <div>
                    <h3>Diskon Spesial Produk Vitamin</h3>
                    <p>Jaga daya tahan tubuh, mulai dari sekarang! Diskon hingga 30%.</p>
                </div>
            </div>

            <div class="section-heading">
                <h2>Semua Produk</h2>
            </div>

            <?php echo $__env->make('partials.flash', [
                'pesanFlash' => $flashFavorit ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php echo $__env->make('partials.flash', [
                'pesanFlash' => $flashKeranjang ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="product-grid">

                <?php $__currentLoopData = $produk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.product-card', [
                        'p' => $p,
                        'kembali' => '/dashboard',
                        'varian' => 'katalog',
                        'favoritId' => $favoritId ?? []
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>

        </main>
    </div>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('js/site.js')); ?>"></script>

</body>
</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/user/dashboard.blade.php ENDPATH**/ ?>