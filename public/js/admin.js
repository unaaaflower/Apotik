
// =========================================================
// KELOLA OBAT — CRUD melalui Laravel dan database
// =========================================================

// =========================================================
// TOAST HELPER — Pesan notifikasi admin
// =========================================================

function showToast(message, isDanger = false) {
    const toast = document.getElementById('admin-toast');

    if (!toast) {
        console.warn(message);
        return;
    }

    toast.textContent = message;
    toast.classList.toggle('is-danger', Boolean(isDanger));
    toast.classList.add('is-visible');

    clearTimeout(window.adminToastTimer);

    window.adminToastTimer = setTimeout(() => {
        toast.classList.remove('is-visible');
    }, 2400);
}

// =========================================================
// FORMAT HARGA RUPIAH
// =========================================================


const obatTbody = document.getElementById('obat-tbody');

function formatRupiah(angka) {
    return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
}

if (obatTbody) {
    let obatList = [];

    const searchInput = document.getElementById('obat-search');
    const filterKategori = document.getElementById('obat-filter-kategori');
    const overlay = document.getElementById('obat-modal-overlay');
    const form = document.getElementById('obat-form');
    const modalTitle = document.getElementById('obat-modal-title');
    const baseUrl = document.querySelector('meta[name="base-url"]')?.content || '';
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const endpoint = `${baseUrl.replace(/\/$/, '')}/admin/obat`;

    function stokBadge(stok) {
        if (stok <= 5) return '<span class="badge danger">Stok Kritis</span>';
        if (stok <= 10) return '<span class="badge warning">Menipis</span>';
        return '<span class="badge success">Tersedia</span>';
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function renderObat() {
        const q = (searchInput?.value || '').toLowerCase().trim();
        const kategori = filterKategori?.value || '';

        const filtered = obatList.filter(o => {
            const matchQ = !q || String(o.nama || '').toLowerCase().includes(q);
            const matchKategori = !kategori || o.kategori === kategori;
            return matchQ && matchKategori;
        });

        if (!filtered.length) {
            obatTbody.innerHTML = '<tr class="admin-empty-row"><td colspan="7">Tidak ada obat yang cocok.</td></tr>';
            return;
        }

        obatTbody.innerHTML = filtered.map(o => {
            const butuhResep = Number(o.butuh_resep) === 1 || o.butuh_resep === true;
            const aktif = Number(o.aktif) === 1 || o.aktif === true;
            const stok = Number(o.stok || 0);

            return `
                <tr data-id="${Number(o.id)}">
                    <td>
                        <div class="admin-cell-product">
                            <span class="admin-cell-thumb">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/>
                                    <path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <div>
                                <strong>${escapeHtml(o.nama)}</strong>
                                <span>${escapeHtml(o.satuan || '-')}</span>
                            </div>
                        </div>
                    </td>
                    <td>${escapeHtml(o.kategori || '-')}</td>
                    <td>${formatRupiah(o.harga)}</td>
                    <td>${stok}</td>
                    <td>
                        ${butuhResep
                            ? '<span class="badge warning">Wajib Resep</span>'
                            : '<span class="badge neutral">Bebas</span>'}
                    </td>
                    <td>
                        ${aktif
                            ? '<span class="badge success">Aktif</span>'
                            : '<span class="badge neutral">Nonaktif</span>'}
                        ${stokBadge(stok)}
                    </td>
                    <td>
                        <div class="admin-row-actions">
                            <button type="button" class="admin-icon-action"
                                data-edit-obat="${Number(o.id)}" aria-label="Ubah obat">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button type="button" class="admin-icon-action danger"
                                data-delete-obat="${Number(o.id)}" aria-label="Hapus obat">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13"
                                        stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function requestObat(url, options = {}) {
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          ...(options.body && !(options.body instanceof FormData)
    ? { 'Content-Type': 'application/json' }
    : {}),
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            ...(options.headers || {})
        };

        const response = await fetch(url, {
            ...options,
            headers,
            credentials: 'same-origin'
        });

        let result = {};

        try {
            result = await response.json();
        } catch (error) {
            throw new Error('Respons server tidak dapat dibaca. Periksa kembali Controller Laravel.');
        }

        if (!response.ok) {
            const validationMessages = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : '';

            throw new Error(validationMessages || result.message || 'Permintaan gagal diproses.');
        }

        return result;
    }

    async function muatObat() {
        obatTbody.innerHTML = '<tr class="admin-empty-row"><td colspan="7">Memuat data obat...</td></tr>';

        try {
            const result = await requestObat(endpoint);
            obatList = Array.isArray(result.data) ? result.data : [];
            renderObat();
        } catch (error) {
            obatTbody.innerHTML = '<tr class="admin-empty-row"><td colspan="7">Data obat gagal dimuat.</td></tr>';
            showToast(error.message, true);
        }
    }

    function openObatModal(obat) {
        form.reset();

        if (obat) {
            modalTitle.textContent = 'Ubah Obat';
            document.getElementById('obat-id').value = obat.id;
            document.getElementById('obat-nama').value = obat.nama || '';
            document.getElementById('obat-kategori').value = obat.kategori || 'Obat Bebas';
            document.getElementById('obat-satuan').value = obat.satuan || '';
            document.getElementById('obat-harga').value = obat.harga ?? 0;
            document.getElementById('obat-stok').value = obat.stok ?? 0;
            document.getElementById('obat-deskripsi').value = obat.deskripsi || '';

            document.getElementById('obat-butuh-resep').checked =
                Number(obat.butuh_resep) === 1 || obat.butuh_resep === true;

            document.getElementById('obat-status').checked =
                Number(obat.aktif) === 1 || obat.aktif === true;
        } else {
            modalTitle.textContent = 'Tambah Obat';
            document.getElementById('obat-id').value = '';
            document.getElementById('obat-status').checked = true;
        }

        overlay.classList.add('is-open');
    }

    function closeObatModal() {
        overlay.classList.remove('is-open');
    }

    document.getElementById('btn-tambah-obat')?.addEventListener('click', () => openObatModal(null));

const fotoInput = document.getElementById('obat-foto');
const fotoPreviewWrap = document.getElementById('obat-foto-preview-wrap');
const fotoPreview = document.getElementById('obat-foto-preview');

let fotoPreviewUrl = null;

fotoInput?.addEventListener('change', () => {
    const file = fotoInput.files[0];

    if (fotoPreviewUrl) {
        URL.revokeObjectURL(fotoPreviewUrl);
        fotoPreviewUrl = null;
    }

    if (!file) {
        fotoPreviewWrap.hidden = true;
        fotoPreview.removeAttribute('src');
        return;
    }

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        showToast('Format foto harus JPG, PNG, atau WEBP.', true);
        fotoInput.value = '';
        fotoPreviewWrap.hidden = true;
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        showToast('Ukuran foto maksimal 2 MB.', true);
        fotoInput.value = '';
        fotoPreviewWrap.hidden = true;
        return;
    }

    fotoPreviewUrl = URL.createObjectURL(file);
    fotoPreview.src = fotoPreviewUrl;
    fotoPreviewWrap.hidden = false;
});

    document.getElementById('obat-modal-close')?.addEventListener('click', closeObatModal);
    document.getElementById('obat-modal-cancel')?.addEventListener('click', closeObatModal);

    overlay?.addEventListener('click', (e) => {
        if (e.target === overlay) closeObatModal();
    });

    
form?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('obat-id').value;
    const deskripsi = document.getElementById('obat-deskripsi').value.trim();
    const fotoInput = document.getElementById('obat-foto');
    const foto = fotoInput?.files[0];

    const data = new FormData();

    data.append('nama', document.getElementById('obat-nama').value.trim());
    data.append('kategori', document.getElementById('obat-kategori').value);
    data.append('satuan', document.getElementById('obat-satuan').value.trim());
    data.append('harga', document.getElementById('obat-harga').value);
    data.append('stok', document.getElementById('obat-stok').value);
    data.append('deskripsi', deskripsi);
    data.append('ket', deskripsi.substring(0, 60));
    data.append(
        'butuh_resep',
        document.getElementById('obat-butuh-resep').checked ? 1 : 0
    );
    data.append(
        'aktif',
        document.getElementById('obat-status').checked ? 1 : 0
    );

    if (foto) {
        data.append('foto', foto);
    }

    if (!id && !foto) {
        showToast('Foto obat wajib diunggah.', true);
        return;
    }

    if (foto && foto.size > 2 * 1024 * 1024) {
        showToast('Ukuran foto maksimal 2 MB.', true);
        return;
    }

    if (foto && !['image/jpeg', 'image/png', 'image/webp'].includes(foto.type)) {
        showToast('Format foto harus JPG, PNG, atau WEBP.', true);
        return;
    }

    const submitButton = document.getElementById('obat-modal-submit');
    submitButton.disabled = true;

    try {
        if (id) {
            data.append('_method', 'PUT');
        }

        await requestObat(id ? `${endpoint}/${id}` : endpoint, {
            method: 'POST',
            body: data
        });

        closeObatModal();
        showToast(id ? 'Obat berhasil diperbarui.' : 'Obat berhasil ditambahkan.');
        await muatObat();

    } catch (error) {
        showToast(error.message, true);
    } finally {
        submitButton.disabled = false;
    }
});


    obatTbody.addEventListener('click', async (e) => {
        const editId = e.target.closest('[data-edit-obat]')?.dataset.editObat;
        const deleteId = e.target.closest('[data-delete-obat]')?.dataset.deleteObat;

        if (editId) {
            try {
                const result = await requestObat(`${endpoint}/${editId}`);

                if (result.data) {
                    openObatModal(result.data);
                }
            } catch (error) {
                showToast(error.message, true);
            }
        }

        if (deleteId) {
            const obat = obatList.find(o => Number(o.id) === Number(deleteId));

            if (obat && confirm(`Hapus "${obat.nama}" dari daftar obat?`)) {
                try {
                    await requestObat(`${endpoint}/${deleteId}`, {
                        method: 'DELETE'
                    });

                    showToast('Obat berhasil dihapus.');
                    await muatObat();
                } catch (error) {
                    showToast(error.message, true);
                }
            }
        }
    });

    searchInput?.addEventListener('input', renderObat);
    filterKategori?.addEventListener('change', renderObat);

    muatObat();
}

// =========================================================
// NAVBAR ADMIN — tombol menu
// =========================================================

const navToggle = document.getElementById('admin-nav-toggle');
const navMenu = document.getElementById('admin-nav-menu');

if (navToggle && navMenu) {
    const setOpen = (open) => {
        navMenu.classList.toggle('is-open', open);
        navToggle.setAttribute('aria-expanded', String(open));
        navToggle.setAttribute(
            'aria-label',
            open ? 'Tutup menu' : 'Buka menu'
        );
    };

    navToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        setOpen(!navMenu.classList.contains('is-open'));
    });

    document.addEventListener('click', (event) => {
        if (!navMenu.contains(event.target) &&
            !navToggle.contains(event.target)) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            navMenu.classList.contains('is-open')
        ) {
            setOpen(false);
            navToggle.focus();
        }
    });
}

// =========================================================
// KELOLA PESANAN — CRUD melalui Laravel
// =========================================================

const pesananTbody = document.getElementById('pesanan-tbody');

if (pesananTbody) {
    const overlay = document.getElementById('pesanan-modal-overlay');
    const detailOverlay = document.getElementById('pesanan-detail-overlay');
    const form = document.getElementById('pesanan-form');
    const modalTitle = document.getElementById('pesanan-modal-title');
    const itemsBox = document.getElementById('pesanan-items');
    const tpl = document.getElementById('tpl-baris-produk');
    const errBox = document.getElementById('pesanan-form-error');
    const estimasi = document.getElementById('pesanan-estimasi');

    const field = (id) => document.getElementById(id);

    function formatRupiahPesanan(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function showToastPesanan(message, isDanger = false) {
        const toast = document.getElementById('admin-toast');

        if (!toast) return;

        toast.textContent = message;
        toast.classList.toggle('is-danger', Boolean(isDanger));
        toast.classList.add('is-visible');

        clearTimeout(window.pesananToastTimer);

        window.pesananToastTimer = setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 2400);
    }

    function tambahBaris(produkId, jumlah) {
        const row = tpl.content.firstElementChild.cloneNode(true);

        if (produkId !== undefined && produkId !== null) {
            row.querySelector('select').value = String(produkId);
        }

        if (jumlah !== undefined && jumlah !== null) {
            row.querySelector('input').value = String(jumlah);
        }

        itemsBox.appendChild(row);
        hitungEstimasi();
    }

    function hitungEstimasi() {
        let total = 0;

        itemsBox.querySelectorAll('.admin-item-row').forEach(row => {
            const opt = row.querySelector('select').selectedOptions[0];
            const harga = Number(opt?.dataset.harga || 0);
            const jml = parseInt(row.querySelector('input').value, 10);

            if (harga && jml > 0 && jml <= 99) {
                total += harga * jml;
            }
        });

        const ongkir = parseInt(field('pesanan-shipping').value, 10) || 0;

        if (estimasi) {
            estimasi.textContent = formatRupiahPesanan(
                total + (total ? ongkir : 0)
            );
        }
    }

    function tampilkanError(pesan) {
        errBox.textContent = pesan || '';
        errBox.hidden = !pesan;
    }

    function isiForm(data) {
        field('pesanan-aksi').value = data.aksi;
        field('pesanan-id').value = data.id || '';
        field('pesanan-user').value = String(data.user_id || '');
        field('pesanan-shipping').value = String(data.shipping || '');
        field('pesanan-status').value = data.status || 'Diproses';

        field('pesanan-alamat-nama').value = data.alamat_nama || '';
        field('pesanan-alamat-telp').value = data.alamat_telp || '';
        field('pesanan-alamat-teks').value = data.alamat_teks || '';

        itemsBox.textContent = '';

        const daftarItem = data.items && data.items.length
            ? data.items
            : [{ produk_id: '', jumlah: 1 }];

        daftarItem.forEach(item => {
            tambahBaris(item.produk_id, item.jumlah);
        });

        hitungEstimasi();
    }

    function bukaModal(judul, data) {
        modalTitle.textContent = judul;
        tampilkanError('');
        isiForm(data);
        overlay.classList.add('is-open');
    }

    function tutupModal() {
        overlay.classList.remove('is-open');
        tampilkanError('');
    }

    function bukaDetail(pesanan) {
        field('pesanan-detail-title').textContent =
            'Pesanan #' + pesanan.kode;

        field('pesanan-detail-sub').textContent =
            pesanan.tanggal;

        const dl = document.createElement('dl');

        function baris(label, isi) {
            const dt = document.createElement('dt');
            dt.textContent = label;

            const dd = document.createElement('dd');

            if (typeof isi === 'string') {
                dd.textContent = isi;
            } else {
                dd.appendChild(isi);
            }

            dl.append(dt, dd);
        }

        const ul = document.createElement('ul');

        (pesanan.items || []).forEach(item => {
            const li = document.createElement('li');

            li.textContent =
                `${item.nama} (${item.ket}) × ${item.jumlah} — ${item.harga}`;

            ul.appendChild(li);
        });

        baris(
            'Pelanggan',
            `${pesanan.pelanggan} · ${pesanan.email}`
        );

        baris(
            'Penerima',
            `${pesanan.alamat_nama} · ${pesanan.alamat_telp}`
        );

        baris('Alamat', pesanan.alamat_teks);
        baris('Produk', ul);
        baris('Subtotal', pesanan.subtotal);
        baris('Ongkir', `${pesanan.kurir} — ${pesanan.ongkir}`);
        baris('Total', pesanan.total);
        baris('Pembayaran', 'COD (bayar di tempat)');
        baris('Status', pesanan.status);

        const body = field('pesanan-detail-body');

        body.textContent = '';
        body.appendChild(dl);

        detailOverlay.classList.add('is-open');
    }

    function tutupDetail() {
        detailOverlay.classList.remove('is-open');
    }

    // Tombol tambah pesanan
    field('btn-tambah-pesanan')?.addEventListener('click', () => {
        form.reset();

        bukaModal('Tambah Pesanan', {
            aksi: 'tambah',
            status: 'Diproses'
        });
    });

    // Tombol tambah baris produk
    field('btn-tambah-baris')?.addEventListener('click', () => {
        tambahBaris('', 1);
    });

    // Tombol tutup modal
    field('pesanan-modal-close')?.addEventListener('click', tutupModal);
    field('pesanan-modal-cancel')?.addEventListener('click', tutupModal);

    field('pesanan-detail-close')?.addEventListener('click', tutupDetail);
    field('pesanan-detail-tutup')?.addEventListener('click', tutupDetail);

    overlay?.addEventListener('click', event => {
        if (event.target === overlay) tutupModal();
    });

    detailOverlay?.addEventListener('click', event => {
        if (event.target === detailOverlay) tutupDetail();
    });

    // Hapus baris produk
    itemsBox.addEventListener('click', event => {
        const hapus = event.target.closest('[data-hapus-baris]');

        if (!hapus) return;

        const jumlahBaris =
            itemsBox.querySelectorAll('.admin-item-row').length;

        if (jumlahBaris > 1) {
            hapus.closest('.admin-item-row').remove();
        } else {
            const row = hapus.closest('.admin-item-row');

            row.querySelector('select').value = '';
            row.querySelector('input').value = 1;
        }

        hitungEstimasi();
    });

    // Hitung ulang perkiraan total
    form.addEventListener('input', hitungEstimasi);
    form.addEventListener('change', hitungEstimasi);

    // Tombol detail dan ubah
    pesananTbody.addEventListener('click', event => {
        const tr = event.target.closest('tr[data-pesanan]');

        if (!tr) return;

        let pesanan;

        try {
            pesanan = JSON.parse(tr.dataset.pesanan);
        } catch (error) {
            return;
        }

        if (event.target.closest('[data-detail-pesanan]')) {
            bukaDetail(pesanan);

        } else if (event.target.closest('[data-edit-pesanan]')) {
            bukaModal('Ubah Pesanan #' + pesanan.kode, {
                aksi: 'ubah',
                id: pesanan.id,
                user_id: pesanan.user_id,
                shipping: pesanan.shipping,
                status: pesanan.status,
                alamat_nama: pesanan.alamat_nama,
                alamat_telp: pesanan.alamat_telp,
                alamat_teks: pesanan.alamat_teks,
                items: pesanan.items
            });
        }
    });

    // Buka kembali modal jika validasi server gagal
    if (form.dataset.isian) {
        const pesanServer = errBox.textContent.trim();

        try {
            const lama = JSON.parse(form.dataset.isian);

            bukaModal(
                lama.aksi === 'ubah' ? 'Ubah Pesanan' : 'Tambah Pesanan',
                lama
            );

            if (pesanServer) {
                tampilkanError(pesanServer);
            }

        } catch (error) {
            console.error('Data formulir pesanan tidak dapat dibaca.', error);
        }
    }

    // Tampilkan pesan dari server
    const alertServer = document.querySelector(
        '.admin-alert[role="status"]'
    );

    if (alertServer && alertServer.textContent.trim()) {
        showToastPesanan(
            alertServer.textContent.trim(),
            alertServer.classList.contains('is-error')
        );
    }
}


// =========================================================
// KELOLA PROMO — CRUD melalui Laravel
// =========================================================

const promoTbody = document.getElementById('promo-tbody');

if (promoTbody) {
    const overlay = document.getElementById('promo-modal-overlay');
    const form = document.getElementById('promo-form');
    const modalTitle = document.getElementById('promo-modal-title');
    const errBox = document.getElementById('promo-form-error');

    const field = (id) => document.getElementById(id);

    function tampilkanErrorPromo(pesan) {
        if (!errBox) return;
        errBox.textContent = pesan || '';
        errBox.hidden = !pesan;
    }

    function isiFormPromo(data) {
        field('promo-aksi').value = data.aksi;
        field('promo-id').value = data.id || '';
        field('promo-nama').value = data.nama || '';
        field('promo-kode').value = data.kode || '';
        field('promo-jenis').value = data.jenis || 'persen';
        field('promo-nilai').value = data.nilai ?? '';
        field('promo-minimal').value = data.minimal ?? '';
        field('promo-target').value = data.target || 'Semua Pengguna';
        field('promo-mulai').value = data.mulai || '';
        field('promo-selesai').value = data.selesai || '';

        field('promo-status').checked =
            data.aktif === undefined
                ? true
                : (data.aktif === true || data.aktif === '1' || data.aktif === 1);
    }

    function bukaModalPromo(judul, data) {
        modalTitle.textContent = judul;
        isiFormPromo(data);
        tampilkanErrorPromo('');
        overlay.classList.add('is-open');
    }

    function tutupModalPromo() {
        overlay.classList.remove('is-open');
        tampilkanErrorPromo('');
    }

    field('btn-tambah-promo')?.addEventListener('click', () => {
        bukaModalPromo('Tambah Promo', {
            aksi: 'tambah',
            aktif: true
        });
    });

    field('promo-modal-close')?.addEventListener('click', tutupModalPromo);
    field('promo-modal-cancel')?.addEventListener('click', tutupModalPromo);

    overlay?.addEventListener('click', (event) => {
        if (event.target === overlay) tutupModalPromo();
    });

    promoTbody.addEventListener('click', (event) => {
        if (!event.target.closest('[data-edit-promo]')) return;

        const baris = event.target.closest('tr[data-promo]');
        if (!baris) return;

        try {
            const promo = JSON.parse(baris.dataset.promo);
            bukaModalPromo('Ubah Promo ' + promo.kode,
                Object.assign({ aksi: 'ubah' }, promo));
        } catch (error) {
            console.error('Data promo tidak dapat dibaca.', error);
        }
    });

    if (form.dataset.isian) {
        try {
            const dataLama = JSON.parse(form.dataset.isian);

            bukaModalPromo(
                dataLama.aksi === 'ubah' ? 'Ubah Promo' : 'Tambah Promo',
                dataLama
            );

            const pesanError = errBox?.textContent.trim();
            if (pesanError) tampilkanErrorPromo(pesanError);
        } catch (error) {
            console.error('Data formulir promo tidak dapat dibaca.', error);
        }
    }

    const alertPromo = document.querySelector('.admin-alert[role="status"]');

    if (alertPromo && alertPromo.textContent.trim()) {
        showToast(
            alertPromo.textContent.trim(),
            alertPromo.classList.contains('is-error')
        );
    }
}
