<?php
    $tampilForm = (
        !empty($flashAlamat)
        && empty($flashAlamat['ok'])
        && ($flashAlamat['label'] ?? $kunci) === $kunci
    );

    $tampilPesanAlamat = (
        !empty($flashAlamat)
        && ($flashAlamat['label'] ?? $kunci) === $kunci
    );
?>

<?php if($tampilPesanAlamat): ?>
    <div
        class="form-message<?php echo e($flashAlamat['ok'] ? '' : ' is-error'); ?>"
        role="status"
        style="text-align:left;"
    >
        <?php echo e($flashAlamat['pesan']); ?>

    </div>
<?php endif; ?>

<form
    method="POST"
    action="<?php echo e(url($aksiUrl)); ?>"
    class="addr-form"
    data-addr-form
    <?php if(!$tampilForm): ?> hidden <?php endif; ?>
>
    <?php echo csrf_field(); ?>

    <input type="hidden" name="aksi" value="ubah_alamat">
    <input type="hidden" name="label" value="<?php echo e($kunci); ?>">

    <div class="field">
        <label for="a-nama-<?php echo e($kunci); ?>">Nama penerima</label>
        <input
            type="text"
            id="a-nama-<?php echo e($kunci); ?>"
            name="nama"
            value="<?php echo e(old('nama', $al['nama'])); ?>"
            maxlength="100"
            required
        >
    </div>

    <div class="field">
        <label for="a-telp-<?php echo e($kunci); ?>">No. HP</label>
        <input
            type="tel"
            id="a-telp-<?php echo e($kunci); ?>"
            name="telp"
            value="<?php echo e(old('telp', $al['telp'])); ?>"
            maxlength="20"
            required
        >
    </div>

    <div class="field field-textarea">
        <label for="a-alamat-<?php echo e($kunci); ?>">Alamat lengkap</label>
        <textarea
            id="a-alamat-<?php echo e($kunci); ?>"
            name="alamat"
            rows="3"
            maxlength="300"
            required
        ><?php echo e(old('alamat', $al['alamat'])); ?></textarea>
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
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/partials/alamat-form.blade.php ENDPATH**/ ?>