<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Daftar — KlikMedis</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">
</head>

<body>

    <div class="page">

        <main class="auth-panel">
            <a href="<?php echo e(url('/login')); ?>" class="logo logo-standalone">
                <span class="logo-mark">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="Logo KlikMedis">
                </span>
                <span class="logo-text logo-text-dark">
                    KlikMedis<br><small>Apotek Online</small>
                </span>
            </a>

            <div class="auth-card">

                <div class="tabs" data-active="register" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab" href="<?php echo e(url('/login')); ?>" role="tab" aria-selected="false">Masuk</a>
                    <a class="tab is-active" href="<?php echo e(url('/register')); ?>" role="tab" aria-selected="true">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-register"
                    method="POST" action="<?php echo e(url('/register')); ?>">

                    <?php echo csrf_field(); ?>

                    <h2>Buat akun baru</h2>
                    <p class="auth-sub">
                        Gratis, cepat, dan siap kelola kebutuhan kesehatanmu.
                    </p>

                    <div class="field">
                        <label for="reg-name">Nama lengkap</label>
                        <input
                            type="text"
                            id="reg-name"
                            name="nama"
                            placeholder="Nama sesuai KTP"
                            value="<?php echo e(old('nama')); ?>"
                            autocomplete="name"
                            required minlength="3" maxlength="100"
                        >
                        <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="reg-username">Username</label>
                        <input
                            type="text"
                            id="reg-username"
                            name="username"
                            placeholder="mis. andi_pratama"
                            value="<?php echo e(old('username')); ?>"
                            autocomplete="username"
                            required minlength="3" maxlength="30"
                        >
                        <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="reg-email">Email</label>
                        <input
                            type="email"
                            id="reg-email"
                            name="email"
                            placeholder="nama@email.com"
                            value="<?php echo e(old('email')); ?>"
                            autocomplete="email"
                            required maxlength="100"
                        >
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="reg-phone">No. HP</label>
                        <input
                            type="tel"
                            id="reg-phone"
                            name="no_hp"
                            placeholder="0812 3456 7890"
                            value="<?php echo e(old('no_hp')); ?>"
                            autocomplete="tel"
                            required
                        >
                        <?php $__errorArgs = ['no_hp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="reg-password">Kata sandi</label>

                        <div class="password-wrap">
                            <input
                                type="password"
                                id="reg-password"
                                name="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                required minlength="8" maxlength="72"
                            >

                            <button
                                type="button"
                                class="toggle-pass"
                                data-target="reg-password"
                                aria-label="Tampilkan kata sandi"
                            >
                                <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                    <path d="M12 5c-5 0-9 4-10.5 7C3 15 7 19 12 19s9-4 10.5-7C21 9 17 5 12 5Z"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                    <circle cx="12" cy="12" r="3"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                </svg>
                            </button>
                        </div>

                        <div class="strength-meter" aria-hidden="true">
                            <span></span><span></span><span></span><span></span>
                        </div>

                        <span class="field-hint" id="reg-password-hint">
                            Gunakan kombinasi huruf, angka, dan simbol
                        </span>

                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="field">
                        <label for="reg-confirm">Konfirmasi kata sandi</label>
                        <input
                            type="password"
                            id="reg-confirm"
                            name="konfirmasi"
                            placeholder="Ulangi kata sandi"
                            autocomplete="new-password"
                            required minlength="8" maxlength="72"
                        >

                        <?php $__errorArgs = ['konfirmasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="field-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <label class="checkbox terms">
                        <input
                            type="checkbox"
                            name="setuju"
                            id="reg-terms"
                            value="1"
                            required
                            <?php if(old('setuju')): echo 'checked'; endif; ?>
                        >
                        <span>
                            Saya menyetujui
                            <a href="#" class="link-strong">Syarat &amp; Ketentuan</a>
                            dan
                            <a href="#" class="link-strong">Kebijakan Privasi</a>
                        </span>
                    </label>

                    <?php $__errorArgs = ['setuju'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <span class="field-error"><?php echo e($message); ?></span>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <button type="submit" class="btn-primary">Daftar</button>

                    <div class="form-message" id="register-message"
                        role="status" aria-live="polite">
                        <?php if($errors->any()): ?>
                            <?php echo e($errors->first()); ?>

                        <?php endif; ?>
                    </div>

                    <p class="switch-text">
                        Sudah punya akun?
                        <a href="<?php echo e(url('/login')); ?>" class="link-strong">Masuk di sini</a>
                    </p>

                </form>

            </div>
        </main>

    </div>

    <script src="<?php echo e(asset('js/auth.js')); ?>"></script>
</body>

</html>
<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/auth/register.blade.php ENDPATH**/ ?>