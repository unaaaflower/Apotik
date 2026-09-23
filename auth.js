(() => {
  // Password show/hide toggles
  document.querySelectorAll('.toggle-pass').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';
      btn.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      btn.classList.toggle('is-visible', isPassword);
    });
  });

  function setError(inputId, message) {
    const errorEl = document.querySelector(`[data-error-for="${inputId}"]`);
    const inputEl = document.getElementById(inputId);
    if (errorEl) errorEl.textContent = message || '';
    if (inputEl) inputEl.classList.toggle('has-error', Boolean(message));
  }

  function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => (el.textContent = ''));
    form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
  }

  function showMessage(el, text, isError) {
    el.textContent = text;
    el.classList.toggle('is-error', Boolean(isError));
  }

  // ---- Login page ----
  const loginForm = document.getElementById('panel-login');
  if (loginForm) {
    const loginMessage = document.getElementById('login-message');

    loginForm.addEventListener('submit', (event) => {
      event.preventDefault();
      clearErrors(loginForm);
      showMessage(loginMessage, '', false);

      const email = document.getElementById('login-email').value.trim();
      const password = document.getElementById('login-password').value;
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      let hasError = false;

      if (!email) {
        alert('Email wajib diisi.');
        setError('login-email', 'Masukkan email kamu.');
        hasError = true;
      } else if (!emailPattern.test(email)) {
        alert('Format email tidak valid. Contoh: nama@email.com');
        setError('login-email', 'Format email tidak valid.');
        hasError = true;
      }
      if (!password) {
        setError('login-password', 'Masukkan kata sandi kamu.');
        hasError = true;
      } else if (password.length < 8) {
        setError('login-password', 'Kata sandi minimal 8 karakter.');
        hasError = true;
      }

      if (hasError) return;

      const submitBtn = loginForm.querySelector('.btn-primary');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Memproses...';

      // Simulated request — replace with a real API call.
      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Masuk';
        showMessage(loginMessage, 'Berhasil masuk. Mengalihkan ke dashboard...', false);
        setTimeout(() => { window.location.href = 'dashboard.html'; }, 700);
      }, 900);
    });
  }

  // ---- Register page ----
  const registerForm = document.getElementById('panel-register');
  if (registerForm) {
    const registerMessage = document.getElementById('register-message');
    const regPassword = document.getElementById('reg-password');
    const strengthBars = registerForm.querySelectorAll('.strength-meter span');
    const strengthColors = ['#D64545', '#E8A200', '#2EAD63', '#0E5E37'];

    function passwordScore(value) {
      let score = 0;
      if (value.length >= 8) score += 1;
      if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score += 1;
      if (/\d/.test(value)) score += 1;
      if (/[^A-Za-z0-9]/.test(value)) score += 1;
      return score;
    }

    regPassword.addEventListener('input', () => {
      const score = passwordScore(regPassword.value);
      strengthBars.forEach((bar, i) => {
        bar.style.background = i < score ? strengthColors[score - 1] : 'var(--line)';
      });
    });

    registerForm.addEventListener('submit', (event) => {
      event.preventDefault();
      clearErrors(registerForm);
      showMessage(registerMessage, '', false);

      const name = document.getElementById('reg-name').value.trim();
      const email = document.getElementById('reg-email').value.trim();
      const phone = document.getElementById('reg-phone').value.trim();
      const password = document.getElementById('reg-password').value;
      const confirm = document.getElementById('reg-confirm').value;
      const terms = document.getElementById('reg-terms').checked;

      let hasError = false;
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const phonePattern = /^[0-9+\s-]{9,15}$/;

      if (name.length < 3) {
        setError('reg-name', 'Nama minimal 3 karakter.');
        hasError = true;
      }
      if (!emailPattern.test(email)) {
        setError('reg-email', 'Masukkan alamat email yang valid.');
        hasError = true;
      }
      if (!phonePattern.test(phone)) {
        setError('reg-phone', 'Masukkan nomor HP yang valid.');
        hasError = true;
      }
      if (password.length < 8) {
        setError('reg-password', 'Kata sandi minimal 8 karakter.');
        hasError = true;
      }
      if (confirm !== password || confirm === '') {
        setError('reg-confirm', 'Konfirmasi kata sandi tidak cocok.');
        hasError = true;
      }
      if (!terms) {
        setError('reg-terms', 'Setujui syarat & ketentuan untuk melanjutkan.');
        hasError = true;
      }

      if (hasError) return;

      const submitBtn = registerForm.querySelector('.btn-primary');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Membuat akun...';

      // Simulated request — replace with a real API call.
      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Daftar';
        showMessage(registerMessage, 'Akun berhasil dibuat. Mengalihkan ke halaman masuk...', false);
        registerForm.reset();
        strengthBars.forEach(bar => (bar.style.background = 'var(--line)'));
        setTimeout(() => { window.location.href = 'login.html'; }, 900);
      }, 900);
    });
  }
})();