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
