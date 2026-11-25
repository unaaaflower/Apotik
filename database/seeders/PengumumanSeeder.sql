-- Data awal pengumuman (hanya bila tabel masih kosong).
INSERT INTO pengumuman (judul, isi, tipe, aktif)
SELECT 'Selamat datang di KlikMedis', 'Belanja obat dan kebutuhan kesehatan jadi lebih mudah. Coba juga fitur Pengingat Obat agar tidak lupa minum obat.', 'info', 1
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM pengumuman);
