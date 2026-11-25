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
