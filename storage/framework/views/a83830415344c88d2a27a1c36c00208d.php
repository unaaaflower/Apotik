<?php
    /*
     * Partial: pesan sukses/error sekali tampil.
     * Variabel: $pesanFlash = ['ok' => bool, 'pesan' => string]
     * atau null.
     */

    $pesanFlash = $pesanFlash ?? session()->pull('flash');
?>

<?php if(!empty($pesanFlash)): ?>
    <div class="form-message<?php echo e($pesanFlash['ok'] ? '' : ' is-error'); ?>"
         role="status"
         style="text-align:left; margin-bottom:16px;">
        <?php echo e($pesanFlash['pesan']); ?>

    </div>
<?php endif; ?>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/partials/flash.blade.php ENDPATH**/ ?>