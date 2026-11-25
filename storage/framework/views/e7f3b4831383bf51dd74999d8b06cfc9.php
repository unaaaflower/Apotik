<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <title>Masuk — KlikMedis</title>
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

                <div class="tabs" data-active="login" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab is-active" href="<?php echo e(url('/login')); ?>" role="tab" aria-selected="true">Masuk</a>
                    <a class="tab" href="<?php echo e(url('/register')); ?>" role="tab" aria-selected="false">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-login"
                    method="POST" action="<?php echo e(url('/login')); ?>">

                    <?php echo csrf_field(); ?>

                    <h2>Selamat datang kembali</h2>
                    <p class="auth-sub">
                        Masuk untuk melanjutkan belanja kebutuhan kesehatanmu.
                    </p>

                    <div class="field">
                        <label for="login-identitas">Email atau username</label>

                        <input
                            type="text"
                            id="login-identitas"
                            name="identitas"
                            placeholder="nama@email.com atau username"
                            value="<?php echo e(old('identitas')); ?>"
                            autocomplete="username"
                            required
                        >

                        <?php $__errorArgs = ['identitas'];
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
                        <label for="login-password">Kata sandi</label>

                        <div class="password-wrap">
                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-pass"
                                data-target="login-password"
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

                    <div class="row-between">
                        <label class="checkbox">
                            <input type="checkbox" name="remember" value="1">
                            <span>Ingat saya</span>
                        </label>

                        <a href="#" class="link-muted">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-primary">Masuk</button>

                    <div class="form-message <?php echo e(($errors->has('login') || $errors->has('identitas') || $errors->has('password')) ? 'is-error' : ''); ?>"
    id="login-message" role="status" aria-live="polite">

    <?php if(session('auth')): ?>
        <?php echo e(session('auth')); ?>

    <?php elseif($errors->has('login')): ?>
        <?php echo e($errors->first('login')); ?>

    <?php elseif($errors->has('identitas')): ?>
        <?php echo e($errors->first('identitas')); ?>

    <?php elseif($errors->has('password')): ?>
        <?php echo e($errors->first('password')); ?>

    <?php endif; ?>

</div>

                    <p class="switch-text">
                        Belum punya akun?
                        <a href="<?php echo e(url('/register')); ?>" class="link-strong">Daftar sekarang</a>
                    </p>

                </form>

            </div>
        </main>

    </div>

    <script src="<?php echo e(asset('js/auth.js')); ?>"></script>
</body>

</html>

<?php /**PATH D:\xamppp\htdocs\klikmedis-laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>