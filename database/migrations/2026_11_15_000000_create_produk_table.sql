-- Katalog produk (sumber data keranjang). Id tetap supaya tombol "+ Keranjang" di halaman produk cocok.
CREATE TABLE IF NOT EXISTS produk (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nama   VARCHAR(100) NOT NULL UNIQUE,
  ket    VARCHAR(60)  NOT NULL,                 -- mis. "10 Tablet"
  satuan VARCHAR(20)  NOT NULL DEFAULT 'strip', -- mis. strip / botol
  harga  INT          NOT NULL
) ENGINE=InnoDB;
