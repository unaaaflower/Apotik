<header class="admin-topbar">

    <a href="{{ url('/admin/dashboard') }}" class="admin-logo">
        <span class="admin-logo-mark">
            <img src="{{ asset('images/logo.png') }}" alt="Logo KlikMedis">
        </span>

        <span class="admin-logo-text">
            KlikMedis<br>
            <small>Admin Panel</small>
        </span>
    </a>

    <span class="admin-topbar-divider" aria-hidden="true"></span>

    <div>
        <div class="admin-page-title">{{ $judul }}</div>
        <div class="admin-breadcrumb">{{ $breadcrumb }}</div>
    </div>

    <div class="admin-topbar-actions">

        <button
            type="button"
            class="admin-icon-btn admin-nav-toggle"
            id="admin-nav-toggle"
            aria-label="Buka menu"
            aria-haspopup="true"
            aria-expanded="false"
            aria-controls="admin-nav-menu"
        >
            <svg class="icon-open" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
            </svg>

            <svg class="icon-close" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
            </svg>
        </button>

        <nav class="admin-nav-menu" id="admin-nav-menu" role="menu" aria-label="Menu admin">

            <div class="admin-account" role="presentation">
                <div class="admin-avatar">
                    {{ collect(explode(' ', trim(auth()->user()->nama)))->filter()->take(2)->map(fn ($kata) => mb_substr($kata, 0, 1))->implode('') }}
                </div>

                <div>
                    <div class="admin-account-name">
                        {{ auth()->user()->nama }}
                    </div>

                    <div class="admin-account-role">
                        Admin
                    </div>
                </div>
            </div>

            <div class="admin-nav-divider"></div>

            <p class="admin-nav-title">Menu Utama</p>

            <a
                href="{{ url('/admin/dashboard') }}"
                class="admin-nav-link{{ $aktifAdmin === 'dashboard' ? ' is-active' : '' }}"
                role="menuitem"
            >
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13" y="3" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="3" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13" y="13" width="8" height="8" rx="2" stroke="currentColor" stroke-width="1.7"/>
                </svg>
                Dashboard
            </a>

            <a
                href="{{ url('/admin/obat') }}"
                class="admin-nav-link{{ $aktifAdmin === 'obat' ? ' is-active' : '' }}"
                role="menuitem"
            >
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <rect x="5" y="3" width="14" height="18" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                Kelola Obat
            </a>

            <a
                href="{{ url('/admin/promo') }}"
                class="admin-nav-link{{ $aktifAdmin === 'promo' ? ' is-active' : '' }}"
                role="menuitem"
            >
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <path d="M20.6 12.3 12.3 20.6a2 2 0 0 1-2.8 0l-6-6a2 2 0 0 1 0-2.9L11.8 3.4a2 2 0 0 1 1.4-.6H19a2 2 0 0 1 2 2v6.1c0 .5-.2 1-.6 1.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <circle cx="15.5" cy="8.5" r="1.6" stroke="currentColor" stroke-width="1.7"/>
                </svg>
                Kelola Promo
            </a>

            <p class="admin-nav-title">Lainnya</p>

            <a href="#" class="admin-nav-link" role="menuitem">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                Pengguna
            </a>

            <a
                href="{{ url('/admin/pesanan') }}"
                class="admin-nav-link{{ $aktifAdmin === 'pesanan' ? ' is-active' : '' }}"
                role="menuitem"
            >
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pesanan
            </a>

            <div class="admin-nav-divider"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="admin-nav-link is-danger"
                    role="menuitem"
                    style="width:100%; border:0; background:none; text-align:left; cursor:pointer;"
                >
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <path d="M15 17l5-5-5-5M20 12H9M13 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Keluar
                </button>
            </form>

        </nav>
    </div>
</header>
