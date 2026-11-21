
@php
    $aktifAdmin = 'promo';
    $judul = 'Kelola Promo';
    $breadcrumb = 'Dashboard / Kelola Promo';

    $formJson = static fn ($data) => json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    );

    $hariIni = date('Y-m-d');

    $formatRupiah = static fn ($angka) =>
        'Rp ' . number_format((int) $angka, 0, ',', '.');

    $formatTanggal = static fn ($tanggal) =>
        date('d/m/Y', strtotime($tanggal));
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Kelola Promo — Admin KlikMedis</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body class="admin-body">

    <div class="admin-shell">

        <div>
            @include('partials.admin-header')

            <main class="admin-main">

                <div class="admin-page-heading">
                    <div>
                        <h1>Promo untuk Pengguna</h1>
                        <p>Tambah, lihat, ubah, atau hapus kode promo dan diskon. Data tersimpan di database.</p>
                    </div>
                </div>

                @if ($flash)
                    <div class="admin-alert{{ $flash['ok'] ? '' : ' is-error' }}" role="status">
                        {{ $flash['pesan'] }}
                    </div>
                @endif

                <div class="admin-card">

                    <form class="admin-toolbar" method="get" action="{{ url('/admin/promo') }}">
                        <div class="admin-search">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M21 21l-3.8-3.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>

                            <input
                                type="text"
                                name="q"
                                id="promo-search"
                                value="{{ $q }}"
                                maxlength="50"
                                placeholder="Cari nama atau kode promo..."
                                aria-label="Cari promo"
                            >
                        </div>

                        <select class="admin-filter" name="status" id="promo-filter-status" aria-label="Filter status">
                            <option value="">Semua Status</option>
                            <option value="Aktif"{{ $status === 'Aktif' ? ' selected' : '' }}>Aktif</option>
                            <option value="Nonaktif"{{ $status === 'Nonaktif' ? ' selected' : '' }}>Nonaktif</option>
                        </select>

                        <button type="submit" class="admin-btn-ghost">Cari</button>

                        <button type="button" class="admin-btn-primary" id="btn-tambah-promo">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                            </svg>
                            Tambah Promo
                        </button>
                    </form>

                    <div class="admin-table-wrap">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Promo</th>
                                    <th>Kode</th>
                                    <th>Diskon</th>
                                    <th>Min. Belanja</th>
                                    <th>Berlaku Untuk</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="promo-tbody">

                                @if (!$daftar)
                                    <tr class="admin-empty-row">
                                        <td colspan="8">
                                            {{ ($q !== '' || $status !== '') ? 'Tidak ada promo yang cocok.' : 'Belum ada promo.' }}
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($daftar as $p)
                                    @php
                                        $data = [
                                            'id' => (int) $p['id'],
                                            'nama' => $p['nama'],
                                            'kode' => $p['kode'],
                                            'jenis' => $p['jenis'],
                                            'nilai' => (int) $p['nilai'],
                                            'minimal' => (int) $p['minimal'],
                                            'target' => $p['target'],
                                            'mulai' => $p['mulai'],
                                            'selesai' => $p['selesai'],
                                            'aktif' => (bool) $p['aktif'],
                                        ];

                                        $berakhir = $p['selesai'] < $hariIni;
                                    @endphp

                                    <tr data-promo="{{ $formJson($data) }}">
                                        <td>
                                            <strong>{{ $p['nama'] }}</strong>
                                        </td>

                                        <td>{{ $p['kode'] }}</td>

                                        <td>
                                            @if ($p['jenis'] === 'persen')
                                                {{ (int) $p['nilai'] }}%
                                            @else
                                                {{ $formatRupiah($p['nilai']) }}
                                            @endif
                                        </td>

                                        <td>
                                            {{ (int) $p['minimal'] > 0 ? $formatRupiah($p['minimal']) : '-' }}
                                        </td>

                                        <td>{{ $p['target'] }}</td>

                                        <td>
                                            {{ $formatTanggal($p['mulai']) }}
                                            –
                                            {{ $formatTanggal($p['selesai']) }}
                                        </td>

                                        <td>
                                            @if (!$p['aktif'])
                                                <span class="badge neutral">Nonaktif</span>
                                            @elseif ($berakhir)
                                                <span class="badge warning">Berakhir</span>
                                            @else
                                                <span class="badge success">Aktif</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="admin-row-actions">

                                                <button
                                                    type="button"
                                                    class="admin-icon-action"
                                                    data-edit-promo
                                                    aria-label="Ubah promo"
                                                >
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                                        <path d="M12 20h9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>

                                                <form
                                                    method="post"
                                                    action="{{ url('/admin/promo') }}"
                                                    class="admin-inline-form"
                                                    data-konfirmasi="{{ 'Hapus promo "' . $p['nama'] . '" (' . $p['kode'] . ')? Tindakan ini tidak bisa dibatalkan.' }}"
                                                    onsubmit="return confirm(this.dataset.konfirmasi);"
                                                >
                                                    @csrf

                                                    <input type="hidden" name="aksi" value="hapus">
                                                    <input type="hidden" name="id" value="{{ (int) $p['id'] }}">

                                                    <button
                                                        type="submit"
                                                        class="admin-icon-action danger"
                                                        aria-label="Hapus promo"
                                                    >
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

    <!-- Modal: Tambah / Ubah Promo -->
    <div class="admin-modal-overlay" id="promo-modal-overlay">
        <div class="admin-modal">

            <div class="admin-modal-head">
                <div>
                    <h2 id="promo-modal-title">Tambah Promo</h2>
                    <p>Atur ketentuan promo yang akan diberikan ke pengguna.</p>
                </div>

                <button
                    type="button"
                    class="admin-modal-close"
                    id="promo-modal-close"
                    aria-label="Tutup"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <form
                id="promo-form"
                method="post"
                action="{{ url('/admin/promo') }}"
                novalidate
                @if ($isian !== null)
                    data-isian="{{ $formJson($isian) }}"
                @endif
            >
                @csrf

                <input type="hidden" name="aksi" id="promo-aksi" value="tambah">
                <input type="hidden" name="id" id="promo-id" value="">

                <div
                    class="admin-alert is-error"
                    id="promo-form-error"
                    role="alert"
                    @if ($galat === '') hidden @endif
                >{{ $galat }}</div>

                <div class="admin-field">
                    <label for="promo-nama">Nama Promo</label>
                    <input
                        type="text"
                        id="promo-nama"
                        name="nama"
                        maxlength="100"
                        placeholder="Contoh: Diskon Spesial Vitamin"
                    >
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="promo-kode">Kode Promo</label>
                        <input
                            type="text"
                            id="promo-kode"
                            name="kode"
                            maxlength="20"
                            placeholder="Contoh: SEHAT30"
                            style="text-transform:uppercase;"
                            autocomplete="off"
                        >
                    </div>

                    <div class="admin-field">
                        <label for="promo-jenis">Jenis Diskon</label>
                        <select id="promo-jenis" name="jenis">
                            @foreach ($jenis as $nilaiJenis => $labelJenis)
                                <option value="{{ $nilaiJenis }}">{{ $labelJenis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="promo-nilai">Nilai Diskon</label>
                        <input
                            type="number"
                            id="promo-nilai"
                            name="nilai"
                            min="1"
                            step="1"
                            placeholder="Contoh: 30"
                        >
                    </div>

                    <div class="admin-field">
                        <label for="promo-minimal">Minimal Pembelian (Rp)</label>
                        <input
                            type="number"
                            id="promo-minimal"
                            name="minimal"
                            min="0"
                            step="1"
                            placeholder="0"
                        >
                    </div>
                </div>

                <div class="admin-field">
                    <label for="promo-target">Berlaku Untuk</label>
                    <select id="promo-target" name="target">
                        @foreach ($target as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="admin-field-row">
                    <div class="admin-field">
                        <label for="promo-mulai">Tanggal Mulai</label>
                        <input type="date" id="promo-mulai" name="mulai">
                    </div>

                    <div class="admin-field">
                        <label for="promo-selesai">Tanggal Berakhir</label>
                        <input type="date" id="promo-selesai" name="selesai">
                    </div>
                </div>

                <div class="admin-toggle-row">
                    <div>
                        <div class="toggle-label">Status Aktif</div>
                        <div class="toggle-hint">Nonaktifkan untuk menghentikan promo sementara.</div>
                    </div>

                    <label class="toggle-switch">
                        <input type="checkbox" id="promo-status" name="aktif" value="1" checked>
                        <span class="track"></span>
                    </label>
                </div>

                <div class="admin-modal-actions">
                    <button type="button" class="admin-btn-ghost" id="promo-modal-cancel">
                        Batal
                    </button>

                    <button type="submit" class="admin-btn-primary" id="promo-modal-submit">
                        Simpan Promo
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Toast feedback -->
    <div class="admin-toast" id="admin-toast" role="status" aria-live="polite"></div>

    <script src="{{ asset('js/admin.js') }}"></script>

</body>
</html>
