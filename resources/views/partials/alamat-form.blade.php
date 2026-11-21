@php
    $tampilForm = (
        !empty($flashAlamat)
        && empty($flashAlamat['ok'])
        && ($flashAlamat['label'] ?? $kunci) === $kunci
    );

    $tampilPesanAlamat = (
        !empty($flashAlamat)
        && ($flashAlamat['label'] ?? $kunci) === $kunci
    );
@endphp

@if ($tampilPesanAlamat)
    <div
        class="form-message{{ $flashAlamat['ok'] ? '' : ' is-error' }}"
        role="status"
        style="text-align:left;"
    >
        {{ $flashAlamat['pesan'] }}
    </div>
@endif

<form
    method="POST"
    action="{{ url($aksiUrl) }}"
    class="addr-form"
    data-addr-form
    @if (!$tampilForm) hidden @endif
>
    @csrf

    <input type="hidden" name="aksi" value="ubah_alamat">
    <input type="hidden" name="label" value="{{ $kunci }}">

    <div class="field">
        <label for="a-nama-{{ $kunci }}">Nama penerima</label>
        <input
            type="text"
            id="a-nama-{{ $kunci }}"
            name="nama"
            value="{{ old('nama', $al['nama']) }}"
            maxlength="100"
            required
        >
    </div>

    <div class="field">
        <label for="a-telp-{{ $kunci }}">No. HP</label>
        <input
            type="tel"
            id="a-telp-{{ $kunci }}"
            name="telp"
            value="{{ old('telp', $al['telp']) }}"
            maxlength="20"
            required
        >
    </div>

    <div class="field field-textarea">
        <label for="a-alamat-{{ $kunci }}">Alamat lengkap</label>
        <textarea
            id="a-alamat-{{ $kunci }}"
            name="alamat"
            rows="3"
            maxlength="300"
            required
        >{{ old('alamat', $al['alamat']) }}</textarea>
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
