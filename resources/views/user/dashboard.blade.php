<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Dashboard — KlikMedis</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="shop-body">

    @php
        $aktif = 'beranda';

        $namaLengkap = trim(auth()->user()->nama ?? '');
        $bagianNama = preg_split('/\s+/', $namaLengkap);
        $namaDepan = $bagianNama[0] ?? '';
    @endphp

    @include('partials.topbar-user', ['aktif' => $aktif])

    <div class="shell">

        <!-- Content -->
        <main class="content">

            <div class="page-heading">
                <div>
                    <h1>Selamat datang, {{ $namaDepan }} 👋</h1>
                    <p>Yuk cek promo hari ini dan lengkapi kebutuhan kesehatanmu.</p>
                </div>
            </div>

            @if (!empty($pengumuman))
                <div class="pengumuman-list" aria-label="Pengumuman">

                    @foreach ($pengumuman as $g)
                        <div class="pengumuman-item is-{{ $g['tipe'] }}">

                            <span class="pengumuman-tag">
                                {{ \App\Models\Pengumuman::TIPE[$g['tipe']] ?? 'Info' }}
                            </span>

                            <div>
                                <strong>{{ $g['judul'] }}</strong>
                                <p>{{ $g['isi'] }}</p>
                            </div>

                        </div>
                    @endforeach

                </div>
            @endif

            <div class="promo-banner">
                <div>
                    <h3>Diskon Spesial Produk Vitamin</h3>
                    <p>Jaga daya tahan tubuh, mulai dari sekarang! Diskon hingga 30%.</p>
                </div>
            </div>

            <div class="section-heading">
                <h2>Semua Produk</h2>
            </div>

            @include('partials.flash', [
                'pesanFlash' => $flashFavorit ?? null
            ])

            @include('partials.flash', [
                'pesanFlash' => $flashKeranjang ?? null
            ])

            <div class="product-grid">

                @foreach ($produk as $p)
                    @include('partials.product-card', [
                        'p' => $p,
                        'kembali' => '/dashboard',
                        'varian' => 'katalog',
                        'favoritId' => $favoritId ?? []
                    ])
                @endforeach

            </div>

        </main>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>

</body>
</html>
