(() => {
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
  document.querySelectorAll('.btn-add').forEach(btn => {
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

    // Buka tab sesuai hash (mis. profil.php#pesanan)
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
    const SUBTOTAL = 48000;

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
  // ---- Favorit: hapus produk dari daftar favorit ----
  const favGrid = document.getElementById('favorit-grid');
  if (favGrid) {
    const favEmpty = document.getElementById('favorit-empty');

    function checkFavEmpty() {
      const remaining = favGrid.querySelectorAll('[data-fav-item]').length;
      favGrid.style.display = remaining ? '' : 'none';
      if (favEmpty) favEmpty.style.display = remaining ? 'none' : 'flex';
    }

    favGrid.querySelectorAll('[data-fav-remove]').forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('[data-fav-item]');
        if (item) {
          item.style.opacity = '0';
          setTimeout(() => {
            item.remove();
            checkFavEmpty();
          }, 150);
        }
      });
    });
  }

  // ---- Keranjang: ubah jumlah & hapus item, hitung ulang total ----
  const cartItemsWrap = document.getElementById('cart-items');
  if (cartItemsWrap) {
    const cartEmpty = document.getElementById('cart-empty');
    const sumSubtotal = document.getElementById('sum-subtotal');
    const sumTotal = document.getElementById('sum-total');
    const sumCount = document.getElementById('sum-count');
    const SHIPPING = 10000;

    function formatRupiah(value) {
      return 'Rp ' + value.toLocaleString('id-ID');
    }

    function recalcCart() {
      const items = cartItemsWrap.querySelectorAll('[data-cart-item]');
      let subtotal = 0;

      items.forEach(item => {
        const price = Number(item.dataset.price);
        const qty = Number(item.querySelector('[data-qty-value]').textContent);
        const lineTotal = price * qty;
        subtotal += lineTotal;
        const totalEl = item.querySelector('[data-item-total]');
        if (totalEl) totalEl.textContent = formatRupiah(lineTotal);
      });

      if (sumSubtotal) sumSubtotal.textContent = formatRupiah(subtotal);
      if (sumTotal) sumTotal.textContent = formatRupiah(subtotal + (items.length ? SHIPPING : 0));
      if (sumCount) sumCount.textContent = items.length;

      if (cartEmpty) {
        cartEmpty.style.display = items.length ? 'none' : 'flex';
      }
    }

    cartItemsWrap.addEventListener('click', (event) => {
      const minusBtn = event.target.closest('[data-qty-minus]');
      const plusBtn = event.target.closest('[data-qty-plus]');
      const removeBtn = event.target.closest('[data-cart-remove]');

      if (minusBtn) {
        const valueEl = minusBtn.parentElement.querySelector('[data-qty-value]');
        const current = Number(valueEl.textContent);
        if (current > 1) {
          valueEl.textContent = current - 1;
          recalcCart();
        }
      }

      if (plusBtn) {
        const valueEl = plusBtn.parentElement.querySelector('[data-qty-value]');
        valueEl.textContent = Number(valueEl.textContent) + 1;
        recalcCart();
      }

      if (removeBtn) {
        const item = removeBtn.closest('[data-cart-item]');
        if (item) {
          item.remove();
          recalcCart();
        }
      }
    });

    recalcCart();
  }

  // ---- Pengingat Obat: tampilkan nama file resep yang diunggah ----
  const resepFile = document.getElementById('resep-file');
  if (resepFile) {
    const fileNameEl = document.getElementById('resep-file-name');
    resepFile.addEventListener('change', () => {
      if (resepFile.files && resepFile.files[0]) {
        fileNameEl.textContent = resepFile.files[0].name;
      } else {
        fileNameEl.textContent = '';
      }
    });
  }

  // ---- Pengingat Obat: submit resep -> buat kartu pengingat baru ----
  const resepForm = document.getElementById('resep-form');
  if (resepForm) {
    const reminderList = document.getElementById('reminder-list');
    const reminderEmpty = document.getElementById('reminder-empty');
    const resepMessage = document.getElementById('resep-message');

    function checkReminderEmpty() {
      const remaining = reminderList.querySelectorAll('[data-reminder]').length;
      if (reminderEmpty) reminderEmpty.style.display = remaining ? 'none' : 'flex';
    }

    function buildTimes(startTime, frequency) {
      const [h, m] = startTime.split(':').map(Number);
      const interval = Math.floor(24 / frequency);
      const times = [];
      for (let i = 0; i < frequency; i++) {
        const hour = (h + i * interval) % 24;
        times.push(String(hour).padStart(2, '0') + ':' + String(m).padStart(2, '0'));
      }
      return times;
    }

    resepForm.addEventListener('submit', (event) => {
      event.preventDefault();

      const nama = document.getElementById('resep-nama').value.trim();
      if (!nama) {
        resepMessage.textContent = 'Nama obat wajib diisi.';
        resepMessage.classList.add('is-error');
        document.getElementById('resep-nama').classList.add('has-error');
        return;
      }
      document.getElementById('resep-nama').classList.remove('has-error');

      const dosis = document.getElementById('resep-dosis').value.trim() || '1 tablet';
      const frekuensi = Number(document.getElementById('resep-frekuensi').value);
      const mulai = document.getElementById('resep-mulai').value || '07:00';
      const durasi = document.getElementById('resep-durasi').value || '5';
      const dokter = document.getElementById('resep-dokter').value.trim();

      const times = buildTimes(mulai, frekuensi);

      const card = document.createElement('div');
      card.className = 'reminder-card';
      card.setAttribute('data-reminder', '');
      card.innerHTML = `
        <span class="reminder-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        </span>
        <div class="reminder-body">
          <strong></strong>
          <div class="reminder-meta"></div>
          <div class="reminder-times"></div>
        </div>
        <div class="reminder-actions">
          <label class="toggle-switch" aria-label="Aktifkan/nonaktifkan pengingat">
            <input type="checkbox" checked data-reminder-toggle>
            <span class="track"></span>
          </label>
          <button type="button" class="icon-btn" data-reminder-edit aria-label="Ubah pengingat">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
          </button>
          <button type="button" class="icon-btn" data-reminder-delete aria-label="Hapus pengingat">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </div>
      `;

      card.querySelector('strong').textContent = nama;
      card.querySelector('.reminder-meta').textContent =
        `${dosis} · ${frekuensi}x sehari` + (dokter ? ` · Diresepkan ${dokter}` : '') + ` · sisa ${durasi} hari`;

      const timesWrap = card.querySelector('.reminder-times');
      times.forEach(t => {
        const chip = document.createElement('span');
        chip.className = 'reminder-time-chip';
        chip.textContent = t;
        timesWrap.appendChild(chip);
      });

      reminderList.prepend(card);
      checkReminderEmpty();

      resepMessage.classList.remove('is-error');
      resepMessage.textContent = 'Jadwal pengingat berhasil dibuat.';
      resepForm.reset();
      document.getElementById('resep-frekuensi').value = '2';
      document.getElementById('resep-mulai').value = '07:00';
      document.getElementById('resep-durasi').value = '5';
      const fileNameEl = document.getElementById('resep-file-name');
      if (fileNameEl) fileNameEl.textContent = '';
    });

    // ---- Toggle aktif/nonaktif, edit (inline), hapus pengingat ----
    reminderList.addEventListener('click', (event) => {
      const deleteBtn = event.target.closest('[data-reminder-delete]');
      const editBtn = event.target.closest('[data-reminder-edit]');

      if (deleteBtn) {
        const card = deleteBtn.closest('[data-reminder]');
        if (card && confirm('Hapus pengingat obat ini?')) {
          card.remove();
          checkReminderEmpty();
        }
      }

      if (editBtn) {
        const card = editBtn.closest('[data-reminder]');
        const nameEl = card.querySelector('strong');
        const newName = prompt('Ubah nama obat:', nameEl.textContent);
        if (newName && newName.trim()) {
          nameEl.textContent = newName.trim();
        }
      }
    });

    reminderList.addEventListener('change', (event) => {
      const toggle = event.target.closest('[data-reminder-toggle]');
      if (toggle) {
        const card = toggle.closest('[data-reminder]');
        card.classList.toggle('is-inactive', !toggle.checked);
      }
    });

    checkReminderEmpty();
  }
})();

(() => {
  // ---- Dashboard: tombol hati favorit di setiap produk ----
  document.querySelectorAll('[data-fav-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      const active = btn.classList.toggle('is-active');
      btn.setAttribute('aria-pressed', String(active));
      btn.setAttribute('aria-label', active ? 'Hapus dari favorit' : 'Tambah ke favorit');
    });
  });
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
      window.location.href = 'dashboard.php' + (q ? '?q=' + encodeURIComponent(q) : '');
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

  // dibuka dari halaman lain: dashboard.php?q=kata
  const initial = new URLSearchParams(window.location.search).get('q');
  if (initial) {
    input.value = initial;
    applySearch();
  }
})();
