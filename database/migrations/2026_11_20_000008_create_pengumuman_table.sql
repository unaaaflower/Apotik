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
