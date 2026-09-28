<?php
require '../includes/session.php';

// Sudah login -> langsung ke dashboard sesuai role
if (is_logged_in()) {
    redirect_to(is_admin() ? '../admin/admin-dashboard.php' : 'dashboard.php');
}

require '../config/users.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = cari_user($email);

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['email'] = $user['email'];
        $_SESSION['nama']  = $user['nama'];
        $_SESSION['role']  = $user['role'];

        redirect_to($user['role'] === 'admin' ? '../admin/admin-dashboard.php' : 'dashboard.php');
    }

    $error = 'Email atau kata sandi salah.';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SehatKita</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="page">

        <main class="auth-panel">
            <a href="login.php" class="logo logo-standalone">
                <span class="logo-mark"><img src="logo.png" alt="Logo SehatKita"></span>
                <span class="logo-text logo-text-dark">SehatKita<br><small>Apotek Online</small></span>
            </a>

            <div class="auth-card">

                <div class="tabs" data-active="login" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab is-active" href="login.php" role="tab" aria-selected="true">Masuk</a>
                    <a class="tab" href="register.php" role="tab" aria-selected="false">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-login" method="post" action="login.php" novalidate>

                    <h2>Selamat datang kembali</h2>
                    <p class="auth-sub">Masuk untuk melanjutkan belanja kebutuhan kesehatanmu.</p>

                    <div class="field">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" placeholder="nama@email.com" value="<?= e($email) ?>"
                            autocomplete="username" required>
                        <span class="field-error" data-error-for="login-email"></span>
                    </div>

                    <div class="field">
                        <label for="login-password">Kata sandi</label>
                        <div class="password-wrap">
                            <input type="password" id="login-password" name="password" placeholder="Masukkan kata sandi"
                                autocomplete="current-password" required minlength="8">
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

                    <div class="form-message<?= $error ? ' is-error' : '' ?>" id="login-message" role="status" aria-live="polite"><?= e($error) ?></div>

                    <p class="switch-text">Belum punya akun? <a href="register.php" class="link-strong">Daftar sekarang</a></p>
                </form>

            </div>
        </main>

    </div>

    <script src="auth.js"></script>
</body>

</html>