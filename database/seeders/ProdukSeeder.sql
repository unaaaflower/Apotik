-- Katalog produk awal. Id tetap supaya tombol "+ Keranjang" di halaman produk cocok.
INSERT IGNORE INTO produk (id, nama, ket, satuan, harga) VALUES
  (1, 'Paracetamol 500 mg', '10 Tablet', 'strip', 5000),
  (2, 'Vitamin C 1000 mg',  '10 Tablet', 'botol', 25000),
  (3, 'Minyak Telon',       '60 ml',     'botol', 18000),
  (4, 'Antangin JRG',       '12 Sachet', 'box',   15000),
  (5, 'Cetirizine 10 mg',   '10 Tablet', 'strip', 12000),
  (6, 'Hansaplast Plester', '20 pcs',    'box',   28000);
