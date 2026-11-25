-- Data awal promo. INSERT IGNORE: dilewati bila kode yang sama sudah ada.
INSERT IGNORE INTO promo (nama, kode, jenis, nilai, minimal, target, mulai, selesai, aktif) VALUES
  ('Diskon Spesial Vitamin',     'SEHAT30',      'persen',  30, 20000, 'Semua Pengguna', '2026-11-01', '2026-11-30', 1),
  ('Gratis Ongkir',              'GRATISONGKIR', 'nominal', 10000, 50000, 'Semua Pengguna', '2026-11-10', '2026-12-10', 1),
  ('Selamat Datang Member Baru', 'WELCOME15',    'persen',  15, 0,     'Member Baru',     '2026-10-01', '2026-12-31', 1),
  ('Promo Ramadan',              'RAMADAN20',    'persen',  20, 30000, 'Semua Pengguna', '2026-03-01', '2026-03-31', 0);
