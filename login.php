<?php
/**
 * VIEW — form Masuk. Hanya menampilkan data; tidak ada query atau logika bisnis.
 * Variabel dari AuthController::login(): $error, $identitas, $flash
 */
$pesan   = $error !== '' ? $error : ($flash['pesan'] ?? '');
$isError = $error !== '' || ($flash && !$flash['ok']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — KlikMedis</title>
    <link rel="stylesheet" href="style.css?v=<?= asset_ver('user/style.css') ?>">
</head>

<body>

    <div class="page">

        <main class="auth-panel">
            <a href="login.php" class="logo logo-standalone">
                <span class="logo-mark"><img src="logo.png" alt="Logo KlikMedis"></span>
                <span class="logo-text logo-text-dark">KlikMedis<br><small>Apotek Online</small></span>
            </a>

            <div class="auth-card">

                <div class="tabs" data-active="login" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab is-active" href="login.php" role="tab" aria-selected="true">Masuk</a>
                    <a class="tab" href="register.php" role="tab" aria-selected="false">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-login" method="post" action="login.php" novalidate>
                    <?= csrf_field() ?>

                    <h2>Selamat datang kembali</h2>
                    <p class="auth-sub">Masuk untuk melanjutkan belanja kebutuhan kesehatanmu.</p>

                    <div class="field">
                        <label for="login-identitas">Email atau username</label>
                        <input type="text" id="login-identitas" name="identitas" placeholder="nama@email.com atau username"
                            value="<?= e($identitas) ?>" autocomplete="username" required>
                        <span class="field-error" data-error-for="login-identitas"></span>
                    </div>

                    <div class="field">
                        <label for="login-password">Kata sandi</label>
                        <div class="password-wrap">
                            <input type="password" id="login-password" name="password" placeholder="Masukkan kata sandi"
                                autocomplete="current-password" required>
                            <button type="button" class="toggle-pass" data-target="login-password"
                                aria-label="Tampilkan kata sandi">
                                <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                    <path d="M12 5c-5 0-9 4-10.5 7C3 15 7 19 12 19s9-4 10.5-7C21 9 17 5 12 5Z"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                    <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor"
                                        stroke-width="1.6" />
                                </svg>
                            </button>
                        </div>
                        <span class="field-error" data-error-for="login-password"></span>
                    </div>

                    <div class="row-between">
                        <label class="checkbox">
                            <input type="checkbox" name="remember">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="link-muted">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-primary">Masuk</button>

                    <div class="form-message<?= $isError ? ' is-error' : '' ?>" id="login-message" role="status" aria-live="polite"><?= e($pesan) ?></div>

                    <p class="switch-text">Belum punya akun? <a href="register.php" class="link-strong">Daftar sekarang</a></p>
                </form>

            </div>
        </main>

    </div>

    <script src="auth.js?v=<?= asset_ver('user/auth.js') ?>"></script>
</body>

</html>
