@php
    $aktif = 'keranjang';
    $formatRupiah = fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Pembayaran — KlikMedis</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="shop-body">

    @include('partials.topbar-user')

    <div class="shell">
        <main class="content">

            <div class="checkout-steps">
                <div class="step is-done">
                    <span class="step-circle">✓</span> Keranjang
                </div>
                <span class="step-line"></span>
                <div class="step is-done">
                    <span class="step-circle">✓</span> Konfirmasi
                </div>
                <span class="step-line"></span>
                <div class="step is-active">
                    <span class="step-circle">3</span> Pembayaran (COD)
                </div>
            </div>

            <div class="checkout-grid">

                {{-- Kiri: instruksi pembayaran COD --}}
                <div>
                    <div class="card">
                        <p class="card-title">Pesanan Berhasil Dikonfirmasi</p>

                        <p class="card-subtitle">
                            Pesanan #{{ $order['kode'] }} · status:
                            <span class="status-pill pending">{{ $order['status'] }}</span>
                        </p>

                        <div class="pay-total">
                            <span>Total pembayaran</span>
                            <strong>{{ $formatRupiah($order['total']) }}</strong>
                        </div>

                        <p class="pay-heading">COD (Bayar di Tempat)</p>

                        <p class="card-subtitle">
                            Siapkan uang tunai sebesar
                            <strong>{{ $formatRupiah($order['total']) }}</strong>
                            saat pesanan tiba. Tidak ada pembayaran yang perlu dilakukan sekarang.
                        </p>

                        <a href="{{ url('/profil') }}#tab-pesanan" class="btn-primary">
                            Lihat Pesanan Saya
                        </a>

                        <p class="pay-back-wrap">
                            <a href="{{ url('/dashboard') }}" class="pay-back">
                                ← Kembali ke beranda
                            </a>
                        </p>
                    </div>
                </div>

                {{-- Kanan: ringkasan pesanan --}}
                <div>
                    <div class="card">
                        <p class="card-title">Ringkasan Pesanan</p>

                        @foreach ($items as $it)
                            <div class="checkout-item">
                                <div class="item-thumb"></div>

                                <div class="item-info">
                                    <strong>{{ $it['nama'] }}</strong>
                                    <span>
                                        {{ $it['ket'] }} × {{ (int) $it['jumlah'] }}
                                    </span>
                                </div>

                                <div class="item-price">
                                    {{ $formatRupiah((int) $it['harga'] * (int) $it['jumlah']) }}
                                </div>
                            </div>
                        @endforeach

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span>{{ $formatRupiah($order['subtotal']) }}</span>
                        </div>

                        <div class="summary-line">
                            <span>Ongkos Kirim ({{ $order['kurir'] }})</span>
                            <span>{{ $formatRupiah($order['ongkir']) }}</span>
                        </div>

                        <div class="summary-line total">
                            <span>Total</span>
                            <span>{{ $formatRupiah($order['total']) }}</span>
                        </div>

                        <p class="pay-note">
                            Dikirim ke:
                            <strong>{{ $order['alamat_nama'] }}</strong>
                            · {{ $order['alamat_telp'] }}
                            <br>
                            {{ $order['alamat_teks'] }}
                        </p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>
</body>

</html>
