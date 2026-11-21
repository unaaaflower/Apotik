
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kelola Obat — Admin KlikMedis</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <!-- Main -->
        <div>
            @include('partials.admin-header', [
                'aktifAdmin' => 'obat',
                'judul' => 'Kelola Obat',
                'breadcrumb' => 'Dashboard / Kelola Obat'
            ])

            <main class="admin-main">

                <div class="admin-page-heading">
                    <div>
                        <h1>Daftar Obat &amp; Produk</h1>
                        <p>Tambah, ubah, atau hapus obat dan produk kesehatan yang dijual.</p>
                    </div>
                </div>

                <div class="admin-card">

                    <div class="admin-toolbar">
                        <div class="admin-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            <input type="text" id="obat-search" placeholder="Cari nama obat...">
                        </div>

                        <select class="admin-filter" id="obat-filter-kategori">
                            <option value="">Semua Kategori</option>
                            <option value="Obat Resep">Obat Resep</option>
                            <option value="Obat Bebas">Obat Bebas</option>
                            <option value="Vitamin & Suplemen">Vitamin &amp; Suplemen</option>
                            <option value="Alat Kesehatan">Alat Kesehatan</option>
                        </select>

                        <button type="button" class="admin-btn-primary" id="btn-tambah-obat">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            </svg>
                            Tambah Obat
                        </button>
                    </div>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Resep</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="obat-tbody">
                                <!-- Rows are rendered by admin.js -->
                            </tbody>
                        </table>
                    </div>

                </div>

            </main>
        </div>

    </div>

    <!-- Modal: Tambah / Ubah Obat -->
    <div class="admin-modal-overlay" id="obat-modal-overlay">
        <div class="admin-modal">
            <div class="admin-modal-head">
                <div>
                    <h2 id="obat-modal-title">Tambah Obat</h2>
                    <p>Lengkapi detail produk di bawah ini.</p>
                </div>

                <button type="button" class="admin-modal-close" id="obat-modal-close" aria-label="Tutup">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <form id="obat-form">
                <input type="hidden" id="obat-id">

                <div class="admin-field">
                    <label for="obat-nama">Nama Obat</label>
                    <input type="text" id="obat-nama" placeholder="Contoh: Paracetamol 500 mg" required>
                </div>


                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="obat-kategori">Kategori</label>
                        <select id="obat-kategori">
                            <option value="Obat Resep">Obat Resep</option>
                            <option value="Obat Bebas">Obat Bebas</option>
                            <option value="Vitamin & Suplemen">Vitamin &amp; Suplemen</option>
                            <option value="Perawatan Diri">Perawatan Diri</option>
                            <option value="Alat Kesehatan">Alat Kesehatan</option>
                        </select>
                    </div>

                    <div class="admin-field">
                        <label for="obat-satuan">Satuan</label>
                        <input type="text" id="obat-satuan" placeholder="Contoh: 10 Tablet">
                    </div>
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="obat-harga">Harga (Rp)</label>
                        <input type="number" id="obat-harga" min="0" placeholder="0" required>
                    </div>

                    <div class="admin-field">
                        <label for="obat-stok">Stok</label>
                        <input type="number" id="obat-stok" min="0" placeholder="0" required>
                    </div>
                </div>

                <div class="admin-field">
                    <label for="obat-deskripsi">Deskripsi</label>
                    <textarea id="obat-deskripsi" placeholder="Deskripsi singkat produk"></textarea>
                </div>

<div class="admin-field">
    <label for="obat-foto">Foto Obat</label>

    <input
        type="file"
        id="obat-foto"
        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
    >

    <small class="admin-upload-hint">
        Format JPG, PNG, atau WEBP. Maksimal 2 MB.
        Disarankan foto persegi agar pas di Dashboard.
    </small>

    <div class="admin-upload-preview" id="obat-foto-preview-wrap" hidden>
        <img id="obat-foto-preview" alt="Pratinjau foto obat">
        <span id="obat-foto-preview-label">Pratinjau foto</span>
    </div>
</div>


                <div class="admin-toggle-row">
                    <div>
                        <div class="toggle-label">Butuh Resep Dokter</div>
                        <div class="toggle-hint">Aktifkan jika produk memerlukan resep untuk dibeli.</div>
                    </div>

                    <label class="toggle-switch">
                        <input type="checkbox" id="obat-butuh-resep">
                        <span class="track"></span>
                    </label>
                </div>

                <div class="admin-toggle-row">
                    <div>
                        <div class="toggle-label">Status Aktif</div>
                        <div class="toggle-hint">Nonaktifkan untuk menyembunyikan dari katalog.</div>
                    </div>

                    <label class="toggle-switch">
                        <input type="checkbox" id="obat-status" checked>
                        <span class="track"></span>
                    </label>
                </div>

                <div class="admin-modal-actions">
                    <button type="button" class="admin-btn-ghost" id="obat-modal-cancel">
                        Batal
                    </button>

                    <button type="submit" class="admin-btn-primary" id="obat-modal-submit">
                        Simpan Obat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast feedback -->
    <div class="admin-toast" id="admin-toast" role="status" aria-live="polite"></div>

    <script src="{{ asset('js/admin.js') }}"></script>

</body>
</html>
