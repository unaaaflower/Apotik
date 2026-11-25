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
