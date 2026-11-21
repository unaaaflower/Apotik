@php
    $aktif = 'profil';

    $tampilPesan = function (string $bagian) use ($pesan) {
        $p = $pesan[$bagian] ?? null;

        if ($p) {
            echo '<div class="form-message' .
                ($p['ok'] ? '' : ' is-error') .
                '" role="status" style="text-align:left;">' .
                e($p['pesan']) .
                '</div>';
        }
    };

    $inisial = collect(explode(' ', trim($user->nama)))
        ->filter()
        ->take(2)
        ->map(fn ($kata) => mb_substr($kata, 0, 1))
        ->implode('');

    $tanggalBergabung = $user->created_at
        ? \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y')
        : '-';

    $labelAlamat = [
        'utama' => 'Utama',
        'kantor' => 'Kantor',
    ];

    $kelasStatus = [
        'Selesai' => 'success',
        'Dikirim' => 'success',
        'Dibatalkan' => 'danger',
    ];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="{{ url('/') }}">
    <title>Profil Saya — KlikMedis</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="shop-body">

    @include('partials.topbar-user')

    <div class="shell">
        <main class="content">

        

            {{-- TAB PROFIL --}}
            <section id="tab-profil" class="account-tab is-active">
                <div class="card">

                    <div class="profile-header">
                        <div class="avatar">{{ $inisial }}</div>

                        <div>
                            <h2>{{ $user->nama }}</h2>
                            <p>
                                Anggota sejak {{ $tanggalBergabung }}
                                · @{{ $user->username }}
                                · {{ $user->email }}
                            </p>
                        </div>
                    </div>

                    <form class="form-grid" method="POST" action="{{ route('profil') }}" novalidate>
                        @csrf

                        <input type="hidden" name="aksi" value="ubah_profil">

                        <div class="field">
                            <label for="p-name">Nama lengkap</label>
                            <input
                                type="text"
                                id="p-name"
                                name="nama"
                                value="{{ old('nama', $isian['nama']) }}"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="p-username">Username</label>
                            <input
                                type="text"
                                id="p-username"
                                name="username"
                                value="{{ old('username', $isian['username']) }}"
                                maxlength="30"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="p-email">Email</label>
                            <input
                                type="email"
                                id="p-email"
                                name="email"
                                value="{{ old('email', $isian['email']) }}"
                                maxlength="100"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="p-phone">No. HP</label>
                            <input
                                type="tel"
                                id="p-phone"
                                name="no_hp"
                                value="{{ old('no_hp', $isian['no_hp']) }}"
                                maxlength="20"
                            >
                        </div>

                        <div class="field full">
                            <button type="submit" class="btn-primary" style="width:auto;">
                                Simpan Perubahan
                            </button>

                            {!! $tampilPesan('profil') !!}
                        </div>
                    </form>
                </div>
            </section>

            {{-- TAB KEAMANAN --}}
            <section id="tab-keamanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Keamanan</p>
                    <p class="card-subtitle">
                        Ganti kata sandi akunmu secara berkala.
                    </p>

                    <form
                        class="form-grid"
                        method="POST"
                        action="{{ route('profil') }}#tab-keamanan"
                        novalidate
                    >
                        @csrf

                        <input type="hidden" name="aksi" value="ubah_password">

                        <div class="field full">
                            <label for="pw-lama">Kata sandi saat ini</label>
                            <input
                                type="password"
                                id="pw-lama"
                                name="password_lama"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="pw-baru">Kata sandi baru</label>
                            <input
                                type="password"
                                id="pw-baru"
                                name="password_baru"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >
                            <span class="field-hint">Minimal 8 karakter</span>
                        </div>

                        <div class="field">
                            <label for="pw-konfirmasi">Konfirmasi kata sandi baru</label>
                            <input
                                type="password"
                                id="pw-konfirmasi"
                                name="konfirmasi"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >
                        </div>

                        <div class="field full">
                            <button type="submit" class="btn-primary" style="width:auto;">
                                Ubah Kata Sandi
                            </button>

                            {!! $tampilPesan('password') !!}
                        </div>
                    </form>
                </div>
            </section>

            {{-- TAB RIWAYAT PESANAN --}}
            <section id="tab-pesanan" class="account-tab">
                <div class="card">
                    <p class="card-title">Riwayat Pesanan</p>
                    <p class="card-subtitle">
                        Pantau status pesanan obat dan produk kesehatanmu.
                    </p>

                    @php
                        $pesanFlash = $flashPesanan;
                    @endphp

                    @include('partials.flash')

                    @if (empty($pesananBaru))
                        <p class="card-subtitle">
                            Belum ada pesanan. Pesanan dari checkout akan muncul di sini.
                        </p>
                    @endif

                    @foreach ($pesananBaru as $ps)
                        <div class="order-row">
                            <div class="order-thumb"></div>

                            <div class="order-info">
                                <strong>
                                    #{{ $ps['kode'] }}
                                    · {{ (int) $ps['jumlah'] }} produk
                                </strong>

                                <span>
                                    {{ \Carbon\Carbon::parse($ps['created_at'])->translatedFormat('d F Y') }}
                                    · Rp{{ number_format((int) $ps['total'], 0, ',', '.') }}
                                    · COD
                                </span>
                            </div>

                            <a
                                href="{{ url('/pembayaran') }}?kode={{ urlencode($ps['kode']) }}"
                                class="status-pill {{ $kelasStatus[$ps['status']] ?? 'pending' }}"
                            >
                                {{ $ps['status'] }}
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- TAB ALAMAT --}}
            <section id="tab-alamat" class="account-tab">
                <div class="card">
                    <p class="card-title">Alamat Tersimpan</p>
                    <p class="card-subtitle">
                        Digunakan sebagai alamat pengiriman saat checkout.
                    </p>

                    @foreach ($labelAlamat as $kunci => $namaLabel)
                        @php
                            $al = $alamatList[$kunci] ?? [
                                'nama' => '',
                                'telp' => '',
                                'alamat' => '',
                            ];
                        @endphp

                        <div class="address-item" data-addr-card>
                            <div style="flex:1; min-width:0;">
                                <span class="tag">{{ $namaLabel }}</span>

                                <p data-addr-view>
                                    <strong>{{ $al['nama'] }}</strong>
                                    · {{ $al['telp'] }}
                                    <br>
                                    {{ $al['alamat'] }}
                                </p>

                                @php
                                    $aksiUrl = '/profil';
                                @endphp

                                @include('partials.alamat-form')
                            </div>

                            <a href="#" class="btn-outline" data-addr-edit>
                                Ubah
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- TAB HAPUS AKUN --}}
            <section id="tab-hapus" class="account-tab">
                <div class="card">
                    <p class="card-title">Hapus Akun</p>
                    <p class="card-subtitle">
                        Akun dan data profilmu akan dihapus permanen dan tidak bisa dikembalikan.
                    </p>

                    <form
                        class="form-grid"
                        method="POST"
                        action="{{ route('profil') }}#tab-hapus"
                        novalidate
                        onsubmit="return confirm('Yakin hapus akun? Tindakan ini tidak bisa dibatalkan.');"
                    >
                        @csrf

                        <input type="hidden" name="aksi" value="hapus_akun">

                        <div class="field full">
                            <label for="hapus-password">
                                Masukkan kata sandi untuk konfirmasi
                            </label>

                            <input
                                type="password"
                                id="hapus-password"
                                name="password"
                                autocomplete="current-password"
                                required
                            >
                        </div>

                        <div class="field full">
                            <button type="submit" class="btn-danger">
                                Hapus Akun Saya
                            </button>

                            {!! $tampilPesan('hapus') !!}
                        </div>
                    </form>
                </div>
            </section>

        </main>
    </div>



    @include('partials.footer')

    <script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
