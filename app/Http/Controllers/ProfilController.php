<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index(Request $request)
    {
        $id = (int) Auth::id();
        $user = Auth::user();

        if (!$user) {
            Auth::logout();

            return redirect()->route('login')
                ->with('auth', 'Akun tidak ditemukan. Silakan masuk kembali.');
        }

        // Proses formulir.
        if ($request->isMethod('post')) {
            $aksi = $request->input('aksi');

            switch ($aksi) {
                case 'ubah_profil':
                    return $this->ubahProfil($request, $id);

                case 'ubah_password':
                    return $this->ubahPassword($request, $id);

                case 'hapus_akun':
                    return $this->hapusAkun($request, $id);

                case 'ubah_alamat':
                    return $this->ubahAlamat($request, $id);
            }
        }

        // Ambil pesan notifikasi.
        $pesan = [
            'profil' => session('profil_profil'),
            'password' => session('profil_password'),
            'hapus' => session('profil_hapus'),
        ];

        // Data profil pengguna.
        $isian = [
            'nama' => $user->nama,
            'username' => $user->username,
            'email' => $user->email,
            'no_hp' => (string) $user->no_hp,
        ];

        // Mengembalikan isian setelah validasi gagal.
        if (session()->has('_old_input')) {
            $isian = array_merge($isian, $request->old());
        }

        // Membuat alamat bawaan jika belum tersedia.
        $alamatBawaan = Alamat::bawaan($user->nama);

        foreach ($alamatBawaan as $label => $data) {
            DB::table('alamat')->insertOrIgnore([
                'user_id' => $id,
                'label' => $label,
                'nama' => $data['nama'],
                'telp' => $data['telp'],
                'alamat' => $data['alamat'],
            ]);
        }

        // Mengambil alamat milik pengguna.
        $alamatList = DB::table('alamat')
            ->where('user_id', $id)
            ->get()
            ->keyBy('label')
            ->map(function ($alamat) {
                return [
                    'nama' => $alamat->nama,
                    'telp' => $alamat->telp,
                    'alamat' => $alamat->alamat,
                ];
            })
            ->toArray();

        // Mengambil riwayat pesanan.
        try {
            $pesananBaru = (new Pesanan())->daftarUser($id);
        } catch (Throwable $e) {
            report($e);
            $pesananBaru = [];
        }

        return view('user.profil', [
            'user' => $user,
            'isian' => $isian,
            'pesan' => $pesan,
            'alamatList' => $alamatList,
            'flashAlamat' => session('flashAlamat'),
            'flashPesanan' => session('pesanan'),
            'pesananBaru' => $pesananBaru,
        ]);
    }

    /**
     * Mengubah informasi profil pengguna.
     */
    private function ubahProfil(Request $request, int $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'nama' => ['required', 'string', 'min:2', 'max:100'],
                'username' => ['required', 'string', 'min:3', 'max:30', 'alpha_dash'],
                'email' => ['required', 'email', 'max:100'],
                'no_hp' => ['nullable', 'string', 'max:20'],
            ],
            [
                'nama.required' => 'Nama wajib diisi.',
                'nama.min' => 'Nama minimal 2 karakter.',
                'username.required' => 'Username wajib diisi.',
                'username.min' => 'Username minimal 3 karakter.',
                'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, garis bawah, dan tanda hubung.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'no_hp.max' => 'Nomor HP maksimal 20 karakter.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with('profil_profil', [
                    'ok' => false,
                    'pesan' => $validator->errors()->first(),
                ]);
        }

        $nama = trim($request->input('nama'));
        $username = strtolower(trim($request->input('username')));
        $email = strtolower(trim($request->input('email')));
        $noHp = trim((string) $request->input('no_hp'));

        // Memeriksa username yang sudah digunakan.
        $usernameDipakai = DB::table('users')
            ->where('username', $username)
            ->where('id', '!=', $id)
            ->exists();

        if ($usernameDipakai) {
            return back()
                ->withInput()
                ->with('profil_profil', [
                    'ok' => false,
                    'pesan' => 'Username sudah dipakai pengguna lain.',
                ]);
        }

        // Memeriksa email yang sudah digunakan.
        $emailDipakai = DB::table('users')
            ->where('email', $email)
            ->where('id', '!=', $id)
            ->exists();

        if ($emailDipakai) {
            return back()
                ->withInput()
                ->with('profil_profil', [
                    'ok' => false,
                    'pesan' => 'Email sudah dipakai pengguna lain.',
                ]);
        }

        // Menyimpan perubahan profil.
        DB::table('users')
            ->where('id', $id)
            ->update([
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'no_hp' => $noHp !== '' ? $noHp : null,
            ]);

        return redirect()->route('profil')
            ->with('profil_profil', [
                'ok' => true,
                'pesan' => 'Profil berhasil diperbarui.',
            ]);
    }

    /**
     * Mengubah kata sandi pengguna.
     */
    private function ubahPassword(Request $request, int $id)
    {
        $lama = (string) $request->input('password_lama');
        $baru = (string) $request->input('password_baru');
        $konfirmasi = (string) $request->input('konfirmasi');

        $hash = DB::table('users')
            ->where('id', $id)
            ->value('password');

        if (!$hash || !Hash::check($lama, $hash)) {
            $error = 'Kata sandi saat ini salah.';
        } elseif (strlen($baru) < 8) {
            $error = 'Kata sandi baru minimal 8 karakter.';
        } elseif ($baru !== $konfirmasi) {
            $error = 'Konfirmasi kata sandi baru tidak cocok.';
        } elseif ($baru === $lama) {
            $error = 'Kata sandi baru harus berbeda dari yang lama.';
        } else {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'password' => Hash::make($baru),
                ]);

            $request->session()->regenerate();

            return redirect()->route('profil')
                ->with('profil_password', [
                    'ok' => true,
                    'pesan' => 'Kata sandi berhasil diubah.',
                ]);
        }

        return back()->with('profil_password', [
            'ok' => false,
            'pesan' => $error,
        ]);
    }

    /**
     * Menghapus akun pengguna.
     */
    private function hapusAkun(Request $request, int $id)
    {
        $password = (string) $request->input('password');

        $hash = DB::table('users')
            ->where('id', $id)
            ->value('password');

        if (!$hash || !Hash::check($password, $hash)) {
            return back()->with('profil_hapus', [
                'ok' => false,
                'pesan' => 'Kata sandi salah. Akun tidak dihapus.',
            ]);
        }

        $user = Auth::user();

        // Mencegah penghapusan satu-satunya akun admin.
        if ($user->role === 'admin') {
            $jumlahAdmin = DB::table('users')
                ->where('role', 'admin')
                ->count();

            if ($jumlahAdmin <= 1) {
                return back()->with('profil_hapus', [
                    'ok' => false,
                    'pesan' => 'Ini satu-satunya akun admin, jadi tidak bisa dihapus.',
                ]);
            }
        }

        DB::table('users')
            ->where('id', $id)
            ->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('auth', 'Akun kamu sudah dihapus.');
    }

    /**
     * Mengubah alamat utama atau kantor.
     */
    private function ubahAlamat(Request $request, int $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'label' => ['required', 'in:utama,kantor'],
                'nama' => ['required', 'string', 'max:100'],
                'telp' => ['required', 'string', 'max:20'],
                'alamat' => ['required', 'string', 'max:500'],
            ],
            [
                'label.required' => 'Jenis alamat tidak ditemukan.',
                'label.in' => 'Jenis alamat tidak valid.',
                'nama.required' => 'Nama penerima wajib diisi.',
                'telp.required' => 'Nomor telepon wajib diisi.',
                'alamat.required' => 'Alamat lengkap wajib diisi.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with('flashAlamat', [
                    'ok' => false,
                    'pesan' => $validator->errors()->first(),
                    'label' => $request->input('label'),
                ]);
        }

        $label = $request->input('label');

        // Memperbarui alamat berdasarkan pemilik dan label.
        DB::table('alamat')->updateOrInsert(
            [
                'user_id' => $id,
                'label' => $label,
            ],
            [
                'nama' => trim($request->input('nama')),
                'telp' => trim($request->input('telp')),
                'alamat' => trim($request->input('alamat')),
            ]
        );

        return redirect('/profil#tab-alamat')
            ->with('flashAlamat', [
                'ok' => true,
                'pesan' => 'Alamat berhasil diperbarui.',
                'label' => $label,
            ]);
    }
}
