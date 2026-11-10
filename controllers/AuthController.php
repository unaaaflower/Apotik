<?php
require_once __DIR__ . '/Controller.php';

/**
 * CONTROLLER — Registrasi dan Login.
 * Alurnya: terima request -> validasi -> minta data ke Model -> pilih View.
 */
class AuthController extends Controller
{
    private const PESAN_SERVER = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';

    public function login(): void
    {
        if (is_logged_in()) {
            redirect_to(is_admin() ? '../admin/admin-dashboard.php' : 'dashboard.php');
        }

        $error     = '';
        $identitas = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identitas = trim((string) ($_POST['identitas'] ?? ''));
            $password  = (string) ($_POST['password'] ?? '');

            if (!csrf_valid()) {
                $error = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
            } elseif ($identitas === '' || $password === '') {
                $error = 'Email/username dan kata sandi wajib diisi.';
            } else {
                try {
                    $user = $this->user->cariUntukLogin($identitas);

                    if ($user && password_verify($password, $user['password'])) {
                        login_user($user);
                        redirect_to($user['role'] === 'admin' ? '../admin/admin-dashboard.php' : 'dashboard.php');
                    }
                    // Pesan sama untuk "user tidak ada" dan "password salah".
                    $error = 'Email/username atau kata sandi salah.';
                } catch (PDOException $e) {
                    $this->catat($e);
                    $error = self::PESAN_SERVER;
                }
            }
        }

        $this->render('auth/login', [
            'error'     => $error,
            'identitas' => $identitas,
            'flash'     => get_flash('auth'),
        ]);
    }

    public function register(): void
    {
        if (is_logged_in()) {
            redirect_to(is_admin() ? '../admin/admin-dashboard.php' : 'dashboard.php');
        }

        $old    = ['nama' => '', 'username' => '', 'email' => '', 'no_hp' => ''];
        $errors = [];
        $error  = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($old as $kolom => $_) {
                $old[$kolom] = trim((string) ($_POST[$kolom] ?? ''));
            }
            $password   = (string) ($_POST['password'] ?? '');
            $konfirmasi = (string) ($_POST['konfirmasi'] ?? '');

            if (!csrf_valid()) {
                $error = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
            } else {
                $errors = array_filter([
                    'nama'       => validasi_nama($old['nama']),
                    'username'   => validasi_username($old['username']),
                    'email'      => validasi_email($old['email']),
                    'no_hp'      => validasi_no_hp($old['no_hp']),
                    'password'   => validasi_password($password),
                    'konfirmasi' => $konfirmasi !== $password ? 'Konfirmasi kata sandi tidak cocok.' : '',
                    'setuju'     => empty($_POST['setuju']) ? 'Setujui syarat & ketentuan untuk melanjutkan.' : '',
                ]);

                if (!$errors) {
                    try {
                        if ($this->user->usernameTerpakai($old['username'])) {
                            $errors['username'] = 'Username sudah dipakai.';
                        }
                        if ($this->user->emailTerpakai($old['email'])) {
                            $errors['email'] = 'Email sudah terdaftar.';
                        }

                        if (!$errors) {
                            $this->user->buat($old['nama'], $old['username'], $old['email'], $old['no_hp'], $password);
                            set_flash('auth', true, 'Akun berhasil dibuat. Silakan masuk.');
                            redirect_to('login.php');
                        }
                    } catch (PDOException $e) {
                        // SQLSTATE 23000 = pelanggaran UNIQUE (dua orang mendaftar bersamaan)
                        if ((string) $e->getCode() === '23000') {
                            $error = 'Username atau email sudah terdaftar.';
                        } else {
                            $this->catat($e);
                            $error = self::PESAN_SERVER;
                        }
                    }
                }
            }
        }

        $this->render('auth/register', [
            'old'    => $old,
            'errors' => $errors,
            'error'  => $error,
        ]);
    }
}
