<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Masuk — KlikMedis</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <div class="page">

        <main class="auth-panel">
            <a href="{{ url('/login') }}" class="logo logo-standalone">
                <span class="logo-mark">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo KlikMedis">
                </span>
                <span class="logo-text logo-text-dark">
                    KlikMedis<br><small>Apotek Online</small>
                </span>
            </a>

            <div class="auth-card">

                <div class="tabs" data-active="login" role="tablist" aria-label="Pilih Masuk atau Daftar">
                    <a class="tab is-active" href="{{ url('/login') }}" role="tab" aria-selected="true">Masuk</a>
                    <a class="tab" href="{{ url('/register') }}" role="tab" aria-selected="false">Daftar</a>
                    <span class="tab-indicator" aria-hidden="true"></span>
                </div>

                <form class="auth-form is-active" id="panel-login"
                    method="POST" action="{{ url('/login') }}">

                    @csrf

                    <h2>Selamat datang kembali</h2>
                    <p class="auth-sub">
                        Masuk untuk melanjutkan belanja kebutuhan kesehatanmu.
                    </p>

                    <div class="field">
                        <label for="login-identitas">Email atau username</label>

                        <input
                            type="text"
                            id="login-identitas"
                            name="identitas"
                            placeholder="nama@email.com atau username"
                            value="{{ old('identitas') }}"
                            autocomplete="username"
                            required
                        >

                        @error('identitas')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="login-password">Kata sandi</label>

                        <div class="password-wrap">
                            <input
                                type="password"
                                id="login-password"
                                name="password"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="toggle-pass"
                                data-target="login-password"
                                aria-label="Tampilkan kata sandi"
                            >
                                <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                    <path d="M12 5c-5 0-9 4-10.5 7C3 15 7 19 12 19s9-4 10.5-7C21 9 17 5 12 5Z"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                    <circle cx="12" cy="12" r="3"
                                        fill="none" stroke="currentColor" stroke-width="1.6" />
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row-between">
                        <label class="checkbox">
                            <input type="checkbox" name="remember" value="1">
                            <span>Ingat saya</span>
                        </label>

                        <a href="#" class="link-muted">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="btn-primary">Masuk</button>

                    <div class="form-message {{ ($errors->has('login') || $errors->has('identitas') || $errors->has('password')) ? 'is-error' : '' }}"
    id="login-message" role="status" aria-live="polite">

    @if (session('auth'))
        {{ session('auth') }}
    @elseif ($errors->has('login'))
        {{ $errors->first('login') }}
    @elseif ($errors->has('identitas'))
        {{ $errors->first('identitas') }}
    @elseif ($errors->has('password'))
        {{ $errors->first('password') }}
    @endif

</div>

                    <p class="switch-text">
                        Belum punya akun?
                        <a href="{{ url('/register') }}" class="link-strong">Daftar sekarang</a>
                    </p>

                </form>

            </div>
        </main>

    </div>

    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>

