<?php
/**
 * VIEW — form Daftar. Hanya menampilkan data; tidak ada query atau logika bisnis.
 * Variabel dari AuthController::register(): $old, $errors, $error
 */
$err = static fn(string $k): string => $errors[$k] ?? '';
$cls = static fn(string $k): string => isset($errors[$k]) ? ' has-error' : '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — KlikMedis</title>
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

                <div class="tabs" data-active="register" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab" href="login.php" role="tab" aria-selected="false">Masuk</a>
                    <a class="tab is-active" href="register.php" role="tab" aria-selected="true">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-register" method="post" action="register.php" novalidate>
                    <?= csrf_field() ?>

                    <h2>Buat akun baru</h2>
                    <p class="auth-sub">Gratis, cepat, dan siap kelola kebutuhan kesehatanmu.</p>

                    <div class="field">
                        <label for="reg-name">Nama lengkap</label>
                        <input type="text" id="reg-name" name="nama" class="<?= trim($cls('nama')) ?>" placeholder="Nama sesuai KTP"
                            value="<?= e($old['nama']) ?>" autocomplete="name" required minlength="3" maxlength="100">
                        <span class="field-error" data-error-for="reg-name"><?= e($err('nama')) ?></span>
                    </div>

                    <div class="field">
                        <label for="reg-username">Username</label>
                        <input type="text" id="reg-username" name="username" class="<?= trim($cls('username')) ?>" placeholder="mis. andi_pratama"
                            value="<?= e($old['username']) ?>" autocomplete="username" required minlength="3" maxlength="30">
                        <span class="field-error" data-error-for="reg-username"><?= e($err('username')) ?></span>
                    </div>

                    <div class="field">
                        <label for="reg-email">Email</label>
                        <input type="email" id="reg-email" name="email" class="<?= trim($cls('email')) ?>" placeholder="nama@email.com"
                            value="<?= e($old['email']) ?>" autocomplete="email" required maxlength="100">
                        <span class="field-error" data-error-for="reg-email"><?= e($err('email')) ?></span>
                    </div>

                    <div class="field">
                        <label for="reg-phone">No. HP</label>
                        <input type="tel" id="reg-phone" name="no_hp" class="<?= trim($cls('no_hp')) ?>" placeholder="0812 3456 7890"
                            value="<?= e($old['no_hp']) ?>" autocomplete="tel" required pattern="[0-9+\s\-]{9,15}">
                        <span class="field-error" data-error-for="reg-phone"><?= e($err('no_hp')) ?></span>
                    </div>

                    <div class="field">
                        <label for="reg-password">Kata sandi</label>
                        <div class="password-wrap">
                            <input type="password" id="reg-password" name="password" class="<?= trim($cls('password')) ?>" placeholder="Minimal 8 karakter"
                                autocomplete="new-password" required minlength="8" maxlength="72">
                            <button type="button" class="toggle-pass" data-target="reg-password"
                                aria-label="Tampilkan kata sandi">
                                <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                    <path d="M12 5c-5 0-9 4-10.5 7C3 15 7 19 12 19s9-4 10.5-7C21 9 17 5 12 5Z"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                    <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor"
                                        stroke-width="1.6" />
                                </svg>
                            </button>
                        </div>
                        <div class="strength-meter" aria-hidden="true">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <span class="field-hint" id="reg-password-hint">Gunakan kombinasi huruf, angka, dan
                            simbol</span>
                        <span class="field-error" data-error-for="reg-password"><?= e($err('password')) ?></span>
                    </div>

                    <div class="field">
                        <label for="reg-confirm">Konfirmasi kata sandi</label>
                        <input type="password" id="reg-confirm" name="konfirmasi" class="<?= trim($cls('konfirmasi')) ?>" placeholder="Ulangi kata sandi"
                            autocomplete="new-password" required minlength="8" maxlength="72">
                        <span class="field-error" data-error-for="reg-confirm"><?= e($err('konfirmasi')) ?></span>
                    </div>

                    <label class="checkbox terms">
                        <input type="checkbox" name="setuju" id="reg-terms" value="1" required<?= !empty($_POST['setuju']) ? ' checked' : '' ?>>
                        <span>Saya menyetujui <a href="#" class="link-strong">Syarat &amp; Ketentuan</a> dan <a href="#"
                                class="link-strong">Kebijakan Privasi</a></span>
                    </label>
                    <span class="field-error" data-error-for="reg-terms"><?= e($err('setuju')) ?></span>

                    <button type="submit" class="btn-primary">Daftar</button>

                    <div class="form-message<?= $error !== '' ? ' is-error' : '' ?>" id="register-message" role="status" aria-live="polite"><?= e($error) ?></div>

                    <p class="switch-text">Sudah punya akun? <a href="login.php" class="link-strong">Masuk di sini</a></p>
                </form>

            </div>
        </main>

    </div>

    <script src="auth.js?v=<?= asset_ver('user/auth.js') ?>"></script>
</body>

</html>
