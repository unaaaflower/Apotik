<?php
    $aktif = 'keranjang';
    $ongkirAwal = (int) array_key_first($opsiPengiriman);
    $formatRupiah = fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');
    $formatTanggal = fn ($hari) => now()->addDays($hari)->format('d/m/Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Konfirmasi — KlikMedis</title>
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
                <div class="step is-active">
                    <span class="step-circle">2</span> Konfirmasi
                </div>
            </div>

            
            <form id="checkout-form" method="POST" action="<?php echo e(route('checkout')); ?>">
                <?php echo csrf_field(); ?>
            </form>

            <div class="checkout-grid" id="checkout-grid">

                
                <div>
                    <div class="card">
                        <div class="card-head">
                            <p class="card-title">Alamat Pengiriman</p>
                            <button type="button" class="btn-outline" data-addr-edit>
                                Ubah
                            </button>
                        </div>

                        <p data-addr-view style="margin:0; font-size:13px; color:var(--ink-soft); line-height:1.6;">
                            <strong style="color:var(--ink);">
                                <?php echo e($alamat['nama']); ?>

                            </strong>
                            · <?php echo e($alamat['telp']); ?>

                            <br>
                            <?php echo e($alamat['alamat']); ?>

                        </p>

                        
                        <?php if($flash && ($flash['label'] ?? '') === 'utama'): ?>
                            <div class="form-message<?php echo e($flash['ok'] ? '' : ' is-error'); ?>"
                                 role="status"
                                 style="text-align:left;">
                                <?php echo e($flash['pesan']); ?>

                            </div>
                        <?php endif; ?>

                        
                        <form method="POST"
                              action="<?php echo e(route('checkout')); ?>"
                              class="addr-form"
                              data-addr-form
                              <?php if(!$flash || ($flash['ok'] ?? true) || ($flash['label'] ?? '') !== 'utama'): ?>
                                  hidden
                              <?php endif; ?>>

                            <?php echo csrf_field(); ?>

                            <input type="hidden" name="aksi" value="ubah_alamat">
                            <input type="hidden" name="label" value="utama">

                            <div class="field">
                                <label for="a-nama-utama">Nama penerima</label>
                                <input type="text"
                                       id="a-nama-utama"
                                       name="nama"
                                       value="<?php echo e($alamat['nama']); ?>"
                                       maxlength="100"
                                       required>
                            </div>

                            <div class="field">
                                <label for="a-telp-utama">No. HP</label>
                                <input type="tel"
                                       id="a-telp-utama"
                                       name="telp"
                                       value="<?php echo e($alamat['telp']); ?>"
                                       maxlength="20"
                                       required>
                            </div>

                            <div class="field field-textarea">
                                <label for="a-alamat-utama">Alamat lengkap</label>
                                <textarea id="a-alamat-utama"
                                          name="alamat"
                                          rows="3"
                                          maxlength="300"
                                          required><?php echo e($alamat['alamat']); ?></textarea>
                            </div>

                            <div class="addr-actions">
                                <button type="submit" class="btn-primary" style="width:auto;">
                                    Simpan Alamat
                                </button>

                                <button type="button" class="btn-outline" data-addr-cancel>
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    
                    <div class="card">
                        <p class="card-title">Metode Pengiriman</p>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="shipping"
                                   value="10000"
                                   form="checkout-form"
                                   checked>

                            <div class="ship-info">
                                <strong>JNE Reguler (2-3 hari)</strong>
                                <span>
                                    Estimasi tiba <?php echo e($formatTanggal(2)); ?>

                                    – <?php echo e($formatTanggal(3)); ?>

                                </span>
                            </div>

                            <span class="ship-price">Rp 10.000</span>
                        </label>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="shipping"
                                   value="15000"
                                   form="checkout-form">

                            <div class="ship-info">
                                <strong>JNE YES (1 hari)</strong>
                                <span>
                                    Estimasi tiba <?php echo e($formatTanggal(1)); ?>

                                </span>
                            </div>

                            <span class="ship-price">Rp 15.000</span>
                        </label>
                    </div>

                    
                    <div class="card">
                        <p class="card-title">Metode Pembayaran</p>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="payment"
                                   value="cod"
                                   form="checkout-form"
                                   checked>

                            <div class="ship-info">
                                <strong>COD (Bayar di Tempat)</strong>
                                <span>
                                    Bayar tunai ke kurir saat pesanan tiba
                                </span>
                            </div>
                        </label>
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
                            <span id="sum-subtotal" data-subtotal="<?php echo e((int) $subtotal); ?>">
                                <?php echo e($formatRupiah($subtotal)); ?>

                            </span>
                        </div>

                        <div class="summary-line">
                            <span>Ongkos Kirim</span>
                            <span id="sum-shipping">
                                <?php echo e($formatRupiah($ongkirAwal)); ?>

                            </span>
                        </div>

                        <div class="summary-line total">
                            <span>Total</span>
                            <span id="sum-total">
                                <?php echo e($formatRupiah($subtotal + $ongkirAwal)); ?>

                            </span>
                        </div>

                        <button type="submit"
                                class="btn-primary"
                                id="pay-btn"
                                form="checkout-form">
                            Konfirmasi Pesanan
                        </button>

                        <div class="form-message<?php echo e($errorCheckout !== '' ? ' is-error' : ''); ?>"
                             id="checkout-message"
                             role="status"
                             aria-live="polite">
                            <?php echo e($errorCheckout); ?>

                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('js/site.js')); ?>"></script>
</body>
</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/user/checkout.blade.php ENDPATH**/ ?>