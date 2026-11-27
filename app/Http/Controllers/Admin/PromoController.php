<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use DateTime;

class PromoController extends Controller
{
    private const MAKS_PERSEN = 100;
    private const MIN_NOMINAL = 1000;
    private const MAKS_NOMINAL = 1000000;
    private const MAKS_MINIMAL = 100000000;
    private const MAKS_HARI = 366;

    public function index(Request $request)
    {
        $promo = new Promo();

        $galat = '';
        $isian = null;

        if ($request->isMethod('post')) { 
            $aksi = $request->input('aksi');

            if (in_array($aksi, ['tambah', 'ubah'], true)) {
                // simpan() mengembalikan dua nilai: pesan error dan isian form.
                [$galat, $isian] = $this->simpan($request, $promo, $aksi);

                if ($galat === '') {
                    // Setelah berhasil, buka kembali daftar promo dan bawa pesan lewat session.
                    return redirect()
                        ->route('admin.promo')
                        ->with('promo_admin', [
                            'ok' => true,
                            'pesan' => 'Promo berhasil disimpan.',
                        ]);
                }
            } elseif ($aksi === 'hapus') {
                $id = $request->input('id');

                // ID dari form = teks yang isi angka saja.
                if (!is_string($id) || !ctype_digit($id)) {
                    return back()->with('promo_admin', [
                        'ok' => false,
                        'pesan' => 'Promo tidak valid.',
                    ]);
                }

                $data = $promo->cariById((int) $id);

                // Kalau promo tidak ditemukan, hapus() langsung dilewati.
                if (!$data || !$promo->hapus((int) $id)) {
                    return back()->with('promo_admin', [
                        'ok' => false,
                        'pesan' => 'Promo tidak ditemukan atau sudah dihapus.',
                    ]);
                }

                return redirect()
                    ->route('admin.promo')
                    ->with('promo_admin', [
                        'ok' => true,
                        'pesan' => 'Promo ' . $data['kode'] . ' berhasil dihapus.',
                    ]);
            } else {
                $galat = 'Aksi tidak dikenal.';
            }
        }

        // Ambil kata kunci dari URL max 50 karakter.
        $q = mb_substr(trim((string) $request->query('q', '')), 0, 50);
        $status = (string) $request->query('status', '');

        if (!in_array($status, ['Aktif', 'Nonaktif'], true)) {
            $status = '';
        }

        $daftar = $promo->daftar($q, $status);

        // Kirim daftar, filter, pesan, dan isi form ke halaman Blade.
        return view('admin.promo', [
            'daftar' => $daftar,
            'jenis' => Promo::JENIS,
            'target' => Promo::TARGET,
            'q' => $q,
            'status' => $status,
            'flash' => session('promo_admin'),
            'galat' => $galat,
            'isian' => $isian,
        ]);
    }

    private function simpan(
        Request $request,
        Promo $promo,
        string $aksi
    ): array {
        $in = [
            'aksi' => $aksi,
            'id' => (string) $request->input('id', ''),
            'nama' => trim((string) $request->input('nama', '')), //' sehat30 ' diubah menjadi 'SEHAT30'.
            'kode' => strtoupper(trim((string) $request->input('kode', ''))),
            'jenis' => (string) $request->input('jenis', ''),
            'nilai' => (string) $request->input('nilai', ''),
            'minimal' => (string) $request->input('minimal', ''),
            'target' => (string) $request->input('target', ''),
            'mulai' => (string) $request->input('mulai', ''),
            'selesai' => (string) $request->input('selesai', ''),
            'aktif' => $request->boolean('aktif') ? '1' : '0', // kalau ga 1 0
        ];

        $hasil = $this->validasi($in, $promo);

        // Kalau hasil validasi berupa pesan, kembalikan pesan dan isian agar form bisa diisi ulang.
        if (is_string($hasil)) {
            return [$hasil, $in];
        }

        try {
            if ($aksi === 'tambah') {
                $promo->tambah($hasil['data']);
            } else {
                $promo->ubah((int) $in['id'], $hasil['data']);
            }
        } catch (QueryException $e) {
            // Untuk kode error database ini
            if ($e->getCode() === '23000') {
                return ['Kode promo sudah dipakai promo lain.', $in];
            }

            throw $e;
        }

        // Pesan kosong menandakan berhasil
        return ['', null];
    }

    private function tanggal(string $tanggal): ?DateTime
    {
        $hasil = DateTime::createFromFormat('!Y-m-d', $tanggal);

        // Cek ulang tanggal
        return ($hasil && $hasil->format('Y-m-d') === $tanggal)
            ? $hasil
            : null;
    }

    private function validasi(array $in, Promo $promo)
    {
        // Saat mengubah promo, cek ID dan pastikan promo masih ada
        if ($in['aksi'] === 'ubah') {
            if (
                $in['id'] === '' ||
                !ctype_digit($in['id']) ||
                strlen($in['id']) > 9
            ) {
                return 'Promo yang diubah tidak valid.';
            }

            if (!$promo->cariById((int) $in['id'])) {
                return 'Promo tidak ditemukan.';
            }
        }

        if ($in['nama'] === '') {
            return 'Nama promo wajib diisi.';
        }

        if (mb_strlen($in['nama']) < 3 || mb_strlen($in['nama']) > 100) {
            return 'Nama promo harus 3-100 karakter.';
        }

        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $in['nama'])) {
            return 'Nama promo mengandung karakter yang tidak diizinkan.';
        }

        if ($in['kode'] === '') {
            return 'Kode promo wajib diisi.';
        }

        if (!preg_match('/^[A-Z0-9]{3,20}$/', $in['kode'])) {
            return 'Kode promo 3-20 karakter, hanya huruf dan angka.';
        }

        // Saat edit, abaikan ID promo ini agar kode lamanya boleh dipakai lagi
        if ($promo->kodeTerpakai($in['kode'], (int) $in['id'])) {
            return 'Kode promo "' . $in['kode'] . '" sudah dipakai promo lain.';
        }

        if (!isset(Promo::JENIS[$in['jenis']])) {
            return 'Jenis diskon tidak valid.';
        }

        if (!in_array($in['target'], Promo::TARGET, true)) {
            return 'Target pengguna tidak valid.';
        }

        if (
            $in['nilai'] === '' ||
            !ctype_digit($in['nilai']) ||
            strlen($in['nilai']) > 9
        ) {
            return 'Nilai diskon harus berupa angka bulat positif.';
        }

        // Ubah teks menjadi angka setelah dipastikan isinya hanya digit.
        $nilai = (int) $in['nilai'];

        if (
            $in['jenis'] === 'persen' &&
            ($nilai < 1 || $nilai > self::MAKS_PERSEN)
        ) {
            return 'Diskon persen harus antara 1 dan 100.';
        }

        if (
            $in['jenis'] === 'nominal' &&
            ($nilai < self::MIN_NOMINAL || $nilai > self::MAKS_NOMINAL)
        ) {
            return 'Diskon nominal harus antara Rp1.000 dan Rp1.000.000.';
        }

        // Kalau kolom minimal pembelian kosong, pakai 0.
        $minimal = 0;

        if ($in['minimal'] !== '') {
            if (
                !ctype_digit($in['minimal']) ||
                strlen($in['minimal']) > 9
            ) {
                return 'Minimal pembelian harus berupa angka bulat.';
            }

            $minimal = (int) $in['minimal'];

            if ($minimal > self::MAKS_MINIMAL) {
                return 'Minimal pembelian melebihi batas maksimal.';
            }
        }

        // Jika ada minimal pembelian, diskon rupiah tidak boleh melebihi jumlah itu.
        if (
            $in['jenis'] === 'nominal' &&
            $minimal > 0 &&
            $nilai > $minimal
        ) {
            return 'Diskon nominal tidak boleh lebih besar dari minimal pembelian.';
        }

        if ($in['mulai'] === '' || $in['selesai'] === '') {
            return 'Tanggal mulai dan tanggal berakhir wajib diisi.';
        }

        $mulai = $this->tanggal($in['mulai']);
        $selesai = $this->tanggal($in['selesai']);

        if (!$mulai || !$selesai) {
            return 'Format tanggal tidak valid.';
        }

        if ($selesai < $mulai) {
            return 'Tanggal berakhir tidak boleh sebelum tanggal mulai.';
        }

        if ((int) $mulai->diff($selesai)->days > self::MAKS_HARI) {
            return 'Periode promo maksimal 366 hari.';
        }

        // Untuk promo baru, tanggal selesai paling cepat hari ini.
        if (
            $in['aksi'] === 'tambah' &&
            $selesai < new DateTime('today')
        ) {
            return 'Promo baru tidak boleh berakhir di masa lalu.';
        }

        // Semua pemeriksaan lolos. Susun nilai sesuai kolom tabel promo.
        return [
            'data' => [
                'nama' => $in['nama'],
                'kode' => $in['kode'],
                'jenis' => $in['jenis'],
                'nilai' => $nilai,
                'minimal' => $minimal,
                'target' => $in['target'],
                'mulai' => $mulai->format('Y-m-d'),
                'selesai' => $selesai->format('Y-m-d'),
                'aktif' => $in['aktif'] === '1' ? 1 : 0,
            ],
        ];
    }
}