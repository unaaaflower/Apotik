
@php
    $f = $isian ?? [
        'aksi' => 'tambah',
        'id' => '',
        'nama' => '',
        'dosis' => '',
        'frekuensi' => '2',
        'jam_mulai' => '07:00',
        'durasi' => '5',
        'dokter' => '',
        'catatan' => '',
    ];

    $mengubah = $f['aksi'] === 'ubah';

    $formatData = fn ($data) => json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    );
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Pengingat Obat — KlikMedis</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="shop-body">

    @php $aktif = 'pengingat'; @endphp

    @include('partials.topbar-user')

    <div class="shell">

        <main class="content">

            {{-- Breadcrumb --}}
            <div class="breadcrumb">
                <a href="{{ url('/dashboard') }}">Beranda</a>
                &nbsp;/&nbsp; Pengingat Obat
            </div>

            {{-- Judul halaman --}}
            <div class="page-heading">
                <div>
                    <h1>Pengingat Obat</h1>
                    <p>
                        Masukkan resep obatmu, lalu kami buatkan jadwal
                        pengingat minum obat otomatis.
                    </p>
                </div>
            </div>

            {{-- Pesan notifikasi --}}
            @include('partials.flash', [
                'pesanFlash' => $flash
            ])

            {{-- =========================================
                 STEP 1: FORM INPUT RESEP OBAT
            ========================================== --}}
            <div
                class="card"
                id="form-resep"
                style="width:100%; min-width:0; box-sizing:border-box;"
            >

                <p class="card-title" id="resep-judul">
                    {{ $mengubah ? 'Ubah Pengingat Obat' : 'Input Resep Obat' }}
                </p>

                <p class="card-subtitle">
                    Lengkapi detail resep sebelum jadwal pengingat dibuat.
                </p>

                <form
                    class="form-grid"
                    id="resep-form"
                    method="POST"
                    action="{{ url('/pengingat-obat') }}"
                    enctype="multipart/form-data"
                    novalidate
                >
                    @csrf

                    <input
                        type="hidden"
                        name="aksi"
                        id="resep-aksi"
                        value="{{ $mengubah ? 'ubah' : 'tambah' }}"
                    >

                    <input
                        type="hidden"
                        name="id"
                        id="resep-id"
                        value="{{ $f['id'] }}"
                    >

                    {{-- Nama obat --}}
                    <div class="field">
                        <label for="resep-nama">
                            Nama Obat
                            <span class="badge-required">Wajib</span>
                        </label>

                        <input
                            type="text"
                            id="resep-nama"
                            name="nama"
                            maxlength="100"
                            value="{{ $f['nama'] }}"
                            placeholder="Contoh: Amoxicillin 500 mg"
                            required
                        >
                    </div>

                    {{-- Dosis --}}
                    <div class="field">
                        <label for="resep-dosis">
                            Dosis Sekali Minum
                            <span class="badge-required">Wajib</span>
                        </label>

                        <input
                            type="text"
                            id="resep-dosis"
                            name="dosis"
                            maxlength="50"
                            value="{{ $f['dosis'] }}"
                            placeholder="Contoh: 1 tablet"
                            required
                        >
                    </div>

                    {{-- Frekuensi --}}
                    <div class="field">
                        <label for="resep-frekuensi">
                            Frekuensi per Hari
                        </label>

                        <select
                            id="resep-frekuensi"
                            name="frekuensi"
                        >
                            @foreach ([1, 2, 3, 4] as $n)
                                <option
                                    value="{{ $n }}"
                                    @selected((string) $n === (string) $f['frekuensi'])
                                >
                                    {{ $n }}x sehari
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jam mulai --}}
                    <div class="field">
                        <label for="resep-mulai">
                            Jam Mulai Minum
                        </label>

                        <input
                            type="time"
                            id="resep-mulai"
                            name="jam_mulai"
                            value="{{ $f['jam_mulai'] }}"
                            required
                        >
                    </div>

                    {{-- Durasi --}}
                    <div class="field">
                        <label for="resep-durasi">
                            Durasi Pengobatan (hari)
                        </label>

                        <input
                            type="number"
                            id="resep-durasi"
                            name="durasi"
                            min="1"
                            max="90"
                            step="1"
                            value="{{ $f['durasi'] }}"
                            required
                        >
                    </div>

                    {{-- Dokter --}}
                    <div class="field">
                        <label for="resep-dokter">
                            Diresepkan Oleh
                        </label>

                        <input
                            type="text"
                            id="resep-dokter"
                            name="dokter"
                            maxlength="100"
                            value="{{ $f['dokter'] }}"
                            placeholder="Contoh: dr. Rina Wulandari"
                        >
                    </div>

                    {{-- Catatan --}}
                    <div class="field full field-textarea">
                        <label for="resep-catatan">
                            Catatan Tambahan
                        </label>

                        <textarea
                            id="resep-catatan"
                            name="catatan"
                            maxlength="200"
                            placeholder="Contoh: diminum setelah makan"
                        >{{ $f['catatan'] }}</textarea>
                    </div>

                    {{-- Upload foto resep --}}
                    <div class="field full">

                        <label for="resep-foto">
                            Unggah Foto Resep (opsional)
                        </label>

                        <label
                            class="resep-upload"
                            id="resep-upload-box"
                        >
                            <input
                                type="file"
                                id="resep-foto"
                                name="foto_resep"
                                accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            >

                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <path
                                    d="M12 16V4m0 0-4 4m4-4 4 4"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                                <path
                                    d="M4 16v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                />
                            </svg>

                            <strong>
                                Klik untuk unggah foto resep
                            </strong>

                            JPG, JPEG, atau PNG. Maksimal 5 MB.

                            <div
                                class="file-name"
                                id="resep-file-name"
                            ></div>
                        </label>

                    </div>

                    {{-- Tombol simpan --}}
                    <div class="field full">

                        <button
                            type="submit"
                            class="btn-primary"
                            id="resep-submit"
                            style="width:auto;"
                        >
                            {{ $mengubah ? 'Simpan Perubahan' : 'Buat Jadwal Pengingat' }}
                        </button>

                        <button
                            type="button"
                            class="btn-outline"
                            id="resep-batal"
                            style="width:auto;{{ $mengubah ? '' : ' display:none;' }}"
                        >
                            Batal
                        </button>

                        <span
                            class="form-message{{ $galat !== '' ? ' is-error' : '' }}"
                            id="resep-message"
                            role="alert"
                            aria-live="polite"
                            style="display:inline-block; margin-left:12px; text-align:left;"
                        >
                            {{ $galat }}
                        </span>

                    </div>

                </form>

            </div>


            {{-- =========================================
                 STEP 2: DAFTAR PENGINGAT AKTIF
            ========================================== --}}
            <div
                class="card"
                id="pengingat-aktif-card"
                style="width:100%; min-width:0; box-sizing:border-box;"
            >

                <p class="card-title">
                    Pengingat Aktif
                </p>

                <p class="card-subtitle">
                    Jadwal minum obat milikmu
                    ({{ count($daftar) }} dari maksimal {{ (int) $maks }}).
                    Ubah, jeda, atau hapus sesuai kebutuhan.
                </p>

                <div
                    class="reminder-list"
                    id="reminder-list"
                    style="width:100%; min-width:0; box-sizing:border-box;"
                >

                    @foreach ($daftar as $r)

                        @php
                            $jam = \App\Models\PengingatObat::jadwal(
                                $r['jam_mulai'],
                                (int) $r['frekuensi']
                            );

                            $sisa = \App\Models\PengingatObat::sisaHari(
                                $r['tanggal_mulai'],
                                (int) $r['durasi_hari']
                            );

                            $data = [
                                'id' => (int) $r['id'],
                                'nama' => $r['nama'],
                                'dosis' => $r['dosis'],
                                'frekuensi' => (int) $r['frekuensi'],
                                'jam_mulai' => $r['jam_mulai'],
                                'durasi' => (int) $r['durasi_hari'],
                                'dokter' => (string) ($r['dokter'] ?? ''),
                                'catatan' => (string) ($r['catatan'] ?? ''),
                                'foto_resep' => (string) ($r['foto_resep'] ?? ''),
                            ];
                        @endphp

                        <div
                            class="reminder-card{{ $r['aktif'] ? '' : ' is-inactive' }}"
                            data-reminder="{{ $formatData($data) }}"
                        >

                            {{-- Ikon obat --}}
                            <span class="reminder-icon">
                                <svg
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <rect
                                        x="5"
                                        y="3"
                                        width="14"
                                        height="18"
                                        rx="2"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                    />

                                    <path
                                        d="M9 8h6M9 12h6M9 16h3"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            {{-- Informasi obat --}}
                            <div class="reminder-body">

                                <strong>
                                    {{ $r['nama'] }}
                                </strong>

                                <div class="reminder-meta">
                                    {{ $r['dosis'] }}
                                    · {{ (int) $r['frekuensi'] }}x sehari

                                    @if ($r['dokter'])
                                        · Diresepkan {{ $r['dokter'] }}
                                    @endif

                                    · {{ $sisa > 0 ? 'sisa ' . $sisa . ' hari' : 'pengobatan selesai' }}
                                </div>

                                {{-- Catatan --}}
                                @if ($r['catatan'])
                                    <div class="reminder-meta">
                                        Catatan: {{ $r['catatan'] }}
                                    </div>
                                @endif

                                {{-- Jadwal jam minum --}}
                                <div class="reminder-times">
                                    @foreach ($jam as $j)
                                        <span class="reminder-time-chip">
                                            {{ $j }}
                                        </span>
                                    @endforeach
                                </div>

                                {{-- Foto resep --}}
                                @if (!empty($r['foto_resep']))
                                    <div
                                        class="reminder-photo"
                                        style="margin-top:10px;"
                                    >
                                        <a
                                            href="{{ asset('storage/' . $r['foto_resep']) }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <img
                                                src="{{ asset('storage/' . $r['foto_resep']) }}"
                                                alt="Foto resep {{ $r['nama'] }}"
                                                style="max-width:120px; max-height:100px; object-fit:cover; border-radius:8px;"
                                            >
                                        </a>
                                    </div>
                                @endif

                            </div>

                            {{-- Tombol aksi --}}
                            <div class="reminder-actions">

                                {{-- Aktif atau jeda --}}
                                <form
                                    method="POST"
                                    action="{{ url('/pengingat-obat') }}"
                                >
                                    @csrf

                                    <input
                                        type="hidden"
                                        name="aksi"
                                        value="toggle"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="{{ (int) $r['id'] }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="aktif"
                                        value="{{ $r['aktif'] ? '0' : '1' }}"
                                    >

                                    <label
                                        class="toggle-switch"
                                        aria-label="Aktifkan atau jedakan pengingat"
                                    >
                                        <input
                                            type="checkbox"
                                            {{ $r['aktif'] ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                        >

                                        <span class="track"></span>
                                    </label>

                                </form>

                                {{-- Ubah pengingat --}}
                                <button
                                    type="button"
                                    class="icon-btn"
                                    data-reminder-edit
                                    aria-label="Ubah pengingat"
                                >
                                    <svg
                                        width="15"
                                        height="15"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                    >
                                        <path
                                            d="M12 20h9"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                        />

                                        <path
                                            d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </button>

                                {{-- Hapus pengingat --}}
                                <form
                                    method="POST"
                                    action="{{ url('/pengingat-obat') }}"
                                    onsubmit="return confirm('Hapus pengingat ini? Tindakan ini tidak bisa dibatalkan.');"
                                >
                                    @csrf

                                    <input
                                        type="hidden"
                                        name="aksi"
                                        value="hapus"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="{{ (int) $r['id'] }}"
                                    >

                                    <button
                                        type="submit"
                                        class="icon-btn"
                                        aria-label="Hapus pengingat"
                                    >
                                        <svg
                                            width="15"
                                            height="15"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                        >
                                            <path
                                                d="M4 7h16M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m-8 0 1 13a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2l1-13"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </button>

                                </form>

                            </div>

                        </div>

                    @endforeach


                    {{-- Tampilan ketika belum ada pengingat --}}
                    @if (count($daftar) === 0)

                        <div
                            class="empty-state"
                            id="reminder-empty"
                            style="width:100%; max-width:none; box-sizing:border-box;"
                        >

                            <span class="empty-icon">
                                <svg
                                    width="26"
                                    height="26"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                >
                                    <circle
                                        cx="12"
                                        cy="13"
                                        r="8"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />

                                    <path
                                        d="M12 9v4l3 2"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <h3>
                                Belum ada pengingat
                            </h3>

                            <p>
                                Isi form resep di atas untuk membuat jadwal
                                pengingat minum obat pertamamu.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </main>

    </div>

    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>

</body>
</html>
