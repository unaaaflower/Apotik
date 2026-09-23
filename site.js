(() => {
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
  const tabLinks = document.querySelectorAll('.sidebar-link[data-tab-target]');
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
  }

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

  // ---- Checkout: simulate payment ----
  const payBtn = document.getElementById('pay-btn');
  if (payBtn) {
    const checkoutMessage = document.getElementById('checkout-message');
    payBtn.addEventListener('click', () => {
      payBtn.disabled = true;
      payBtn.textContent = 'Memproses pembayaran...';
      checkoutMessage.classList.remove('is-error');
      checkoutMessage.textContent = '';

      // Simulated request — replace with a real payment API call.
      setTimeout(() => {
        payBtn.textContent = 'Bayar Sekarang';
        payBtn.disabled = false;
        checkoutMessage.textContent = 'Pembayaran berhasil dikonfirmasi. Terima kasih!';
      }, 1100);
    });
  }
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
