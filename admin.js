(() => {
  // =========================================================
  // Toast helper
  // =========================================================
  const toastEl = document.getElementById('admin-toast');
  let toastTimer = null;

  function showToast(message, isDanger) {
    if (!toastEl) return;
    toastEl.textContent = message;
    toastEl.classList.toggle('is-danger', Boolean(isDanger));
    toastEl.classList.add('is-visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toastEl.classList.remove('is-visible'), 2400);
  }

  function formatRupiah(value) {
    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
  }

  function formatTanggal(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
  }

  // =========================================================
  // KELOLA OBAT — CRUD (tampilan saja, data disimpan di memori)
  // =========================================================
  const obatTbody = document.getElementById('obat-tbody');

  if (obatTbody) {
    let obatList = [
      { id: 1, nama: 'Paracetamol 500 mg', kategori: 'Obat Bebas', satuan: '10 Tablet', harga: 5000, stok: 48, butuhResep: false, aktif: true, deskripsi: 'Obat penurun demam dan pereda nyeri.' },
      { id: 2, nama: 'Amoxicillin 500 mg', kategori: 'Obat Resep', satuan: '10 Kapsul', harga: 22000, stok: 15, butuhResep: true, aktif: true, deskripsi: 'Antibiotik, hanya dengan resep dokter.' },
      { id: 3, nama: 'Vitamin C 1000 mg', kategori: 'Vitamin & Suplemen', satuan: '10 Tablet', harga: 25000, stok: 9, butuhResep: false, aktif: true, deskripsi: 'Menjaga daya tahan tubuh.' },
      { id: 4, nama: 'Cetirizine 10 mg', kategori: 'Obat Bebas', satuan: '10 Tablet', harga: 12000, stok: 4, butuhResep: false, aktif: true, deskripsi: 'Obat antihistamin untuk alergi.' },
      { id: 5, nama: 'Antangin JRG', kategori: 'Obat Bebas', satuan: '12 Sachet', harga: 15000, stok: 6, butuhResep: false, aktif: true, deskripsi: 'Meredakan masuk angin.' },
      { id: 6, nama: 'Hansaplast Plester', kategori: 'Alat Kesehatan', satuan: '20 pcs', harga: 28000, stok: 32, butuhResep: false, aktif: false, deskripsi: 'Plester luka kedap air.' },
    ];
    let nextObatId = 7;

    const searchInput = document.getElementById('obat-search');
    const filterKategori = document.getElementById('obat-filter-kategori');
    const overlay = document.getElementById('obat-modal-overlay');
    const form = document.getElementById('obat-form');
    const modalTitle = document.getElementById('obat-modal-title');

    function stokBadge(stok) {
      if (stok <= 5) return '<span class="badge danger">Stok Kritis</span>';
      if (stok <= 10) return '<span class="badge warning">Menipis</span>';
      return '<span class="badge success">Tersedia</span>';
    }

    function renderObat() {
      const q = (searchInput?.value || '').toLowerCase().trim();
      const kategori = filterKategori?.value || '';

      const filtered = obatList.filter(o => {
        const matchQ = !q || o.nama.toLowerCase().includes(q);
        const matchKategori = !kategori || o.kategori === kategori;
        return matchQ && matchKategori;
      });

      if (!filtered.length) {
        obatTbody.innerHTML = '<tr class="admin-empty-row"><td colspan="7">Tidak ada obat yang cocok.</td></tr>';
        return;
      }

      obatTbody.innerHTML = filtered.map(o => `
        <tr data-id="${o.id}">
          <td>
            <div class="admin-cell-product">
              <span class="admin-cell-thumb">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M9 8h6M9 12h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
              </span>
              <div><strong>${escapeHtml(o.nama)}</strong><span>${escapeHtml(o.satuan || '-')}</span></div>
            </div>
          </td>
          <td>${escapeHtml(o.kategori)}</td>
          <td>${formatRupiah(o.harga)}</td>
          <td>${o.stok}</td>
          <td>${o.butuhResep ? '<span class="badge warning">Wajib Resep</span>' : '<span class="badge neutral">Bebas</span>'}</td>
          <td>${o.aktif ? '<span class="badge success">Aktif</span>' : '<span class="badge neutral">Nonaktif</span>'}${stokAppendix(o.stok)}</td>
          <td>
            <div class="admin-row-actions">
              <button type="button" class="admin-icon-action" data-edit-obat="${o.id}" aria-label="Ubah obat">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
              </button>
              <button type="button" class="admin-icon-action danger" data-delete-obat="${o.id}" aria-label="Hapus obat">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    }

    function stokAppendix() { return ''; }

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str == null ? '' : String(str);
      return div.innerHTML;
    }

    function openObatModal(obat) {
      form.reset();
      if (obat) {
        modalTitle.textContent = 'Ubah Obat';
        document.getElementById('obat-id').value = obat.id;
        document.getElementById('obat-nama').value = obat.nama;
        document.getElementById('obat-kategori').value = obat.kategori;
        document.getElementById('obat-satuan').value = obat.satuan;
        document.getElementById('obat-harga').value = obat.harga;
        document.getElementById('obat-stok').value = obat.stok;
        document.getElementById('obat-deskripsi').value = obat.deskripsi || '';
        document.getElementById('obat-butuh-resep').checked = obat.butuhResep;
        document.getElementById('obat-status').checked = obat.aktif;
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
    document.getElementById('obat-modal-close')?.addEventListener('click', closeObatModal);
    document.getElementById('obat-modal-cancel')?.addEventListener('click', closeObatModal);
    overlay?.addEventListener('click', (e) => { if (e.target === overlay) closeObatModal(); });

    form?.addEventListener('submit', (e) => {
      e.preventDefault();
      const id = document.getElementById('obat-id').value;
      const data = {
        nama: document.getElementById('obat-nama').value.trim(),
        kategori: document.getElementById('obat-kategori').value,
        satuan: document.getElementById('obat-satuan').value.trim(),
        harga: Number(document.getElementById('obat-harga').value) || 0,
        stok: Number(document.getElementById('obat-stok').value) || 0,
        deskripsi: document.getElementById('obat-deskripsi').value.trim(),
        butuhResep: document.getElementById('obat-butuh-resep').checked,
        aktif: document.getElementById('obat-status').checked,
      };

      if (!data.nama) {
        showToast('Nama obat wajib diisi.', true);
        return;
      }

      if (id) {
        const idx = obatList.findIndex(o => o.id === Number(id));
        if (idx > -1) obatList[idx] = { ...obatList[idx], ...data };
        showToast('Obat berhasil diperbarui.');
      } else {
        obatList.unshift({ id: nextObatId++, ...data });
        showToast('Obat baru berhasil ditambahkan.');
      }

      closeObatModal();
      renderObat();
    });

    obatTbody.addEventListener('click', (e) => {
      const editId = e.target.closest('[data-edit-obat]')?.dataset.editObat;
      const deleteId = e.target.closest('[data-delete-obat]')?.dataset.deleteObat;

      if (editId) {
        const obat = obatList.find(o => o.id === Number(editId));
        if (obat) openObatModal(obat);
      }

      if (deleteId) {
        const obat = obatList.find(o => o.id === Number(deleteId));
        if (obat && confirm(`Hapus "${obat.nama}" dari daftar obat?`)) {
          obatList = obatList.filter(o => o.id !== Number(deleteId));
          showToast('Obat berhasil dihapus.', true);
          renderObat();
        }
      }
    });

    searchInput?.addEventListener('input', renderObat);
    filterKategori?.addEventListener('change', renderObat);

    renderObat();
  }

  // =========================================================
  // KELOLA PROMO — CRUD (tampilan saja, data disimpan di memori)
  // =========================================================
  const promoTbody = document.getElementById('promo-tbody');

  if (promoTbody) {
    let promoList = [
      { id: 1, nama: 'Diskon Spesial Vitamin', kode: 'SEHAT30', jenis: 'persen', nilai: 30, minimal: 20000, target: 'Semua Pengguna', mulai: '2026-09-01', selesai: '2026-09-30', aktif: true },
      { id: 2, nama: 'Gratis Ongkir', kode: 'GRATISONGKIR', jenis: 'nominal', nilai: 10000, minimal: 50000, target: 'Semua Pengguna', mulai: '2026-09-10', selesai: '2026-10-10', aktif: true },
      { id: 3, nama: 'Selamat Datang Member Baru', kode: 'WELCOME15', jenis: 'persen', nilai: 15, minimal: 0, target: 'Member Baru', mulai: '2026-08-01', selesai: '2026-09-28', aktif: true },
      { id: 4, nama: 'Promo Ramadan', kode: 'RAMADAN20', jenis: 'persen', nilai: 20, minimal: 30000, target: 'Semua Pengguna', mulai: '2026-03-01', selesai: '2026-03-31', aktif: false },
    ];
    let nextPromoId = 5;

    const searchInput = document.getElementById('promo-search');
    const filterStatus = document.getElementById('promo-filter-status');
    const overlay = document.getElementById('promo-modal-overlay');
    const form = document.getElementById('promo-form');
    const modalTitle = document.getElementById('promo-modal-title');

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str == null ? '' : String(str);
      return div.innerHTML;
    }

    function renderPromo() {
      const q = (searchInput?.value || '').toLowerCase().trim();
      const status = filterStatus?.value || '';

      const filtered = promoList.filter(p => {
        const matchQ = !q || p.nama.toLowerCase().includes(q) || p.kode.toLowerCase().includes(q);
        const matchStatus = !status || (status === 'Aktif' ? p.aktif : !p.aktif);
        return matchQ && matchStatus;
      });

      if (!filtered.length) {
        promoTbody.innerHTML = '<tr class="admin-empty-row"><td colspan="8">Tidak ada promo yang cocok.</td></tr>';
        return;
      }

      promoTbody.innerHTML = filtered.map(p => `
        <tr data-id="${p.id}">
          <td><strong>${escapeHtml(p.nama)}</strong></td>
          <td>${escapeHtml(p.kode)}</td>
          <td>${p.jenis === 'persen' ? p.nilai + '%' : formatRupiah(p.nilai)}</td>
          <td>${p.minimal ? formatRupiah(p.minimal) : '-'}</td>
          <td>${escapeHtml(p.target)}</td>
          <td>${formatTanggal(p.mulai)} – ${formatTanggal(p.selesai)}</td>
          <td>${p.aktif ? '<span class="badge success">Aktif</span>' : '<span class="badge neutral">Nonaktif</span>'}</td>
          <td>
            <div class="admin-row-actions">
              <button type="button" class="admin-icon-action" data-edit-promo="${p.id}" aria-label="Ubah promo">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
              </button>
              <button type="button" class="admin-icon-action danger" data-delete-promo="${p.id}" aria-label="Hapus promo">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
    }

    function openPromoModal(promo) {
      form.reset();
      if (promo) {
        modalTitle.textContent = 'Ubah Promo';
        document.getElementById('promo-id').value = promo.id;
        document.getElementById('promo-nama').value = promo.nama;
        document.getElementById('promo-kode').value = promo.kode;
        document.getElementById('promo-jenis').value = promo.jenis;
        document.getElementById('promo-nilai').value = promo.nilai;
        document.getElementById('promo-minimal').value = promo.minimal;
        document.getElementById('promo-target').value = promo.target;
        document.getElementById('promo-mulai').value = promo.mulai;
        document.getElementById('promo-selesai').value = promo.selesai;
        document.getElementById('promo-status').checked = promo.aktif;
      } else {
        modalTitle.textContent = 'Tambah Promo';
        document.getElementById('promo-id').value = '';
        document.getElementById('promo-status').checked = true;
      }
      overlay.classList.add('is-open');
    }

    function closePromoModal() {
      overlay.classList.remove('is-open');
    }

    document.getElementById('btn-tambah-promo')?.addEventListener('click', () => openPromoModal(null));
    document.getElementById('promo-modal-close')?.addEventListener('click', closePromoModal);
    document.getElementById('promo-modal-cancel')?.addEventListener('click', closePromoModal);
    overlay?.addEventListener('click', (e) => { if (e.target === overlay) closePromoModal(); });

    form?.addEventListener('submit', (e) => {
      e.preventDefault();
      const id = document.getElementById('promo-id').value;
      const data = {
        nama: document.getElementById('promo-nama').value.trim(),
        kode: document.getElementById('promo-kode').value.trim().toUpperCase(),
        jenis: document.getElementById('promo-jenis').value,
        nilai: Number(document.getElementById('promo-nilai').value) || 0,
        minimal: Number(document.getElementById('promo-minimal').value) || 0,
        target: document.getElementById('promo-target').value,
        mulai: document.getElementById('promo-mulai').value,
        selesai: document.getElementById('promo-selesai').value,
        aktif: document.getElementById('promo-status').checked,
      };

      if (!data.nama || !data.kode) {
        showToast('Nama dan kode promo wajib diisi.', true);
        return;
      }

      if (id) {
        const idx = promoList.findIndex(p => p.id === Number(id));
        if (idx > -1) promoList[idx] = { ...promoList[idx], ...data };
        showToast('Promo berhasil diperbarui.');
      } else {
        promoList.unshift({ id: nextPromoId++, ...data });
        showToast('Promo baru berhasil ditambahkan.');
      }

      closePromoModal();
      renderPromo();
    });

    promoTbody.addEventListener('click', (e) => {
      const editId = e.target.closest('[data-edit-promo]')?.dataset.editPromo;
      const deleteId = e.target.closest('[data-delete-promo]')?.dataset.deletePromo;

      if (editId) {
        const promo = promoList.find(p => p.id === Number(editId));
        if (promo) openPromoModal(promo);
      }

      if (deleteId) {
        const promo = promoList.find(p => p.id === Number(deleteId));
        if (promo && confirm(`Hapus promo "${promo.nama}"?`)) {
          promoList = promoList.filter(p => p.id !== Number(deleteId));
          showToast('Promo berhasil dihapus.', true);
          renderPromo();
        }
      }
    });

    searchInput?.addEventListener('input', renderPromo);
    filterStatus?.addEventListener('change', renderPromo);

    renderPromo();
  }
})();
