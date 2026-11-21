
@php
    /*
     * Partial: satu kartu produk.
     *
     * Variabel:
     * $p          = data produk
     * $kembali    = halaman tujuan setelah aksi
     * $varian     = katalog atau favorit
     * $favoritId  = daftar ID produk favorit pengguna
     */

    $varian = $varian ?? 'katalog';
    $kembali = $kembali ?? '/dashboard';

    $sudahFavorit = in_array(
        (int) $p['id'],
        $favoritId ?? [],
        true
    );

    $harga = 'Rp ' . number_format(
        (int) $p['harga'],
        0,
        ',',
        '.'
    );
@endphp

<div class="product-card" @if ($varian === 'favorit') data-fav-item @endif>

    @if ($varian === 'favorit')

        {{-- Tombol hapus produk dari favorit --}}
        <form method="POST"
              action="{{ route('favorit') }}"
              class="fav-form"
              onsubmit="return confirm('Hapus produk ini dari favorit?');">

            @csrf

            <input type="hidden" name="aksi" value="hapus">

            <input type="hidden"
                   name="produk_id"
                   value="{{ (int) $p['id'] }}">

            <input type="hidden"
                   name="kembali"
                   value="/favorit">

            <button class="fav-toggle is-active"
                    type="submit"
                    aria-label="Hapus dari favorit">

                <svg width="15" height="15"
                     viewBox="0 0 24 24"
                     fill="currentColor">
                    <path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"/>
                </svg>

            </button>
        </form>

        {{-- Gambar produk --}}
<div class="product-thumb">
    @if (!empty($p['foto']))
        <img
            src="{{ asset('storage/' . $p['foto']) }}"
            alt="Foto {{ $p['nama'] }}"
            loading="lazy"
        >
    @else
        <svg width="40" height="40"
             viewBox="0 0 24 24"
             fill="none">

            <rect x="5" y="3" width="14" height="18"
                  rx="2"
                  stroke="currentColor"
                  stroke-width="1.6"/>

            <path d="M9 8h6M9 12h6M9 16h3"
                  stroke="currentColor"
                  stroke-width="1.6"
                  stroke-linecap="round"/>
        </svg>
    @endif
</div>
{{-- Nama produk --}}
<div class="product-name">
    {{ $p['nama'] }}
</div>

{{-- Keterangan produk --}}
<div class="product-meta">
    {{ $p['ket'] }}
</div>

{{-- Harga produk --}}
<div class="product-price">
    {{ $harga }}
</div>

    @else

        {{-- Tampilan produk katalog --}}
<div class="product-thumb">
    @if (!empty($p['foto']))
        <img
            src="{{ asset('storage/' . $p['foto']) }}"
            alt="Foto {{ $p['nama'] }}"
            loading="lazy"
        >
    @else
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
            <rect x="5" y="3" width="14" height="18" rx="2"
                  stroke="currentColor" stroke-width="1.6"/>
            <path d="M9 8h6M9 12h6M9 16h3"
                  stroke="currentColor" stroke-width="1.6"
                  stroke-linecap="round"/>
        </svg>
    @endif
</div>

<div class="product-name">
    {{ $p['nama'] }}
</div>

<div class="product-meta">
    {{ $p['ket'] }}
</div>

<div class="price-row">

    <div class="product-price">
        {{ $harga }}
    </div>

    <form method="POST"
          action="{{ route('favorit') }}"
          class="fav-form"
          @if ($sudahFavorit)
              onsubmit="return confirm('Hapus produk ini dari favorit?');"
          @endif>

        @csrf

        <input type="hidden"
               name="aksi"
               value="{{ $sudahFavorit ? 'hapus' : 'tambah' }}">

        <input type="hidden"
               name="produk_id"
               value="{{ (int) $p['id'] }}">

        <input type="hidden"
               name="kembali"
               value="/dashboard">

        <button class="fav-btn{{ $sudahFavorit ? ' is-active' : '' }}"
                type="submit"
                aria-label="{{ $sudahFavorit ? 'Hapus dari favorit' : 'Tambah ke favorit' }}"
                aria-pressed="{{ $sudahFavorit ? 'true' : 'false' }}">

            <svg width="15" height="15"
                 viewBox="0 0 24 24"
                 fill="none">

                <path d="M12 21s-7-4.35-9.5-8.5C.5 8.5 3 5 6.5 5c2 0 3.3 1 5.5 3 2.2-2 3.5-3 5.5-3 3.5 0 6 3.5 4 7.5C19 16.65 12 21 12 21Z"
                      stroke="currentColor"
                      stroke-width="1.8"
                      stroke-linejoin="round"/>
            </svg>

        </button>
    </form>

</div>
    @endif

    {{-- Tombol tambah ke keranjang --}}
    <form method="POST"
          action="{{ route('keranjang') }}"
          class="add-form">

        @csrf

        <input type="hidden" name="aksi" value="tambah">

        <input type="hidden"
               name="produk_id"
               value="{{ (int) $p['id'] }}">

        <input type="hidden"
               name="kembali"
               value="{{ $kembali }}">

        <button class="btn-add" type="submit">
            + Keranjang
        </button>

    </form>

</div>
