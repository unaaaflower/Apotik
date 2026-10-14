<?php
/**
 * Session + proteksi halaman.
 * Harus di-require di BARIS PALING ATAS halaman, sebelum output HTML apa pun.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Alamat dasar proyek, mis. "/PROJECT_PEMWEB" (semua halaman berada satu folder di bawahnya). */
function base_url(): string
{
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
    return $base;
}

/** Escape output HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return !empty($_SESSION['email']);
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

/** Nama depan, mis. "Andi Pratama" -> "Andi". */
function nama_depan(): string
{
    $parts = preg_split('/\s+/', trim($_SESSION['nama'] ?? ''));
    return $parts[0] ?? '';
}

/** Inisial untuk avatar, mis. "Andi Pratama" -> "AP". */
function inisial(): string
{
    $parts = preg_split('/\s+/', trim($_SESSION['nama'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $hasil = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $hasil .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $hasil !== '' ? $hasil : '?';
}

function redirect_to(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function no_cache_headers(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

/** Halaman untuk user yang sudah login (user maupun admin). */
function require_login(): void
{
    no_cache_headers();
    if (!is_logged_in()) {
        redirect_to(base_url() . '/user/login.php');
    }
}

/** Halaman khusus admin. User biasa ditolak (403). */
function require_admin(): void
{
    no_cache_headers();
    if (!is_logged_in()) {
        redirect_to(base_url() . '/user/login.php');
    }
    if (!is_admin()) {
        http_response_code(403);
        $home = e(base_url() . '/user/dashboard.php');
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Akses ditolak</title>'
           . '<meta name="viewport" content="width=device-width, initial-scale=1"></head>'
           . '<body style="font-family:sans-serif;text-align:center;padding:80px 20px">'
           . '<h1>403 — Akses ditolak</h1><p>Halaman ini hanya untuk admin.</p>'
           . '<p><a href="' . $home . '">Kembali ke dashboard</a></p></body></html>';
        exit;
    }
}

/* =========================================================
 * Helper tambahan: format, alamat pengiriman, pesanan (sementara di session)
 * ========================================================= */

/** Format rupiah, mis. 58000 -> "Rp 58.000". */
function rupiah(int $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/** Tanggal Indonesia, mis. "1 Oktober 2026". */
function tanggal_id(?int $ts = null): string
{
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
              'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = $ts ?? time();
    return date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
}

/** Daftar barang checkout (sama dengan yang tampil di checkout.php). */
function checkout_items(): array
{
    return [
        ['nama' => 'Paracetamol 500 mg', 'ket' => '10 Tablet × 1', 'harga' => 5000],
        ['nama' => 'Vitamin C 1000 mg',  'ket' => '10 Tablet × 1', 'harga' => 25000],
        ['nama' => 'Minyak Telon',       'ket' => '60 ml × 1',     'harga' => 18000],
    ];
}

/** Pilihan ongkos kirim yang valid (nilai => label). */
function opsi_pengiriman(): array
{
    return [
        10000 => 'JNE Reguler (2-3 hari)',
        15000 => 'JNE YES (1 hari)',
    ];
}

/**
 * Alamat tersimpan milik user yang sedang login.
 * Disimpan di session dulu; nanti cukup diganti query/UPDATE ke database.
 */
function get_alamat(): array
{
    if (empty($_SESSION['alamat'])) {
        $nama = $_SESSION['nama'] ?? '';
        $_SESSION['alamat'] = [
            'utama'  => [
                'nama'   => $nama,
                'telp'   => '0812 3456 7890',
                'alamat' => 'Jl. Melati No. 12, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141',
            ],
            'kantor' => [
                'nama'   => $nama,
                'telp'   => '0812 3456 7890',
                'alamat' => 'Jl. Ijen No. 45, Kec. Klojen, Kota Malang, Jawa Timur 65119',
            ],
        ];
    }
    return $_SESSION['alamat'];
}

/**
 * Proses form "Ubah alamat" (POST aksi=ubah_alamat).
 * Dipanggil di baris atas checkout.php dan profil.php. Memakai pola
 * Post/Redirect/Get: hasilnya disimpan sebagai pesan flash lalu halaman dimuat ulang.
 */
function proses_ubah_alamat(string $redirect): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['aksi'] ?? '') !== 'ubah_alamat') {
        return;
    }

    $label  = $_POST['label'] ?? '';
    $nama   = trim((string) ($_POST['nama'] ?? ''));
    $telp   = trim((string) ($_POST['telp'] ?? ''));
    $alamat = trim((string) ($_POST['alamat'] ?? ''));

    $alamatSimpan = get_alamat();
    $error = '';

    if (!isset($alamatSimpan[$label])) {
        $error = 'Alamat tidak ditemukan.';
    } elseif ($nama === '' || mb_strlen($nama) > 100) {
        $error = 'Nama penerima wajib diisi (maksimal 100 karakter).';
    } elseif (!preg_match('/^[0-9+\s\-]{8,20}$/', $telp)) {
        $error = 'No. HP tidak valid (8–20 karakter, hanya angka, spasi, + atau -).';
    } elseif (mb_strlen($alamat) < 10 || mb_strlen($alamat) > 300) {
        $error = 'Alamat lengkap harus 10–300 karakter.';
    }

    if ($error === '') {
        $_SESSION['alamat'][$label] = ['nama' => $nama, 'telp' => $telp, 'alamat' => $alamat];
        $_SESSION['flash_alamat'] = ['ok' => true, 'label' => $label, 'pesan' => 'Alamat berhasil diperbarui.'];
    } else {
        $_SESSION['flash_alamat'] = ['ok' => false, 'label' => $label, 'pesan' => $error];
    }

    redirect_to($redirect);
}

/** Ambil (sekali pakai) pesan flash hasil ubah alamat. */
function ambil_flash_alamat(): ?array
{
    $flash = $_SESSION['flash_alamat'] ?? null;
    unset($_SESSION['flash_alamat']);
    return $flash;
}
