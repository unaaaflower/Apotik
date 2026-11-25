-- Akun contoh. Password disimpan sebagai hash bcrypt.
--   admin / admin12345   (role admin)
--   andi  / user12345    (role user)
-- INSERT IGNORE: dilewati bila username/email yang sama sudah ada.
INSERT IGNORE INTO users (username, email, nama, password, role) VALUES
  ('admin', 'admin@klikmedis.id', 'Administrator',
   '$2y$10$jgoGo2wHQxy3NsI8Hj8FAuIRXOLm.OHsh1o23YNPdLWWJBn/xYUwC', 'admin'),
  ('andi', 'andi@klikmedis.id', 'Andi Pratama',
   '$2y$10$rXCbOE8ma.whk8CoaY/lu.7/mF71IBQhzj8tyQqjGe3UZTJ.UwwIS', 'user');
