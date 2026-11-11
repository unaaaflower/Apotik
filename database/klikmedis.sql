-- klikmedis.sql
-- Jalankan lewat phpMyAdmin (tab SQL) atau menu Import.
-- Aman dijalankan berulang kali: tabel yang sudah ada tidak dihapus.

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

-- Akun contoh. Password disimpan sebagai hash bcrypt.
--   admin / admin12345   (role admin)
--   andi  / user12345    (role user)
-- INSERT IGNORE: dilewati bila username/email yang sama sudah ada.
INSERT IGNORE INTO users (username, email, nama, password, role) VALUES
  ('admin', 'admin@klikmedis.id', 'Administrator',
   '$2y$10$jgoGo2wHQxy3NsI8Hj8FAuIRXOLm.OHsh1o23YNPdLWWJBn/xYUwC', 'admin'),
  ('andi', 'andi@klikmedis.id', 'Andi Pratama',
   '$2y$10$rXCbOE8ma.whk8CoaY/lu.7/mF71IBQhzj8tyQqjGe3UZTJ.UwwIS', 'user');
