-- klikmedis.sql — GABUNGAN database/migrations/*.sql lalu database/seeders/*.sql
-- Jalankan lewat phpMyAdmin (tab SQL) atau menu Import. Aman dijalankan berulang kali.

CREATE DATABASE IF NOT EXISTS klikmedis CHARACTER SET utf8mb4;
USE klikmedis;

-- Tabel users: kolom minimal id, username, password, nama
-- ditambah email (login), no_hp, tanggal_lahir, role, created_at.
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(100) NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  no_hp         VARCHAR(20)  NULL,
  tanggal_lahir DATE         NULL,
  password      VARCHAR(255) NOT NULL,          -- hasil password_hash(), bukan teks biasa
  role          VARCHAR(10)  NOT NULL DEFAULT 'user',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Jika tabel users sudah ada dari versi sebelumnya, tambahkan kolom yang belum ada.
-- (MariaDB mendukung ADD COLUMN IF NOT EXISTS; kolom yang sudah ada dilewati.)
ALTER TABLE users ADD COLUMN IF NOT EXISTS no_hp         VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS tanggal_lahir DATE        NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;

-- Katalog produk (sumber data keranjang). Id tetap supaya tombol "+ Keranjang" di halaman produk cocok.
CREATE TABLE IF NOT EXISTS produk (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(100) NOT NULL UNIQUE,
  ket    VARCHAR(60)  NOT NULL,                 -- mis. "10 Tablet"
  satuan VARCHAR(20)  NOT NULL DEFAULT 'strip', -- mis. strip / botol
  harga  INT          NOT NULL
) ENGINE=InnoDB;

-- Keranjang: satu baris per (user, produk). Ikut terhapus bila akun/produk dihapus.
CREATE TABLE IF NOT EXISTS keranjang (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  produk_id  INT NOT NULL,
  jumlah     INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_produk (user_id, produk_id),
  CONSTRAINT fk_keranjang_user   FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
  CONSTRAINT fk_keranjang_produk FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pesanan: hasil checkout. Alamat disalin (snapshot) agar riwayat tidak berubah bila alamat diubah.
CREATE TABLE IF NOT EXISTS pesanan (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  kode        VARCHAR(20)  NOT NULL UNIQUE,      -- mis. SK-48213
  user_id     INT NOT NULL,
  kurir       VARCHAR(50)  NOT NULL,
  ongkir      INT NOT NULL,
  subtotal    INT NOT NULL,
  total       INT NOT NULL,
  metode      VARCHAR(20)  NOT NULL DEFAULT 'cod',
  status      VARCHAR(30)  NOT NULL DEFAULT 'Diproses',
  alamat_nama VARCHAR(100) NOT NULL,
  alamat_telp VARCHAR(20)  NOT NULL,
  alamat_teks VARCHAR(300) NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Rincian barang per pesanan (nama dan harga disalin saat checkout).
CREATE TABLE IF NOT EXISTS pesanan_item (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  pesanan_id INT NOT NULL,
  nama       VARCHAR(100) NOT NULL,
  ket        VARCHAR(60)  NOT NULL,
  harga      INT NOT NULL,
  jumlah     INT NOT NULL,
  CONSTRAINT fk_item_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Favorit: satu baris per (user, produk). Ikut terhapus bila akun/produk dihapus.
-- `catatan` = catatan pribadi pengguna tentang produk favoritnya (opsional, maks 100 karakter).
CREATE TABLE IF NOT EXISTS favorit (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  produk_id  INT NOT NULL,
  catatan    VARCHAR(100) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_favorit_user_produk (user_id, produk_id),
  CONSTRAINT fk_favorit_user   FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
  CONSTRAINT fk_favorit_produk FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Alamat tersimpan per pengguna (label 'utama' dan 'kantor'). Dipakai Profil dan Checkout.
-- Baris awal dibuat otomatis oleh aplikasi saat pertama kali alamat dibuka.
CREATE TABLE IF NOT EXISTS alamat (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  label   VARCHAR(10)  NOT NULL,              -- 'utama' | 'kantor'
  nama    VARCHAR(100) NOT NULL,
  telp    VARCHAR(20)  NOT NULL,
  alamat  VARCHAR(300) NOT NULL,
  UNIQUE KEY uq_alamat_user_label (user_id, label),
  CONSTRAINT fk_alamat_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Promo: kode diskon yang dikelola admin (Kelola Promo). Satu baris per promo.
-- jenis  : 'persen' (nilai 1-100) atau 'nominal' (nilai dalam rupiah)
-- target : kelompok pengguna yang boleh memakai promo
-- aktif  : 1 = berlaku, 0 = dihentikan sementara
CREATE TABLE IF NOT EXISTS promo (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  kode       VARCHAR(20)  NOT NULL,
  jenis      VARCHAR(10)  NOT NULL DEFAULT 'persen',
  nilai      INT          NOT NULL,
  minimal    INT          NOT NULL DEFAULT 0,
  target     VARCHAR(30)  NOT NULL DEFAULT 'Semua Pengguna',
  mulai      DATE         NOT NULL,
  selesai    DATE         NOT NULL,
  aktif      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_promo_kode (kode)
) ENGINE=InnoDB;

-- Akun contoh. Password disimpan sebagai hash bcrypt.
--   admin / admin12345   (role admin)
--   andi  / user12345    (role user)
-- INSERT IGNORE: dilewati bila username/email yang sama sudah ada.
INSERT IGNORE INTO users (username, email, nama, password, role) VALUES
  ('admin', 'admin@klikmedis.id', 'Administrator',
   '$2y$10$jgoGo2wHQxy3NsI8Hj8FAuIRXOLm.OHsh1o23YNPdLWWJBn/xYUwC', 'admin'),
  ('andi', 'andi@klikmedis.id', 'Andi Pratama',
   '$2y$10$rXCbOE8ma.whk8CoaY/lu.7/mF71IBQhzj8tyQqjGe3UZTJ.UwwIS', 'user');

-- Katalog produk awal. Id tetap supaya tombol "+ Keranjang" di halaman produk cocok.
INSERT IGNORE INTO produk (id, nama, ket, satuan, harga) VALUES
  (1, 'Paracetamol 500 mg', '10 Tablet', 'strip', 5000),
  (2, 'Vitamin C 1000 mg',  '10 Tablet', 'botol', 25000),
  (3, 'Minyak Telon',       '60 ml',     'botol', 18000),
  (4, 'Antangin JRG',       '12 Sachet', 'box',   15000),
  (5, 'Cetirizine 10 mg',   '10 Tablet', 'strip', 12000),
  (6, 'Hansaplast Plester', '20 pcs',    'box',   28000);

-- Data awal promo. INSERT IGNORE: dilewati bila kode yang sama sudah ada.
INSERT IGNORE INTO promo (nama, kode, jenis, nilai, minimal, target, mulai, selesai, aktif) VALUES
  ('Diskon Spesial Vitamin',     'SEHAT30',      'persen',  30, 20000, 'Semua Pengguna', '2026-11-01', '2026-11-30', 1),
  ('Gratis Ongkir',              'GRATISONGKIR', 'nominal', 10000, 50000, 'Semua Pengguna', '2026-11-10', '2026-12-10', 1),
  ('Selamat Datang Member Baru', 'WELCOME15',    'persen',  15, 0,     'Member Baru',     '2026-10-01', '2026-12-31', 1),
  ('Promo Ramadan',              'RAMADAN20',    'persen',  20, 30000, 'Semua Pengguna', '2026-03-01', '2026-03-31', 0);

-- ===== Fitur individu: Pengingat Obat & Pengumuman (Dashboard Admin) =====

-- Pengingat Obat: jadwal minum obat milik tiap pengguna (fitur Pengingat Obat). Satu baris per resep.
-- frekuensi     : berapa kali minum per hari (1-4); jam minum dihitung dari jam_mulai
-- tanggal_mulai : hari pertama minum; sisa hari = durasi_hari - hari yang sudah berjalan
-- aktif         : 1 = pengingat berjalan, 0 = dijeda
CREATE TABLE IF NOT EXISTS pengingat_obat (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT          NOT NULL,
  nama          VARCHAR(100) NOT NULL,
  dosis         VARCHAR(50)  NOT NULL,
  frekuensi     TINYINT      NOT NULL DEFAULT 2,
  jam_mulai     TIME         NOT NULL DEFAULT '07:00:00',
  durasi_hari   SMALLINT     NOT NULL DEFAULT 5,
  tanggal_mulai DATE         NOT NULL,
  dokter        VARCHAR(100) NULL,
  catatan       VARCHAR(200) NULL,
  aktif         TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_pengingat_user (user_id),
  CONSTRAINT fk_pengingat_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pengumuman: pesan dari admin (dikelola di Dashboard Admin) yang tampil di Beranda pengguna.
-- tipe  : 'info', 'penting', atau 'promo' (menentukan warna label)
-- aktif : 1 = tampil ke pengguna, 0 = disembunyikan
CREATE TABLE IF NOT EXISTS pengumuman (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  judul      VARCHAR(100) NOT NULL,
  isi        VARCHAR(500) NOT NULL,
  tipe       VARCHAR(10)  NOT NULL DEFAULT 'info',
  aktif      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Data awal pengumuman (hanya bila tabel masih kosong).
INSERT INTO pengumuman (judul, isi, tipe, aktif)
SELECT 'Selamat datang di KlikMedis', 'Belanja obat dan kebutuhan kesehatan jadi lebih mudah. Coba juga fitur Pengingat Obat agar tidak lupa minum obat.', 'info', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM pengumuman);
