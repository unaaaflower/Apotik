CREATE DATABASE IF NOT EXISTS klikmedis CHARACTER SET utf8mb4;
USE klikmedis;

CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(100) NOT NULL UNIQUE,
  nama          VARCHAR(100) NOT NULL,
  no_hp         VARCHAR(20)  NULL,
  tanggal_lahir DATE         NULL,
  password      VARCHAR(255) NOT NULL,         
  role          VARCHAR(10)  NOT NULL DEFAULT 'user',
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

ALTER TABLE users ADD COLUMN IF NOT EXISTS no_hp         VARCHAR(20) NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS tanggal_lahir DATE        NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_at    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL;

INSERT IGNORE INTO users (username, email, nama, password, role) VALUES
  ('admin', 'admin@klikmedis.id', 'Administrator',
   '$2y$10$jgoGo2wHQxy3NsI8Hj8FAuIRXOLm.OHsh1o23YNPdLWWJBn/xYUwC', 'admin'),
  ('andi', 'andi@klikmedis.id', 'Andi Pratama',
   '$2y$10$rXCbOE8ma.whk8CoaY/lu.7/mF71IBQhzj8tyQqjGe3UZTJ.UwwIS', 'user');
