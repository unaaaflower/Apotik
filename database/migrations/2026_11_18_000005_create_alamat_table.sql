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
