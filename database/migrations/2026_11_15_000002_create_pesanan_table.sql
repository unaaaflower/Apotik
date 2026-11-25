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
