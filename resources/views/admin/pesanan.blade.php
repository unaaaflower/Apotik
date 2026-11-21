
<?php

$lencanaStatus = [
    'Selesai' => 'success',
    'Dikirim' => 'success',
    'Diproses' => 'warning',
    'Menunggu Pembayaran' => 'warning',
    'Dibatalkan' => 'danger',
];

$formJson = static fn ($data) => json_encode(
    $data,
    JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
);

$idByNama = [];

foreach ($katalog as $k) {
    $idByNama[$k['nama']] = (int) $k['id'];
}

$formatRupiah = static fn ($angka) => 'Rp ' . number_format((int) $angka, 0, ',', '.');
$tanggalIndonesia = static fn ($tanggal) => date('d/m/Y', strtotime($tanggal));

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kelola Pesanan — Admin KlikMedis</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body class="admin-body">

<div class="admin-shell">

    <div>
        @include('partials.admin-header', [
            'aktifAdmin' => 'pesanan',
            'judul' => 'Kelola Pesanan',
            'breadcrumb' => 'Dashboard / Kelola Pesanan'
        ])

        <main class="admin-main">

            <div class="admin-page-heading">
                <div>
                    <h1>Daftar Pesanan</h1>
                    <p>Tambah, lihat, ubah, atau hapus pesanan pelanggan. Data tersimpan di database.</p>
                </div>
            </div>

            @if ($flash)
                <div class="admin-alert{{ !($flash['success'] ?? false) ? ' is-error' : '' }}" role="status">
                    {{ $flash['message'] ?? '' }}
                </div>
            @endif

            <div class="admin-card">

                <form class="admin-toolbar" method="get" action="{{ url('/admin/pesanan') }}">

                    <div class="admin-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>

                        <input
                            type="text"
                            name="q"
                            id="pesanan-search"
                            value="{{ $q }}"
                            maxlength="50"
                            placeholder="Cari kode atau nama pelanggan..."
                            aria-label="Cari pesanan"
                        >
                    </div>

                   <select
    class="admin-filter"
    name="status"
    id="pesanan-filter-status"
    aria-label="Filter status"
    onchange="this.form.submit()"
>
                        <option value="">Semua Status</option>

                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>

                    <button type="button" class="admin-btn-primary" id="btn-tambah-pesanan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                        Tambah Pesanan
                    </button>

                </form>

                <div class="admin-table-wrap">

                    <table class="admin-table">

                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody id="pesanan-tbody">

                            @if (!$daftar)
                                <tr class="admin-empty-row">
                                    <td colspan="8">
                                        {{ ($q !== '' || $status !== '') ? 'Tidak ada pesanan yang cocok.' : 'Belum ada pesanan.' }}
                                    </td>
                                </tr>
                            @endif

                            @foreach ($daftar as $ps)

                                @php
                                    $rinci = $items[(int) $ps['id']] ?? [];

                                    $data = [
                                        'id' => (int) $ps['id'],
                                        'kode' => $ps['kode'],
                                        'user_id' => (int) $ps['user_id'],
                                        'pelanggan' => $ps['pelanggan'],
                                        'email' => $ps['email'],
                                        'tanggal' => $tanggalIndonesia($ps['created_at']),
                                        'kurir' => $ps['kurir'],
                                        'shipping' => (int) $ps['ongkir'],
                                        'ongkir' => $formatRupiah($ps['ongkir']),
                                        'subtotal' => $formatRupiah($ps['subtotal']),
                                        'total' => $formatRupiah($ps['total']),
                                        'status' => $ps['status'],
                                        'alamat_nama' => $ps['alamat_nama'],
                                        'alamat_telp' => $ps['alamat_telp'],
                                        'alamat_teks' => $ps['alamat_teks'],
                                        'items' => array_map(static fn ($it) => [
                                            'produk_id' => $idByNama[$it['nama']] ?? '',
                                            'nama' => $it['nama'],
                                            'ket' => $it['ket'],
                                            'harga' => $formatRupiah($it['harga']),
                                            'jumlah' => (int) $it['jumlah'],
                                        ], $rinci),
                                    ];
                                @endphp

                                <tr data-pesanan="{{ $formJson($data) }}">

                                    <td><strong>#{{ $ps['kode'] }}</strong></td>

                                    <td>
                                        <div class="admin-cell-product">
                                            <div>
                                                <strong>{{ $ps['pelanggan'] }}</strong>
                                                <span>{{ $ps['email'] }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ $tanggalIndonesia($ps['created_at']) }}</td>

                                    <td>{{ count($rinci) }} produk</td>

                                    <td>{{ $formatRupiah($ps['total']) }}</td>

                                    <td>COD</td>

                                    <td>
                                        <span class="badge {{ $lencanaStatus[$ps['status']] ?? 'neutral' }}">
                                            {{ $ps['status'] }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="admin-row-actions">

                                            <button
                                                type="button"
                                                class="admin-icon-action"
                                                data-detail-pesanan
                                                aria-label="Lihat detail pesanan"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                                                </svg>
                                            </button>

                                            <button
                                                type="button"
                                                class="admin-icon-action"
                                                data-edit-pesanan
                                                aria-label="Ubah pesanan"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                                    <path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                                </svg>
                                            </button>

                                            <form
                                                method="post"
                                                action="{{ url('/admin/pesanan') }}"
                                                class="admin-inline-form"
                                                data-konfirmasi="{{ 'Hapus pesanan #' . $ps['kode'] . ' milik ' . $ps['pelanggan'] . '? Tindakan ini tidak bisa dibatalkan.' }}"
                                                onsubmit="return confirm(this.dataset.konfirmasi);"
                                            >
                                                @csrf

                                                <input type="hidden" name="aksi" value="hapus">
                                                <input type="hidden" name="id" value="{{ (int) $ps['id'] }}">

                                                <button type="submit" class="admin-icon-action danger" aria-label="Hapus pesanan">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                                        <path d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </main>
    </div>

</div>

<!-- Modal Tambah / Ubah Pesanan -->

<div class="admin-modal-overlay" id="pesanan-modal-overlay">

    <div class="admin-modal">

        <div class="admin-modal-head">
            <div>
                <h2 id="pesanan-modal-title">Tambah Pesanan</h2>
                <p>Lengkapi detail pesanan di bawah ini. Harga dihitung otomatis dari katalog.</p>
            </div>

            <button type="button" class="admin-modal-close" id="pesanan-modal-close" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <form
            id="pesanan-form"
            method="post"
            action="{{ url('/admin/pesanan') }}"
            novalidate
            @if ($isian !== null)
                data-isian="{{ $formJson($isian) }}"
            @endif )
        >

            @csrf

            <input type="hidden" name="aksi" id="pesanan-aksi" value="tambah">
            <input type="hidden" name="id" id="pesanan-id" value="">

            <div
                class="admin-alert is-error"
                id="pesanan-form-error"
                role="alert"
                @if ($galat === '') hidden @endif
            >
                {{ $galat }}
            </div>

            <div class="admin-field">
                <label for="pesanan-user">Pelanggan</label>

                <select id="pesanan-user" name="user_id">
                    <option value="">— Pilih pelanggan —</option>

                    @foreach ($pelanggan as $u)
                        <option value="{{ (int) $u['id'] }}">
                            {{ $u['nama'] }} ({{ $u['email'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label>Produk</label>

                <div id="pesanan-items"></div>

                <button type="button" class="admin-btn-ghost" id="btn-tambah-baris">
                    + Tambah produk
                </button>
            </div>

            <div class="admin-field-row">

                <div class="admin-field">
                    <label for="pesanan-shipping">Pengiriman</label>

                    <select id="pesanan-shipping" name="shipping">
                        <option value="">— Pilih pengiriman —</option>

                        @foreach ($opsi as $ongkir => $label)
                            <option value="{{ (int) $ongkir }}">
                                {{ $label }} — {{ $formatRupiah($ongkir) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field">
                    <label for="pesanan-status">Status Pesanan</label>

                    <select id="pesanan-status" name="status">
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" {{ $st === 'Diproses' ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="admin-field-row">

                <div class="admin-field">
                    <label for="pesanan-alamat-nama">Nama Penerima</label>

                    <input
                        type="text"
                        id="pesanan-alamat-nama"
                        name="alamat_nama"
                        maxlength="100"
                        placeholder="Contoh: Andi Pratama"
                    >
                </div>

                <div class="admin-field">
                    <label for="pesanan-alamat-telp">No. HP Penerima</label>

                    <input
                        type="text"
                        id="pesanan-alamat-telp"
                        name="alamat_telp"
                        maxlength="20"
                        placeholder="0812 3456 7890"
                    >
                </div>

            </div>

           <textarea
    id="pesanan-alamat-teks"
    name="alamat_teks"
    maxlength="300"
    rows="5"
    placeholder="Masukkan alamat lengkap: nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota, dan kode pos."
    style="min-height: 120px; resize: vertical;"
></textarea>


            <div class="admin-modal-actions">
                <button type="button" class="admin-btn-ghost" id="pesanan-modal-cancel">
                    Batal
                </button>

                <button type="submit" class="admin-btn-primary" id="pesanan-modal-submit">
                    Simpan Pesanan
                </button>
            </div>

        </form>

    </div>

</div>

<!-- Template Baris Produk -->

<template id="tpl-baris-produk">

    <div class="admin-item-row">

        <select name="produk_id[]" aria-label="Produk">
            <option value="">— Pilih produk —</option>

            @foreach ($katalog as $k)
                <option value="{{ (int) $k['id'] }}" data-harga="{{ (int) $k['harga'] }}">
                    {{ $k['nama'] }} — {{ $formatRupiah($k['harga']) }}
                </option>
            @endforeach
        </select>

        <input
            type="number"
            name="jumlah[]"
            min="1"
            max="99"
            value="1"
            aria-label="Jumlah"
        >

        <button type="button" class="admin-icon-action danger" data-hapus-baris aria-label="Buang baris produk">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>

    </div>

</template>

<!-- Modal Detail Pesanan -->

<div class="admin-modal-overlay" id="pesanan-detail-overlay">

    <div class="admin-modal">

        <div class="admin-modal-head">
            <div>
                <h2 id="pesanan-detail-title">Detail Pesanan</h2>
                <p id="pesanan-detail-sub"></p>
            </div>

            <button type="button" class="admin-modal-close" id="pesanan-detail-close" aria-label="Tutup">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                    <path d="M6 6l12 12M18 12 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <div id="pesanan-detail-body" class="admin-detail-list"></div>

        <div class="admin-modal-actions">
            <button type="button" class="admin-btn-ghost" id="pesanan-detail-tutup">
                Tutup
            </button>
        </div>

    </div>

</div>

<!-- Toast -->

<div class="admin-toast" id="admin-toast" role="status" aria-live="polite"></div>

<script src="{{ asset('js/admin.js') }}"></script>

</body>
</html>
