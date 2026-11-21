@php
    $aktif = 'keranjang';
    $ongkirAwal = (int) array_key_first($opsiPengiriman);
    $formatRupiah = fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');
    $formatTanggal = fn ($hari) => now()->addDays($hari)->format('d/m/Y');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Konfirmasi — KlikMedis</title>
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
                <div class="step is-active">
                    <span class="step-circle">2</span> Konfirmasi
                </div>
            </div>

            {{-- Form utama untuk konfirmasi pesanan --}}
            <form id="checkout-form" method="POST" action="{{ route('checkout') }}">
                @csrf
            </form>

            <div class="checkout-grid" id="checkout-grid">

                {{-- Bagian kiri: alamat, pengiriman, pembayaran --}}
                <div>
                    <div class="card">
                        <div class="card-head">
                            <p class="card-title">Alamat Pengiriman</p>
                            <button type="button" class="btn-outline" data-addr-edit>
                                Ubah
                            </button>
                        </div>

                        <p data-addr-view style="margin:0; font-size:13px; color:var(--ink-soft); line-height:1.6;">
                            <strong style="color:var(--ink);">
                                {{ $alamat['nama'] }}
                            </strong>
                            · {{ $alamat['telp'] }}
                            <br>
                            {{ $alamat['alamat'] }}
                        </p>

                        {{-- Pesan hasil perubahan alamat --}}
                        @if ($flash && ($flash['label'] ?? '') === 'utama')
                            <div class="form-message{{ $flash['ok'] ? '' : ' is-error' }}"
                                 role="status"
                                 style="text-align:left;">
                                {{ $flash['pesan'] }}
                            </div>
                        @endif

                        {{-- Form ubah alamat --}}
                        <form method="POST"
                              action="{{ route('checkout') }}"
                              class="addr-form"
                              data-addr-form
                              @if (!$flash || ($flash['ok'] ?? true) || ($flash['label'] ?? '') !== 'utama')
                                  hidden
                              @endif>

                            @csrf

                            <input type="hidden" name="aksi" value="ubah_alamat">
                            <input type="hidden" name="label" value="utama">

                            <div class="field">
                                <label for="a-nama-utama">Nama penerima</label>
                                <input type="text"
                                       id="a-nama-utama"
                                       name="nama"
                                       value="{{ $alamat['nama'] }}"
                                       maxlength="100"
                                       required>
                            </div>

                            <div class="field">
                                <label for="a-telp-utama">No. HP</label>
                                <input type="tel"
                                       id="a-telp-utama"
                                       name="telp"
                                       value="{{ $alamat['telp'] }}"
                                       maxlength="20"
                                       required>
                            </div>

                            <div class="field field-textarea">
                                <label for="a-alamat-utama">Alamat lengkap</label>
                                <textarea id="a-alamat-utama"
                                          name="alamat"
                                          rows="3"
                                          maxlength="300"
                                          required>{{ $alamat['alamat'] }}</textarea>
                            </div>

                            <div class="addr-actions">
                                <button type="submit" class="btn-primary" style="width:auto;">
                                    Simpan Alamat
                                </button>

                                <button type="button" class="btn-outline" data-addr-cancel>
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Metode pengiriman --}}
                    <div class="card">
                        <p class="card-title">Metode Pengiriman</p>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="shipping"
                                   value="10000"
                                   form="checkout-form"
                                   checked>

                            <div class="ship-info">
                                <strong>JNE Reguler (2-3 hari)</strong>
                                <span>
                                    Estimasi tiba {{ $formatTanggal(2) }}
                                    – {{ $formatTanggal(3) }}
                                </span>
                            </div>

                            <span class="ship-price">Rp 10.000</span>
                        </label>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="shipping"
                                   value="15000"
                                   form="checkout-form">

                            <div class="ship-info">
                                <strong>JNE YES (1 hari)</strong>
                                <span>
                                    Estimasi tiba {{ $formatTanggal(1) }}
                                </span>
                            </div>

                            <span class="ship-price">Rp 15.000</span>
                        </label>
                    </div>

                    {{-- Metode pembayaran --}}
                    <div class="card">
                        <p class="card-title">Metode Pembayaran</p>

                        <label class="shipping-option">
                            <input type="radio"
                                   name="payment"
                                   value="cod"
                                   form="checkout-form"
                                   checked>

                            <div class="ship-info">
                                <strong>COD (Bayar di Tempat)</strong>
                                <span>
                                    Bayar tunai ke kurir saat pesanan tiba
                                </span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Bagian kanan: ringkasan pesanan --}}
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
                            <span id="sum-subtotal" data-subtotal="{{ (int) $subtotal }}">
                                {{ $formatRupiah($subtotal) }}
                            </span>
                        </div>

                        <div class="summary-line">
                            <span>Ongkos Kirim</span>
                            <span id="sum-shipping">
                                {{ $formatRupiah($ongkirAwal) }}
                            </span>
                        </div>

                        <div class="summary-line total">
                            <span>Total</span>
                            <span id="sum-total">
                                {{ $formatRupiah($subtotal + $ongkirAwal) }}
                            </span>
                        </div>

                        <button type="submit"
                                class="btn-primary"
                                id="pay-btn"
                                form="checkout-form">
                            Konfirmasi Pesanan
                        </button>

                        <div class="form-message{{ $errorCheckout !== '' ? ' is-error' : '' }}"
                             id="checkout-message"
                             role="status"
                             aria-live="polite">
                            {{ $errorCheckout }}
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
