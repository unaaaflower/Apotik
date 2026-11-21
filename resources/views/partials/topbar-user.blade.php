
<header class="topbar">

    {{-- LOGO --}}
    <a href="{{ route('dashboard') }}" class="logo">
        <span class="logo-mark">
            <img src="{{ asset('images/logo.png') }}" alt="Logo KlikMedis">
        </span>

        <span class="logo-text logo-text-dark">
            KlikMedis<br>
            <small>Apotek Online</small>
        </span>
    </a>

    {{-- KOLOM PENCARIAN --}}
    <div class="topbar-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <circle cx="11" cy="11" r="7"
                stroke="currentColor" stroke-width="1.8"/>
            <path d="M21 21l-3.8-3.8"
                stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round"/>
        </svg>

        <input type="text" id="site-search" name="q"
            placeholder="Cari obat, vitamin, atau produk kesehatan..."
            aria-label="Cari produk" autocomplete="off">
    </div>

    {{-- AKSI BAGIAN KANAN --}}
    <div class="topbar-actions">

        @php
            $jmlKeranjang = \Illuminate\Support\Facades\DB::table('keranjang')
                ->where('user_id', auth()->id())
                ->count();

            $namaUser = trim(auth()->user()->nama ?? '');

            $inisialUser = collect(explode(' ', $namaUser))
                ->filter()
                ->take(2)
                ->map(fn ($kata) => mb_substr($kata, 0, 1))
                ->implode('');
        @endphp

        {{-- IKON KERANJANG DI LUAR DROPDOWN --}}
        <a href="{{ route('keranjang') }}"
            class="icon-btn{{ ($aktif ?? '') === 'keranjang' ? ' is-active' : '' }}"
            aria-label="Keranjang">

            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.6a2 2 0 0 0 2-1.6L21 8H6"
                    stroke="currentColor" stroke-width="1.8"
                    stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="10" cy="21" r="1.4" fill="currentColor"/>
                <circle cx="17" cy="21" r="1.4" fill="currentColor"/>
            </svg>

            @if ($jmlKeranjang > 0)
                <span class="badge-count">{{ $jmlKeranjang }}</span>
            @endif
        </a>

        {{-- TOMBOL HAMBURGER --}}
        <button type="button" class="icon-btn nav-toggle"
            id="nav-toggle"
            aria-label="Buka menu"
            aria-haspopup="true"
            aria-expanded="false"
            aria-controls="nav-menu">

            <svg class="icon-open" width="18" height="18"
                viewBox="0 0 24 24" fill="none">
                <path d="M4 7h16M4 12h16M4 17h16"
                    stroke="currentColor" stroke-width="1.9"
                    stroke-linecap="round"/>
            </svg>

            <svg class="icon-close" width="18" height="18"
                viewBox="0 0 24 24" fill="none">
                <path d="M6 6l12 12M18 6L6 18"
                    stroke="currentColor" stroke-width="1.9"
                    stroke-linecap="round"/>
            </svg>
        </button>

        {{-- DROPDOWN MENU USER --}}
        <nav class="nav-menu user-nav-menu"
            id="nav-menu"
            role="menu"
            aria-label="Menu pengguna">

            {{-- INFORMASI AKUN --}}
            <div class="user-account" role="presentation">

                <div class="user-avatar">
                    {{ $inisialUser }}
                </div>

                <div class="user-account-info">
                    <div class="user-account-name">
                        {{ $namaUser }}
                    </div>

                    <div class="user-account-role">
                        Pengguna
                    </div>
                </div>

            </div>

            <div class="user-nav-divider"></div>

            {{-- MENU UTAMA --}}
            <p class="nav-menu-title">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
                class="nav-menu-link{{ ($aktif ?? '') === 'beranda' ? ' is-active' : '' }}"
                role="menuitem">

                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="8" height="8" rx="2"
                        stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13" y="3" width="8" height="8" rx="2"
                        stroke="currentColor" stroke-width="1.7"/>
                    <rect x="3" y="13" width="8" height="8" rx="2"
                        stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13" y="13" width="8" height="8" rx="2"
                        stroke="currentColor" stroke-width="1.7"/>
                </svg>

                Beranda
            </a>

            <a href="{{ route('favorit') }}"
                class="nav-menu-link{{ ($aktif ?? '') === 'favorit' ? ' is-active' : '' }}"
                role="menuitem">

                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"
                        stroke="currentColor" stroke-width="1.7"
                        stroke-linejoin="round"/>
                </svg>

                Produk Favorit
            </a>

            {{-- LAINNYA --}}
            <p class="nav-menu-title">Lainnya</p>

            <a href="{{ route('pengingat') }}"
                class="nav-menu-link{{ ($aktif ?? '') === 'pengingat' ? ' is-active' : '' }}"
                role="menuitem">

                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9"
                        stroke="currentColor" stroke-width="1.7"/>
                    <path d="M12 7v5l3 2"
                        stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round"/>
                </svg>

                Pengingat Obat
            </a>

            <a href="{{ route('profil') }}"
                class="nav-menu-link{{ ($aktif ?? '') === 'profil' ? ' is-active' : '' }}"
                role="menuitem">

                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="3.6"
                        stroke="currentColor" stroke-width="1.7"/>
                    <path d="M4.5 20c1.4-3.6 4.4-5.5 7.5-5.5s6.1 1.9 7.5 5.5"
                        stroke="currentColor" stroke-width="1.7"
                        stroke-linecap="round"/>
                </svg>

                Profil Saya
            </a>

            <div class="user-nav-divider"></div>

            {{-- LOGOUT --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit"
                    class="nav-menu-link is-danger user-logout"
                    role="menuitem">

                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                        <path d="M15 17l5-5-5-5M20 12H9M13 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7"
                            stroke="currentColor" stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"/>
                    </svg>

                    Keluar
                </button>
            </form>

        </nav>

    </div>
</header>
