<?php

/**
 * Helper khusus aplikasi KlikMedis.
 * Nama fungsi menggunakan awalan km_ untuk menghindari
 * konflik dengan helper bawaan Laravel.
 */

use App\Models\Alamat;
use App\Models\Keranjang;
use App\Support\Database;

/* =========================================================
   KONFIGURASI
========================================================= */

function km_load_env(string $file): void
{
    if (!is_file($file)) {
        return;
    }

    foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $baris) {
        $baris = trim($baris);

        if ($baris === '' || $baris[0] === '#' || strpos($baris, '=') === false) {
            continue;
        }

        [$kunci, $nilai] = explode('=', $baris, 2);
        $nilai = trim($nilai);

        if (
            strlen($nilai) >= 2 &&
            ($nilai[0] === '"' || $nilai[0] === "'") &&
            substr($nilai, -1) === $nilai[0]
        ) {
            $nilai = substr($nilai, 1, -1);
        }

        $_ENV[trim($kunci)] = $nilai;
    }
}

/**
 * Membaca konfigurasi sistem lama KlikMedis.
 */
function km_config(string $kunci, $bawaan = null)
{
    static $cache = [];

    $bagian = explode('.', $kunci);
    $berkas = array_shift($bagian);

    if (!isset($cache[$berkas])) {
        $path = (defined('BASE_PATH') ? BASE_PATH : base_path())
            . '/config/' . $berkas . '.php';

        $cache[$berkas] = is_file($path) ? require $path : [];
    }

    $nilai = $cache[$berkas];

    foreach ($bagian as $b) {
        if (!is_array($nilai) || !array_key_exists($b, $nilai)) {
            return $bawaan;
        }

        $nilai = $nilai[$b];
    }

    return $nilai;
}

/* =========================================================
   URL
========================================================= */

function base_url(): string
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    $skrip = rtrim(
        str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')),
        '/'
    );

    $uri = parse_url(
        $_SERVER['REQUEST_URI'] ?? '/',
        PHP_URL_PATH
    ) ?: '/';

    if (
        $skrip !== '' &&
        $uri !== $skrip &&
        strpos($uri, $skrip . '/') !== 0
    ) {
        $skrip = rtrim(
            str_replace('\\', '/', dirname($skrip)),
            '/'
        );
    }

    return $base = $skrip;
}

function request_path(): string
{
    $uri = rawurldecode(
        parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
    );

    $base = base_url();

    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }

    $path = '/' . trim($uri, '/');

    return $path === '/index.php' ? '/' : $path;
}

/**
 * URL khusus KlikMedis.
 */
function km_url(string $path = '/'): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * URL aset khusus KlikMedis.
 */
function km_asset(string $path): string
{
    $file = (defined('BASE_PATH') ? BASE_PATH : base_path())
        . '/public/' . ltrim($path, '/');

    $versi = is_file($file) ? (int) filemtime($file) : 0;

    return base_url() . '/' . ltrim($path, '/') . '?v=' . $versi;
}

/**
 * URL berdasarkan nama route sistem lama.
 */
function km_route(string $nama): string
{
    return km_url(\App\Support\Route::path($nama));
}

/**
 * Redirect sistem lama.
 */
function redirect_to(string $url): void
{
    if ($url !== '' && $url[0] === '/') {
        $url = base_url() . $url;
    }

    header('Location: ' . $url);
    exit;
}

/**
 * Halaman error sistem lama.
 */
function km_abort(int $kode, string $pesan = ''): void
{
    http_response_code($kode);

    $view = (defined('BASE_PATH') ? BASE_PATH : base_path())
        . '/resources/views/errors/' . $kode . '.php';

    if (is_file($view)) {
        require $view;
    } else {
        echo $pesan !== '' ? km_e($pesan) : 'Terjadi kesalahan.';
    }

    exit;
}

/* =========================================================
   ESCAPE, SESSION, DAN AUTH
========================================================= */

function km_e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool
{
    return is_logged_in()
        && ($_SESSION['role'] ?? '') === 'admin';
}

function login_user(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];
}

function reset_session(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function no_cache_headers(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

function nama_depan(): string
{
    $parts = preg_split('/\s+/', trim($_SESSION['nama'] ?? ''));

    return $parts[0] ?? '';
}

function inisial(): string
{
    $parts = preg_split(
        '/\s+/',
        trim($_SESSION['nama'] ?? ''),
        -1,
        PREG_SPLIT_NO_EMPTY
    );

    $hasil = '';

    foreach (array_slice($parts, 0, 2) as $p) {
        $hasil .= mb_strtoupper(mb_substr($p, 0, 1));
    }

    return $hasil !== '' ? $hasil : '?';
}

/* =========================================================
   CSRF
========================================================= */

/**
 * Token CSRF khusus sistem lama KlikMedis.
 */
function km_csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

/**
 * Membuat input hidden CSRF sistem lama.
 * Tidak menggunakan nama csrf_field() karena sudah
 * tersedia sebagai helper bawaan Laravel.
 */
function km_csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="'
        . km_e(km_csrf_token())
        . '">';
}

function csrf_valid(): bool
{
    $kirim = $_POST['csrf'] ?? '';

    return is_string($kirim)
        && $kirim !== ''
        && hash_equals(km_csrf_token(), $kirim);
}

/* =========================================================
   FLASH MESSAGE
========================================================= */

function set_flash(string $kunci, bool $ok, string $pesan): void
{
    $_SESSION['flash'][$kunci] = [
        'ok' => $ok,
        'pesan' => $pesan
    ];
}

function get_flash(string $kunci): ?array
{
    $flash = $_SESSION['flash'][$kunci] ?? null;

    unset($_SESSION['flash'][$kunci]);

    return $flash;
}

/* =========================================================
   FORMAT
========================================================= */

function rupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function tanggal_id(?int $ts = null): string
{
    $bulan = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $ts = $ts ?? time();

    return date('j', $ts)
        . ' ' . $bulan[(int) date('n', $ts)]
        . ' ' . date('Y', $ts);
}

/* =========================================================
   KERANJANG DAN PENGIRIMAN
========================================================= */

function badge_keranjang(): int
{
    static $hasil = null;

    if ($hasil !== null) {
        return $hasil;
    }

    $hasil = 0;

    if (!is_logged_in()) {
        return $hasil;
    }

    try {
        $hasil = (new Keranjang(Database::connection(false)))
            ->hitungBaris((int) $_SESSION['user_id']);
    } catch (Throwable $e) {
        error_log($e->getMessage());
    }

    return $hasil;
}

function opsi_pengiriman(): array
{
    return [
        10000 => 'JNE Reguler (2-3 hari)',
        15000 => 'JNE YES (1 hari)',
    ];
}

/* =========================================================
   ALAMAT
========================================================= */

function get_alamat(): array
{
    static $cache = [];

    $uid = (int) ($_SESSION['user_id'] ?? 0);

    if (isset($cache[$uid])) {
        return $cache[$uid];
    }

    $nama = $_SESSION['nama'] ?? '';

    try {
        $cache[$uid] = (new Alamat(Database::connection(false)))
            ->semua($uid, $nama);
    } catch (Throwable $e) {
        error_log($e->getMessage());

        return Alamat::bawaan($nama);
    }

    return $cache[$uid];
}

/**
 * Memproses perubahan alamat sistem lama.
 */
function proses_ubah_alamat(string $redirect): void
{
    if (
        ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' ||
        ($_POST['aksi'] ?? '') !== 'ubah_alamat'
    ) {
        return;
    }

    $label = $_POST['label'] ?? '';
    $nama = trim((string) ($_POST['nama'] ?? ''));
    $telp = trim((string) ($_POST['telp'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));

    $alamatSimpan = get_alamat();
    $error = '';

    if (!csrf_valid()) {
        $error = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (!isset($alamatSimpan[$label])) {
        $error = 'Alamat tidak ditemukan.';
    } elseif ($nama === '' || mb_strlen($nama) > 100) {
        $error = 'Nama penerima wajib diisi (maksimal 100 karakter).';
    } elseif (!preg_match('/^[0-9+\s\-]{8,20}$/', $telp)) {
        $error = 'No. HP tidak valid (8–20 karakter, hanya angka, spasi, + atau -).';
    } elseif (mb_strlen($alamat) < 10 || mb_strlen($alamat) > 300) {
        $error = 'Alamat lengkap harus 10–300 karakter.';
    }

    if ($error === '') {
        try {
            (new Alamat(Database::connection(false)))
                ->ubah(
                    (int) $_SESSION['user_id'],
                    $label,
                    $nama,
                    $telp,
                    $alamat
                );

            $_SESSION['flash_alamat'] = [
                'ok' => true,
                'label' => $label,
                'pesan' => 'Alamat berhasil diperbarui.'
            ];
        } catch (Throwable $e) {
            error_log($e->getMessage());

            $_SESSION['flash_alamat'] = [
                'ok' => false,
                'label' => $label,
                'pesan' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
            ];
        }
    } else {
        $_SESSION['flash_alamat'] = [
            'ok' => false,
            'label' => $label,
            'pesan' => $error
        ];
    }

    redirect_to($redirect);
}

function ambil_flash_alamat(): ?array
{
    $flash = $_SESSION['flash_alamat'] ?? null;

    unset($_SESSION['flash_alamat']);

    return $flash;
}
