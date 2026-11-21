(() => {
  // Alamat dasar aplikasi (dari <meta name="base-url">), supaya URL benar di XAMPP maupun hosting.
  const BASE_URL = (document.querySelector('meta[name="base-url"]') || {}).content || '';

  // ---- Navbar: hamburger dropdown ----
  const navToggle = document.getElementById('nav-toggle');
  const navMenu = document.getElementById('nav-menu');
  if (navToggle && navMenu) {
    const setOpen = (open) => {
      navMenu.classList.toggle('is-open', open);
      navToggle.setAttribute('aria-expanded', String(open));
      navToggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    };

    navToggle.addEventListener('click', (event) => {
      event.stopPropagation();
      setOpen(!navMenu.classList.contains('is-open'));
    });

    // klik di luar menu menutup dropdown
    document.addEventListener('click', (event) => {
      if (!navMenu.contains(event.target)) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && navMenu.classList.contains('is-open')) {
        setOpen(false);
        navToggle.focus();
      }
    });
  }

  // ---- "Tambah ke keranjang" feedback (dashboard) ----
  document.querySelectorAll('.btn-add[type="button"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const original = btn.textContent;
      btn.textContent = 'Ditambahkan ✓';
      btn.disabled = true;
      setTimeout(() => {
        btn.textContent = original;
        btn.disabled = false;
      }, 1200);
    });
  });

  // ---- Profil: switch between Profil / Pesanan / Alamat tabs ----
  const tabLinks = document.querySelectorAll('.content-tab[data-tab-target]');
  if (tabLinks.length) {
    const tabs = document.querySelectorAll('.account-tab');

    tabLinks.forEach(link => {
      link.addEventListener('click', (event) => {
        event.preventDefault();
        const targetId = link.dataset.tabTarget;

        tabLinks.forEach(l => l.classList.toggle('is-active', l === link));
        tabs.forEach(tab => tab.classList.toggle('is-active', tab.id === targetId));
      });
    });

    // Buka tab sesuai hash (mis. /profil#pesanan)
    const initial = document.querySelector('.content-tab[href="' + location.hash + '"]');
    if (initial) initial.click();
  }

  // ---- Dashboard: pilih kategori ----
  document.querySelectorAll('.category-chip').forEach(chip => {
    chip.addEventListener('click', (event) => {
      event.preventDefault();
      document.querySelectorAll('.category-chip').forEach(c => c.classList.toggle('is-active', c === chip));
    });
  });

  // ---- Checkout: recalculate total when shipping option changes ----
  const shippingInputs = document.querySelectorAll('input[name="shipping"]');
  const subtotalEl = document.getElementById('sum-subtotal');
  const shippingEl = document.getElementById('sum-shipping');
  const totalEl = document.getElementById('sum-total');

  if (shippingInputs.length && subtotalEl) {
    const SUBTOTAL = Number(subtotalEl.dataset.subtotal) || 0;

    function formatRupiah(value) {
      return 'Rp ' + value.toLocaleString('id-ID');
    }

    function recalculate() {
      const checked = document.querySelector('input[name="shipping"]:checked');
      const shippingCost = checked ? Number(checked.value) : 0;
      shippingEl.textContent = formatRupiah(shippingCost);
      totalEl.textContent = formatRupiah(SUBTOTAL + shippingCost);
    }

    shippingInputs.forEach(input => input.addEventListener('change', recalculate));
    recalculate();
  }

  // ---- Alamat: tombol "Ubah" membuka form edit, "Batal" menutupnya ----
  document.querySelectorAll('[data-addr-edit]').forEach(btn => {
    btn.addEventListener('click', (event) => {
      event.preventDefault();
      const card = btn.closest('.card, [data-addr-card]');
      const form = card && card.querySelector('[data-addr-form]');
      if (!form) return;
      form.hidden = !form.hidden;
      if (!form.hidden) {
        const first = form.querySelector('input[type="text"]');
        if (first) first.focus();
      }
    });
  });

  document.querySelectorAll('[data-addr-cancel]').forEach(btn => {
    btn.addEventListener('click', () => {
      const form = btn.closest('[data-addr-form]');
      if (form) {
        form.reset();
        form.hidden = true;
      }
    });
  });
})();

(() => {
  // ---- Keranjang: tombol − / + mengubah isi kolom jumlah lalu mengirim form ke server ----
  // (Perubahan disimpan di database oleh KeranjangController; validasi tetap dilakukan di server.)
  document.querySelectorAll('.qty-form').forEach(form => {
    const input = form.querySelector('[data-qty-input]');
    if (!input) return;

    form.querySelectorAll('[data-qty-step]').forEach(btn => {
      btn.addEventListener('click', () => {
        const next = (parseInt(input.value, 10) || 0) + Number(btn.dataset.qtyStep);
        input.value = Math.min(99, Math.max(1, next));
        form.submit();
      });
    });

    input.addEventListener('change', () => form.submit());
  });

  // ---- Pengingat Obat ----
  // Data disimpan server (PengingatController). JavaScript di sini hanya:
  //  1) mengisi form saat tombol Ubah ditekan (nilai lewat .value, bukan innerHTML -> aman dari XSS),
  //  2) validasi ringan di browser (kemudahan saja; validasi sebenarnya ada di server).
  const resepForm = document.getElementById('resep-form');
  if (resepForm) {
    const el = (id) => document.getElementById(id);
    const pesan = el('resep-message');

    const tampilPesan = (teks) => {
      pesan.textContent = teks || '';
      pesan.classList.toggle('is-error', !!teks);
    };

    const setMode = (mengubah) => {
      el('resep-aksi').value = mengubah ? 'ubah' : 'tambah';
      el('resep-judul').textContent = mengubah ? 'Ubah Pengingat Obat' : 'Input Resep Obat';
      el('resep-submit').textContent = mengubah ? 'Simpan Perubahan' : 'Buat Jadwal Pengingat';
      el('resep-batal').style.display = mengubah ? '' : 'none';
    };

    document.getElementById('reminder-list').addEventListener('click', (event) => {
      const tombol = event.target.closest('[data-reminder-edit]');
      if (!tombol) return;
      const kartu = tombol.closest('[data-reminder]');
      let d;
      try { d = JSON.parse(kartu.dataset.reminder); } catch (err) { return; }

      el('resep-id').value = d.id;
      el('resep-nama').value = d.nama;
      el('resep-dosis').value = d.dosis;
      el('resep-frekuensi').value = String(d.frekuensi);
      el('resep-mulai').value = d.jam_mulai;
      el('resep-durasi').value = String(d.durasi);
      el('resep-dokter').value = d.dokter;
      el('resep-catatan').value = d.catatan;
      setMode(true);
      tampilPesan('');
      el('form-resep').scrollIntoView({ behavior: 'smooth', block: 'start' });
      el('resep-nama').focus();
    });

    el('resep-batal').addEventListener('click', () => {
      resepForm.reset();
      el('resep-id').value = '';
      el('resep-frekuensi').value = '2';
      el('resep-mulai').value = '07:00';
      el('resep-durasi').value = '5';
      setMode(false);
      tampilPesan('');
    });

    resepForm.addEventListener('submit', (event) => {
      const nama = el('resep-nama').value.trim();
      const dosis = el('resep-dosis').value.trim();
      const durasi = el('resep-durasi').value.trim();
      let galat = '';

      if (!nama) galat = 'Nama obat wajib diisi.';
      else if (!dosis) galat = 'Dosis sekali minum wajib diisi.';
      else if (!el('resep-mulai').value) galat = 'Jam mulai minum wajib diisi.';
      else if (!/^\d+$/.test(durasi) || Number(durasi) < 1 || Number(durasi) > 90) galat = 'Durasi pengobatan harus antara 1 dan 90 hari.';

      el('resep-nama').classList.toggle('has-error', !nama);
      if (galat) {
        event.preventDefault();
        tampilPesan(galat);
      }
    });
  }
})();

(() => {
  // ---- Pencarian produk (kolom cari di topbar) ----
  // Halaman yang punya daftar produk (Dashboard, Favorit): filter langsung saat mengetik.
  // Halaman lain: tekan Enter -> pindah ke Dashboard dengan hasil pencarian.
  const input = document.getElementById('site-search');
  if (!input) return;

  const cards = Array.from(document.querySelectorAll('.product-card'));
  const normalize = (str) => (str || '').toLowerCase().replace(/\s+/g, ' ').trim();

  if (!cards.length) {
    input.addEventListener('keydown', (event) => {
      if (event.key !== 'Enter') return;
      event.preventDefault();
      const q = input.value.trim();
      window.location.href = BASE_URL + '/dashboard' + (q ? '?q=' + encodeURIComponent(q) : '');
    });
    return;
  }

  const grid = cards[0].parentElement;
  const heading = document.querySelector('.section-heading h2');
  const headingDefault = heading ? heading.textContent : '';

  // teks yang dicari: nama + keterangan produk (mis. "Paracetamol 500 mg 10 Tablet")
  const haystacks = cards.map((card) => {
    const name = card.querySelector('.product-name');
    const meta = card.querySelector('.product-meta');
    return normalize((name ? name.textContent : card.textContent) + ' ' + (meta ? meta.textContent : ''));
  });

  const emptyBox = document.createElement('div');
  emptyBox.className = 'card empty-state search-empty';
  emptyBox.style.display = 'none';
  emptyBox.innerHTML = '<h3>Produk tidak ditemukan</h3><p>Coba kata kunci lain, misalnya nama obat atau kategorinya.</p>';
  grid.insertAdjacentElement('afterend', emptyBox);

  const applySearch = () => {
    const raw = input.value.trim();
    const tokens = normalize(raw).split(' ').filter(Boolean);
    let visible = 0;

    cards.forEach((card, i) => {
      const match = tokens.every((t) => haystacks[i].includes(t));
      card.classList.toggle('is-search-hidden', !match);
      if (match) visible += 1;
    });

    const searching = tokens.length > 0;
    grid.style.display = visible ? '' : 'none';
    emptyBox.style.display = searching && !visible ? 'flex' : 'none';
    if (heading) heading.textContent = searching ? 'Hasil pencarian "' + raw + '"' : headingDefault;

    // simpan kata kunci di URL supaya bisa di-refresh / dibagikan
    const url = new URL(window.location.href);
    if (searching) url.searchParams.set('q', raw); else url.searchParams.delete('q');
    window.history.replaceState(null, '', url);
  };

  input.addEventListener('input', applySearch);
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') event.preventDefault();
  });

  // dibuka dari halaman lain: /dashboard?q=kata
  const initial = new URLSearchParams(window.location.search).get('q');
  if (initial) {
    input.value = initial;
    applySearch();
  }
})();
