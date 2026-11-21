<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">

    <title>Produk Favorit — KlikMedis</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="shop-body">

    @php
        $aktif = 'favorit';
    @endphp

    @include('partials.topbar-user')

    <div class="shell">

        <main class="content">

            <div class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Beranda</a>
                &nbsp;/&nbsp; Produk Favorit
            </div>

            <div class="page-heading">
                <div>
                    <h1>Produk Favorit</h1>
                    <p>
                        Produk kesehatan yang kamu simpan supaya mudah dibeli lagi.
                    </p>
                </div>
            </div>

            {{-- Notifikasi favorit --}}
            @include('partials.flash', [
                'pesanFlash' => $flashFavorit
            ])

            {{-- Notifikasi keranjang --}}
            @include('partials.flash', [
                'pesanFlash' => $flashKeranjang
            ])

            @if ($produk)

                <div class="product-grid" id="favorit-grid">

                    @foreach ($produk as $p)

                        @include('partials.product-card', [
                            'p' => $p,
                            'kembali' => '/favorit',
                            'varian' => 'favorit',
                        ])

                    @endforeach

                </div>

            @else

                {{-- Tampilan ketika belum ada produk favorit --}}
                <div class="card empty-state" id="favorit-empty">

                    <span class="empty-icon">
                        <svg width="26" height="26"
                             viewBox="0 0 24 24"
                             fill="none">
                            <path
                                d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"
                                stroke="currentColor"
                                stroke-width="1.7"/>
                        </svg>
                    </span>

                    <h3>Belum ada produk favorit</h3>

                    <p>
                        Tandai produk dengan ikon hati di halaman Beranda
                        supaya muncul di sini.
                    </p>

                    <a href="{{ url('/dashboard') }}" class="btn-outline">
                        Jelajahi Produk
                    </a>

                </div>

            @endif

        </main>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>

</body>
</html>
