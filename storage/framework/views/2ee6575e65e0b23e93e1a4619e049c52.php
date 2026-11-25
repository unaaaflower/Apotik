<?php
    /*
     * Partial: satu kartu produk.
     *
     * Variabel:
     * $p          = data produk
     * $kembali    = halaman tujuan setelah aksi
     * $varian     = katalog atau favorit
     * $favoritId  = daftar ID produk favorit pengguna
     */

    $varian = $varian ?? 'katalog';
    $kembali = $kembali ?? '/dashboard';

    $sudahFavorit = in_array(
        (int) $p['id'],
        $favoritId ?? [],
        true
    );

    $harga = 'Rp ' . number_format(
        (int) $p['harga'],
        0,
        ',',
        '.'
    );
?>

<?php if($varian === 'favorit'): ?>

    <div class="product-card" data-fav-item>

        <form method="POST"
              action="<?php echo e(route('favorit')); ?>"
              class="fav-form"
              onsubmit="return confirm('Hapus produk ini dari favorit?');">

            <?php echo csrf_field(); ?>

            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="produk_id" value="<?php echo e((int) $p['id']); ?>">
            <input type="hidden" name="kembali" value="/favorit">

            <button class="fav-toggle is-active"
                    type="submit"
                    aria-label="Hapus dari favorit">

                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"/>
                </svg>

            </button>
        </form>

        <div class="product-thumb">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                <rect x="5" y="3" width="14" height="18" rx="2"
                      stroke="currentColor" stroke-width="1.6"/>
                <path d="M9 8h6M9 12h6M9 16h3"
                      stroke="currentColor" stroke-width="1.6"
                      stroke-linecap="round"/>
            </svg>
        </div>

        <div class="product-name"><?php echo e($p['nama']); ?></div>
        <div class="product-meta"><?php echo e($p['ket']); ?></div>
        <div class="product-price"><?php echo e($harga); ?></div>

        <form method="POST"
              action="<?php echo e(route('favorit')); ?>"
              class="note-form">

            <?php echo csrf_field(); ?>

            <input type="hidden" name="aksi" value="catatan">
            <input type="hidden" name="produk_id" value="<?php echo e((int) $p['id']); ?>">
            <input type="hidden" name="kembali" value="/favorit">

            <label class="sr-only"
                   for="catatan-<?php echo e((int) $p['id']); ?>">
                Catatan untuk <?php echo e($p['nama']); ?>

            </label>

            <input type="text"
                   id="catatan-<?php echo e((int) $p['id']); ?>"
                   name="catatan"
                   value="<?php echo e($p['catatan'] ?? ''); ?>"
                   maxlength="100"
                   placeholder="Catatan (mis. stok bulanan)">

            <button type="submit" class="btn-note">
                Simpan
            </button>
        </form>

    </div>

<?php else: ?>

    <div class="product-card">

        <div class="product-thumb"></div>

        <div class="product-name"><?php echo e($p['nama']); ?></div>
        <div class="product-meta"><?php echo e($p['ket']); ?></div>

        <div class="price-row">

            <div class="product-price"><?php echo e($harga); ?></div>

            <form method="POST"
                  action="<?php echo e(route('favorit')); ?>"
                  class="fav-form"
                  <?php if($sudahFavorit): ?>
                      onsubmit="return confirm('Hapus produk ini dari favorit?');"
                  <?php endif; ?>>

                <?php echo csrf_field(); ?>

                <input type="hidden"
                       name="aksi"
                       value="<?php echo e($sudahFavorit ? 'hapus' : 'tambah'); ?>">

                <input type="hidden"
                       name="produk_id"
                       value="<?php echo e((int) $p['id']); ?>">

                <input type="hidden"
                       name="kembali"
                       value="/dashboard">

                <button class="fav-btn<?php echo e($sudahFavorit ? ' is-active' : ''); ?>"
                        type="submit"
                        aria-label="<?php echo e($sudahFavorit ? 'Hapus dari favorit' : 'Tambah ke favorit'); ?>"
                        aria-pressed="<?php echo e($sudahFavorit ? 'true' : 'false'); ?>">

                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"
                              stroke="currentColor"
                              stroke-width="1.8"
                              stroke-linejoin="round"/>
                    </svg>

                </button>
            </form>

        </div>

<?php endif; ?>

    <form method="POST"
          action="<?php echo e(route('keranjang')); ?>"
          class="add-form">

        <?php echo csrf_field(); ?>

        <input type="hidden" name="aksi" value="tambah">

        <input type="hidden"
               name="produk_id"
               value="<?php echo e((int) $p['id']); ?>">

        <input type="hidden"
               name="kembali"
               value="<?php echo e($kembali); ?>">

        <button class="btn-add" type="submit">
            + Keranjang
        </button>

    </form>

</div>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/partials/product-card.blade.php ENDPATH**/ ?>