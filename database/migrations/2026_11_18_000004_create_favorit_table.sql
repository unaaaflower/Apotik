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
