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
