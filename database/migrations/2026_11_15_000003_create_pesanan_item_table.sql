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
