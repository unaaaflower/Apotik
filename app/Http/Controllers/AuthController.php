<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function login()
    {
        return view('auth.login', [
            'error' => '',
            'identitas' => '',
            'flash' => session('auth'),
        ]);
    }

    /**
     * Memproses login menggunakan email atau username.
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identitas' => 'required|string|max:100',
            'password' => 'required|string|max:200',
        ], [
            'identitas.required' => 'Email atau username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('identitas'));
        }

        $identitas = strtolower(trim($request->identitas));

        $user = User::cariUntukLogin($identitas)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors([
                    'login' => 'Email/username atau kata sandi salah.',
                ])
                ->withInput($request->only('identitas'));
        }

        Auth::login($user);

        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Menampilkan halaman registrasi.
     */
    public function register()
    {
        return view('auth.register', [
            'old' => [
                'nama' => '',
                'username' => '',
                'email' => '',
                'no_hp' => '',
            ],
            'error' => '',
        ]);
    }

    /**
     * Memproses registrasi pengguna baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:30|alpha_dash',
            'email' => 'required|email|max:100',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|max:200',
            'konfirmasi' => 'required|same:password',
            'setuju' => 'accepted',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'konfirmasi.same' => 'Konfirmasi kata sandi tidak cocok.',
            'setuju.accepted' => 'Setujui syarat dan ketentuan untuk melanjutkan.',
        ]);

        $old = $request->only([
            'nama',
            'username',
            'email',
            'no_hp',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($old);
        }

        if ((new User())->usernameTerpakai($request->username)) {
            return back()
                ->withErrors([
                    'username' => 'Username sudah dipakai.',
                ])
                ->withInput($old);
        }

        if ((new User())->emailTerpakai($request->email)) {
            return back()
                ->withErrors([
                    'email' => 'Email sudah terdaftar.',
                ])
                ->withInput($old);
        }

        try {
            (new User())->buat(
                $request->nama,
                $request->username,
                $request->email,
                $request->no_hp ?? '',
                $request->password
            );

            return redirect()
                ->route('login')
                ->with('auth', 'Akun berhasil dibuat. Silakan masuk.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()
                ->withErrors([
                    'register' => 'Username atau email sudah terdaftar.',
                ])
                ->withInput($old);
        }
    }

    /**
     * Mengakhiri sesi login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
