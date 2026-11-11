# KlikMedis — MVC + tabel users (PDO)

## Cara menjalankan (XAMPP)
1. Start **Apache** dan **MySQL**.
2. Import `database/klikmedis.sql` lewat phpMyAdmin (aman dijalankan ulang, tabel lama tidak dihapus).
3. Letakkan folder ini di `htdocs/PROJECT_PEMWEB`, buka `http://localhost/PROJECT_PEMWEB/user/login.php`.
4. Akun contoh: `admin` / `admin12345` (admin), `andi` / `user12345` (user).
   Kredensial database ada di `config/koneksi.php`.

## Struktur MVC
| Lapisan | Berkas | Tugas |
|---|---|---|
| Model | `models/User.php` | Semua query tabel `users` (PDO prepared statement) |
| View | `views/auth/*.php`, `views/user/profil.php`, `views/partials/` | Hanya menampilkan HTML |
| Controller | `controllers/AuthController.php`, `ProfilController.php` (turunan `Controller.php`) | Validasi, panggil Model, pilih View |
| Koneksi | `config/koneksi.php` | Satu koneksi PDO, dipanggil dengan `require` |

`user/login.php`, `register.php`, `profil.php` kini hanya titik masuk tipis:
buat controller, lalu panggil method-nya. URL lama tidak berubah, jadi halaman lain tetap jalan.

## Fitur
Registrasi, Login (email atau username), Ubah Profil, Ubah Kata Sandi, Hapus Akun.
Password memakai `password_hash()` / `password_verify()`. Error database dicatat via `error_log()`, pengguna hanya melihat pesan umum. Semua form POST dilindungi token CSRF.
