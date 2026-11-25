<header class="topbar">
    <a href="<?php echo e(route('dashboard')); ?>" class="logo">
        <span class="logo-mark">
            <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo KlikMedis">
        </span>
        <span class="logo-text logo-text-dark">
            KlikMedis<br><small>Apotek Online</small>
        </span>
    </a>

    <div class="topbar-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        <input type="text" id="site-search" name="q"
            placeholder="Cari obat, vitamin, atau produk kesehatan..."
            aria-label="Cari produk" autocomplete="off">
    </div>

    <div class="topbar-actions">
        <?php
            $jmlKeranjang = \Illuminate\Support\Facades\DB::table('keranjang')
                ->where('user_id', auth()->id())
                ->count();
        ?>

        <a href="<?php echo e(route('keranjang')); ?>"
           class="icon-btn<?php echo e(($aktif ?? '') === 'keranjang' ? ' is-active' : ''); ?>"
           aria-label="Keranjang">

            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="10" cy="21" r="1.4" fill="currentColor"/>
                <circle cx="17" cy="21" r="1.4" fill="currentColor"/>
            </svg>

            <?php if($jmlKeranjang > 0): ?>
                <span class="badge-count"><?php echo e($jmlKeranjang); ?></span>
            <?php endif; ?>
        </a>

        <button type="button" class="icon-btn nav-toggle"
            id="nav-toggle" aria-label="Buka menu"
            aria-haspopup="true" aria-expanded="false"
            aria-controls="nav-menu">

            <svg class="icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M4 7h16M4 12h16M4 17h16"
                    stroke="currentColor" stroke-width="1.9"
                    stroke-linecap="round"/>
            </svg>

            <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M6 6l12 12M18 6L6 18"
                    stroke="currentColor" stroke-width="1.9"
                    stroke-linecap="round"/>
            </svg>
        </button>

        <nav class="nav-menu" id="nav-menu"
            role="menu" aria-label="Menu akun">

            <p class="nav-menu-title">Akun Saya</p>

            <a href="<?php echo e(route('dashboard')); ?>"
                class="nav-menu-link<?php echo e(($aktif ?? '') === 'beranda' ? ' is-active' : ''); ?>"
                role="menuitem">
                Beranda
            </a>

            <a href="<?php echo e(route('favorit')); ?>"
                class="nav-menu-link<?php echo e(($aktif ?? '') === 'favorit' ? ' is-active' : ''); ?>"
                role="menuitem">
                Produk Favorit
            </a>

            <a href="<?php echo e(route('pengingat')); ?>"
                class="nav-menu-link<?php echo e(($aktif ?? '') === 'pengingat' ? ' is-active' : ''); ?>"
                role="menuitem">
                Pengingat Obat
            </a>

            <a href="<?php echo e(route('profil')); ?>"
                class="nav-menu-link<?php echo e(($aktif ?? '') === 'profil' ? ' is-active' : ''); ?>"
                role="menuitem">
                Profil Saya
            </a>

            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit"
                    class="nav-menu-link is-danger"
                    role="menuitem">
                    Keluar
                </button>
            </form>
        </nav>
    </div>
</header>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/partials/topbar-user.blade.php ENDPATH**/ ?>