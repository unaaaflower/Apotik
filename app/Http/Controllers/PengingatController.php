<?php

namespace App\Http\Controllers;

use App\Models\PengingatObat;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PengingatController extends Controller
{
    private const MAKS_DURASI = 90;

    private PengingatObat $pengingat;

    public function __construct()
    {
        $this->pengingat = new PengingatObat();
    }

    public function index(Request $request)
    {
        $uid = (int) Auth::id();

        $galat = '';
        $isian = null;

        try {
            if ($request->isMethod('post')) {
                $hasil = $this->prosesForm($request, $uid);

                if ($hasil instanceof RedirectResponse) {
                    return $hasil;
                }

                [$galat, $isian] = $hasil;
            }

            $daftar = $this->pengingat->daftar($uid);

        } catch (Throwable $e) {
            Log::error('Gagal memproses pengingat obat: ' . $e->getMessage());

            abort(500, 'Terjadi kesalahan saat memproses pengingat obat.');
        }

        return view('user.pengingat-obat', [
            'daftar' => $daftar,
            'flash' => session()->pull('pengingat'),
            'galat' => $galat,
            'isian' => $isian,
            'maks' => PengingatObat::MAKS_PER_USER,
        ]);
    }

    private function prosesForm(Request $request, int $uid)
    {
        switch ($request->input('aksi')) {
            case 'tambah':
            case 'ubah':
                return $this->simpan($request, $uid);

            case 'hapus':
                return $this->hapus($request, $uid);

            case 'toggle':
                return $this->toggle($request, $uid);

            default:
                return redirect('/pengingat-obat')->with('pengingat', [
                    'ok' => false,
                    'pesan' => 'Aksi tidak dikenal.',
                ]);
        }
    }

    private function simpan(Request $request, int $uid)
    {
        $aksi = $request->input('aksi');

        $id = $request->input('id', '');

        $isian = [
            'aksi' => $aksi,
            'id' => is_scalar($id) ? (string) $id : '',
            'nama' => $request->input('nama', ''),
            'dosis' => $request->input('dosis', ''),
            'frekuensi' => $request->input('frekuensi', '2'),
            'jam_mulai' => $request->input('jam_mulai', '07:00'),
            'durasi' => $request->input('durasi', '5'),
            'dokter' => $request->input('dokter', ''),
            'catatan' => $request->input('catatan', ''),
        ];

        $dataLama = null;
        $idPengingat = 0;

        if ($aksi === 'ubah') {
            if (
                !ctype_digit($isian['id']) ||
                strlen($isian['id']) > 9
            ) {
                return ['ID pengingat tidak valid.', $isian];
            }

            $idPengingat = (int) $isian['id'];
            $dataLama = $this->pengingat->cariMilik($idPengingat, $uid);

            if (!$dataLama) {
                return ['Pengingat tidak ditemukan.', $isian];
            }

        } elseif ($this->pengingat->hitung($uid) >= PengingatObat::MAKS_PER_USER) {
            return [
                'Jumlah pengingat sudah mencapai batas (' .
                PengingatObat::MAKS_PER_USER . ').',
                $isian,
            ];
        }

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'min:2', 'max:100'],
            'dosis' => ['required', 'string', 'max:50'],
            'frekuensi' => ['required', 'integer', 'in:1,2,3,4'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'durasi' => ['required', 'integer', 'min:1', 'max:' . self::MAKS_DURASI],
            'dokter' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:200'],
            'foto_resep' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'nama.required' => 'Nama obat wajib diisi.',
            'nama.min' => 'Nama obat minimal 2 karakter.',
            'nama.max' => 'Nama obat maksimal 100 karakter.',
            'dosis.required' => 'Dosis sekali minum wajib diisi.',
            'dosis.max' => 'Dosis maksimal 50 karakter.',
            'frekuensi.in' => 'Frekuensi harus 1 sampai 4 kali sehari.',
            'jam_mulai.date_format' => 'Format jam tidak valid.',
            'durasi.max' => 'Durasi maksimal 90 hari.',
            'foto_resep.image' => 'File yang diunggah harus berupa gambar.',
            'foto_resep.mimes' => 'Foto harus berformat JPG, JPEG, atau PNG.',
            'foto_resep.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        if ($validator->fails()) {
            return [
                $validator->errors()->first(),
                $isian,
            ];
        }

        $data = [
            'nama' => trim($request->input('nama')),
            'dosis' => trim($request->input('dosis')),
            'frekuensi' => (int) $request->input('frekuensi'),
            'jam_mulai' => $request->input('jam_mulai') . ':00',
            'durasi_hari' => (int) $request->input('durasi'),
            'dokter' => trim($request->input('dokter', '')) ?: null,
            'catatan' => trim($request->input('catatan', '')) ?: null,
        ];

        $fotoBaru = null;

        try {
            if ($request->hasFile('foto_resep')) {
                $fotoBaru = $request->file('foto_resep')
                    ->store('foto-resep', 'public');

                $data['foto_resep'] = $fotoBaru;
            }

            if ($aksi === 'tambah') {
                $this->pengingat->tambah($uid, $data);

                $pesan = 'Pengingat ' . $data['nama'] . ' berhasil dibuat.';

            } else {
                $this->pengingat->ubah($idPengingat, $uid, $data);

                $pesan = 'Pengingat ' . $data['nama'] . ' berhasil diperbarui.';
            }

            if (
                $aksi === 'ubah' &&
                $fotoBaru &&
                !empty($dataLama['foto_resep'])
            ) {
                Storage::disk('public')->delete($dataLama['foto_resep']);
            }

            return redirect('/pengingat-obat')->with('pengingat', [
                'ok' => true,
                'pesan' => $pesan,
            ]);

        } catch (Throwable $e) {
            if ($fotoBaru) {
                Storage::disk('public')->delete($fotoBaru);
            }

            throw $e;
        }
    }

    private function hapus(Request $request, int $uid): RedirectResponse
    {
        $id = $request->input('id');

        if (
            !is_scalar($id) ||
            !ctype_digit((string) $id) ||
            strlen((string) $id) > 9
        ) {
            return redirect('/pengingat-obat')->with('pengingat', [
                'ok' => false,
                'pesan' => 'ID pengingat tidak valid.',
            ]);
        }

        $id = (int) $id;
        $data = $this->pengingat->cariMilik($id, $uid);

        if (!$data || !$this->pengingat->hapus($id, $uid)) {
            return redirect('/pengingat-obat')->with('pengingat', [
                'ok' => false,
                'pesan' => 'Pengingat tidak ditemukan.',
            ]);
        }

        if (!empty($data['foto_resep'])) {
            Storage::disk('public')->delete($data['foto_resep']);
        }

        return redirect('/pengingat-obat')->with('pengingat', [
            'ok' => true,
            'pesan' => 'Pengingat berhasil dihapus.',
        ]);
    }

    private function toggle(Request $request, int $uid): RedirectResponse
    {
        $id = $request->input('id');

        if (
            !is_scalar($id) ||
            !ctype_digit((string) $id) ||
            strlen((string) $id) > 9
        ) {
            return redirect('/pengingat-obat')->with('pengingat', [
                'ok' => false,
                'pesan' => 'ID pengingat tidak valid.',
            ]);
        }

        $id = (int) $id;

        if (!$this->pengingat->cariMilik($id, $uid)) {
            return redirect('/pengingat-obat')->with('pengingat', [
                'ok' => false,
                'pesan' => 'Pengingat tidak ditemukan.',
            ]);
        }

        $aktif = $request->input('aktif') === '1';

        $this->pengingat->setAktif($id, $uid, $aktif);

        return redirect('/pengingat-obat')->with('pengingat', [
            'ok' => true,
            'pesan' => $aktif ? 'Pengingat diaktifkan.' : 'Pengingat dijeda.',
        ]);
    }
}
