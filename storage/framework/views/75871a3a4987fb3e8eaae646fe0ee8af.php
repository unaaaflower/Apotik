<?php
    $aktif = 'keranjang';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Keranjang — KlikMedis</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>

<body class="shop-body">

    <?php echo $__env->make('partials.topbar-user', ['aktif' => $aktif], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="shell">
        <main class="content">

            <div class="checkout-steps">
                <div class="step is-active">
                    <span class="step-circle">1</span> Keranjang
                </div>
                <span class="step-line"></span>
                <div class="step">
                    <span class="step-circle">2</span> Konfirmasi
                </div>
            </div>

            <?php echo $__env->make('partials.flash', [
                'pesanFlash' => $flash ?? null
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="checkout-grid">

                <!-- Kiri: isi keranjang -->
                <div>
                    <div class="card">
                        <div class="card-head">
                            <p class="card-title">Item di Keranjang</p>
                            <a href="<?php echo e(route('dashboard')); ?>" class="btn-outline">
                                + Tambah Produk
                            </a>
                        </div>

                        <?php if($items): ?>

                            <div class="cart-note">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                    <rect x="4" y="6" width="16" height="12" rx="2"
                                        stroke="currentColor" stroke-width="1.6"/>
                                    <path d="M4 10h16" stroke="currentColor" stroke-width="1.6"/>
                                </svg>
                                Ubah jumlah (1–99) atau hapus produk sebelum lanjut ke konfirmasi.
                            </div>

                            <div id="cart-items">

                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <div class="checkout-item">
                                        <div class="item-thumb"></div>

                                        <div class="item-info">
                                            <strong><?php echo e($it['nama']); ?></strong>
                                            <span>
                                                <?php echo e($it['ket']); ?>

                                                · Rp <?php echo e(number_format((int) $it['harga'], 0, ',', '.')); ?>

                                                / <?php echo e($it['satuan']); ?>

                                            </span>
                                        </div>

                                        <div class="item-controls">

                                            <!-- UBAH JUMLAH -->
                                            <form method="POST"
                                                  action="<?php echo e(route('keranjang')); ?>"
                                                  class="qty-form"
                                                  novalidate>

                                                <?php echo csrf_field(); ?>

                                                <input type="hidden" name="aksi" value="ubah">
                                                <input type="hidden" name="item_id"
                                                       value="<?php echo e((int) $it['id']); ?>">

                                                <div class="qty-stepper">
                                                    <button type="button"
                                                            data-qty-step="-1"
                                                            aria-label="Kurangi jumlah">
                                                        −
                                                    </button>

                                                    <input type="number"
                                                           name="jumlah"
                                                           value="<?php echo e((int) $it['jumlah']); ?>"
                                                           min="1"
                                                           max="99"
                                                           inputmode="numeric"
                                                           aria-label="Jumlah <?php echo e($it['nama']); ?>"
                                                           data-qty-input>

                                                    <button type="button"
                                                            data-qty-step="1"
                                                            aria-label="Tambah jumlah">
                                                        +
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="item-price">
                                                Rp <?php echo e(number_format(
                                                    (int) $it['harga'] * (int) $it['jumlah'],
                                                    0,
                                                    ',',
                                                    '.'
                                                )); ?>

                                            </div>

                                            <!-- HAPUS DENGAN KONFIRMASI -->
                                            <form method="POST"
                                                  action="<?php echo e(route('keranjang')); ?>"
                                                  class="remove-form"
                                                  onsubmit="return confirm('Hapus produk ini dari keranjang?');">

                                                <?php echo csrf_field(); ?>

                                                <input type="hidden" name="aksi" value="hapus">
                                                <input type="hidden" name="item_id"
                                                       value="<?php echo e((int) $it['id']); ?>">

                                                <button type="submit"
                                                        class="item-remove"
                                                        aria-label="Hapus produk">

                                                    <svg width="16" height="16"
                                                         viewBox="0 0 24 24" fill="none">
                                                        <path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13"
                                                              stroke="currentColor"
                                                              stroke-width="1.7"
                                                              stroke-linecap="round"
                                                              stroke-linejoin="round"/>
                                                    </svg>

                                                </button>
                                            </form>

                                        </div>
                                    </div>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>

                        <?php else: ?>

                            <div class="empty-state" id="cart-empty" style="display:flex;">

                                <span class="empty-icon">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"
                                              stroke="currentColor"
                                              stroke-width="1.7"
                                              stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                    </svg>
                                </span>

                                <h3>Keranjang kamu kosong</h3>

                                <p>
                                    Yuk cari obat atau produk kesehatan yang kamu butuhkan.
                                </p>

                                <a href="<?php echo e(route('dashboard')); ?>" class="btn-outline">
                                    Mulai Belanja
                                </a>

                            </div>

                        <?php endif; ?>

                    </div>
                </div>

                <!-- Kanan: ringkasan -->
                <div>
                    <div class="card">

                        <p class="card-title">Ringkasan Belanja</p>

                        <div class="summary-line">
                            <span>Subtotal (<?php echo e(count($items)); ?> produk)</span>
                            <span>Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></span>
                        </div>

                        <div class="summary-line">
                            <span>Estimasi Ongkos Kirim</span>
                            <span>Rp <?php echo e(number_format($ongkir, 0, ',', '.')); ?></span>
                        </div>

                        <div class="summary-line total">
                            <span>Total</span>
                            <span>
                                Rp <?php echo e(number_format($subtotal + $ongkir, 0, ',', '.')); ?>

                            </span>
                        </div>

                        <?php if($items): ?>

                            <a href="<?php echo e(route('checkout')); ?>"
                               class="btn-primary"
                               id="cart-checkout-btn">
                                Lanjut ke Konfirmasi
                            </a>

                        <?php else: ?>

                            <a href="<?php echo e(route('dashboard')); ?>" class="btn-primary">
                                Mulai Belanja
                            </a>

                        <?php endif; ?>

                    </div>
                </div>

            </div>

        </main>
    </div>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('js/site.js')); ?>"></script>

</body>
</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/user/keranjang.blade.php ENDPATH**/ ?>