<?php
require_once __DIR__ . '/Controller.php';

/**
 * CONTROLLER — halaman Profil: Ubah Profil, Ubah Kata Sandi, Hapus Akun.
 * Satu halaman, tiga form; field tersembunyi `aksi` menentukan form mana yang dikirim.
 * Bila berhasil -> Post/Redirect/Get. Bila gagal -> halaman dirender ulang dengan pesan dan isian lama.
 */
class ProfilController extends Controller
{
    private const PESAN_SERVER = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';

    private int $id;

    /** Pesan per bagian: ['profil' => ['ok' => bool, 'pesan' => string], 'password' => ..., 'hapus' => ...] */
    private array $pesan = [];

    /** Isian form profil yang ditampilkan ulang saat validasi gagal. */
    private array $isian = [];

    public function invoke(): void
    {
        $this->id = (int) $_SESSION['user_id'];

        // Fitur lama (alamat tersimpan di session) tetap dipakai apa adanya.
        proses_ubah_alamat('profil.php#tab-alamat');

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->prosesForm();
            }

            $user = $this->user->cariById($this->id);
        } catch (PDOException $e) {
            $this->catat($e);
            http_response_code(500);
            die('Terjadi kesalahan pada server.');
        }

        // Akun sudah tidak ada di database (mis. dihapus dari tempat lain).
        if (!$user) {
            reset_session();
            set_flash('auth', false, 'Akun tidak ditemukan. Silakan masuk kembali.');
            redirect_to('login.php');
        }

        foreach (['profil', 'password', 'hapus'] as $bagian) {
            $this->pesan[$bagian] ??= get_flash('profil_' . $bagian);
        }

        $this->render('user/profil', [
            'user'        => $user,
            'isian'       => $this->isian + [
                'nama'          => $user['nama'],
                'username'      => $user['username'],
                'email'         => $user['email'],
                'no_hp'         => (string) $user['no_hp'],
                'tanggal_lahir' => (string) $user['tanggal_lahir'],
            ],
            'pesan'       => $this->pesan,
            'alamatList'  => get_alamat(),
            'flashAlamat' => ambil_flash_alamat(),
            'flashPesanan' => get_flash('pesanan'),
            'pesananBaru' => $_SESSION['pesanan'] ?? [],
        ]);
    }

    private function prosesForm(): void
    {
        $aksi = $_POST['aksi'] ?? '';
        if (!in_array($aksi, ['ubah_profil', 'ubah_password', 'hapus_akun'], true)) {
            return; // mis. aksi=ubah_alamat sudah ditangani proses_ubah_alamat()
        }

        if (!csrf_valid()) {
            $bagian = ['ubah_profil' => 'profil', 'ubah_password' => 'password', 'hapus_akun' => 'hapus'][$aksi];
            $this->pesan[$bagian] = ['ok' => false, 'pesan' => 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.'];
            return;
        }

        match ($aksi) {
            'ubah_profil'   => $this->ubahProfil(),
            'ubah_password' => $this->ubahPassword(),
            'hapus_akun'    => $this->hapusAkun(),
        };
    }

    private function ubahProfil(): void
    {
        $this->isian = [
            'nama'          => trim((string) ($_POST['nama'] ?? '')),
            'username'      => trim((string) ($_POST['username'] ?? '')),
            'email'         => trim((string) ($_POST['email'] ?? '')),
            'no_hp'         => trim((string) ($_POST['no_hp'] ?? '')),
            'tanggal_lahir' => trim((string) ($_POST['tanggal_lahir'] ?? '')),
        ];
        $in = $this->isian;

        $error = validasi_nama($in['nama'])
            ?: validasi_username($in['username'])
            ?: validasi_email($in['email'])
            ?: validasi_no_hp($in['no_hp'], false)
            ?: validasi_tanggal_lahir($in['tanggal_lahir']);

        if ($error === '') {
            if ($this->user->usernameTerpakai($in['username'], $this->id)) {
                $error = 'Username sudah dipakai pengguna lain.';
            } elseif ($this->user->emailTerpakai($in['email'], $this->id)) {
                $error = 'Email sudah dipakai pengguna lain.';
            }
        }

        if ($error === '') {
            try {
                $this->user->ubahProfil(
                    $this->id, $in['nama'], $in['username'], $in['email'],
                    $in['no_hp'], $in['tanggal_lahir'] !== '' ? $in['tanggal_lahir'] : null
                );
            } catch (PDOException $e) {
                if ((string) $e->getCode() !== '23000') {
                    throw $e;
                }
                $error = 'Username atau email sudah dipakai pengguna lain.';
            }
        }

        if ($error !== '') {
            $this->pesan['profil'] = ['ok' => false, 'pesan' => $error];
            return;
        }

        // Samakan data di session dengan database.
        $_SESSION['nama']     = $in['nama'];
        $_SESSION['username'] = strtolower($in['username']);
        $_SESSION['email']    = strtolower($in['email']);

        set_flash('profil_profil', true, 'Profil berhasil diperbarui.');
        redirect_to('profil.php');
    }

    private function ubahPassword(): void
    {
        $lama       = (string) ($_POST['password_lama'] ?? '');
        $baru       = (string) ($_POST['password_baru'] ?? '');
        $konfirmasi = (string) ($_POST['konfirmasi'] ?? '');

        $hash = $this->user->ambilHash($this->id);

        if ($hash === null || !password_verify($lama, $hash)) {
            $error = 'Kata sandi saat ini salah.';
        } elseif ($error = validasi_password($baru)) {
            // pesan dari validasi_password()
        } elseif ($baru !== $konfirmasi) {
            $error = 'Konfirmasi kata sandi baru tidak cocok.';
        } elseif ($baru === $lama) {
            $error = 'Kata sandi baru harus berbeda dari yang lama.';
        } else {
            $this->user->ubahPassword($this->id, $baru);
            session_regenerate_id(true);
            set_flash('profil_password', true, 'Kata sandi berhasil diubah.');
            redirect_to('profil.php#tab-keamanan');
        }

        $this->pesan['password'] = ['ok' => false, 'pesan' => $error];
    }

    private function hapusAkun(): void
    {
        $password = (string) ($_POST['password'] ?? '');
        $hash     = $this->user->ambilHash($this->id);

        if ($hash === null || !password_verify($password, $hash)) {
            $error = 'Kata sandi salah. Akun tidak dihapus.';
        } elseif (is_admin() && $this->user->hitungAdmin() <= 1) {
            $error = 'Ini satu-satunya akun admin, jadi tidak bisa dihapus.';
        } elseif (!$this->user->hapus($this->id)) {
            $error = 'Akun tidak ditemukan.';
        } else {
            reset_session();
            set_flash('auth', true, 'Akun kamu sudah dihapus.');
            redirect_to('login.php');
        }

        $this->pesan['hapus'] = ['ok' => false, 'pesan' => $error];
    }
}
