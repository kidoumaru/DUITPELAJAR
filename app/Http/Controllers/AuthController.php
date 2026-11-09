<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Reject deactivated accounts (Requirement 3.7).
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
                ])->onlyInput('email');
            }

            // Perbarui ID session setelah login berhasil.
            $request->session()->regenerate();

            // Simpan ID session aktif pengguna.
            $user->forceFill([
                'active_session_id' => $request->session()->getId(),
            ])->save();

            // Hapus session lama milik pengguna yang sama.
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->delete();

            // Role-based redirect (Requirements 1.6, 1.7).
            $redirectRoute = $user->isAdmin()
                ? route('admin.dashboard')
                : route('dashboard');

            return redirect()->intended($redirectRoute)
                ->with(
                    'success',
                    'Selamat datang kembali di DuitPelajar, '.$user->name.'!'
                );
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'school_level' => ['nullable', 'string', 'in:SMP,SMA/SMK,Mahasiswa'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'school_level.in' => 'Pilihan jenjang sekolah tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        // Simpan akun baru ke database.
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'school_level' => $validated['school_level'] ?? 'Pelajar',
            'password' => Hash::make($validated['password']),
        ]);

        // Pengguna harus login secara manual setelah registrasi.
        return redirect()->route('login')
            ->with(
                'success',
                'Akun berhasil dibuat! Silakan login menggunakan email dan kata sandi Anda.'
            );
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $currentSessionId = $request->session()->getId();

        // Hapus ID session aktif jika sesuai dengan session saat ini.
        if (
            $user &&
            $user->active_session_id &&
            hash_equals($user->active_session_id, $currentSessionId)
        ) {
            $user->forceFill([
                'active_session_id' => null,
            ])->save();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('info', 'Anda telah berhasil keluar. Sampai jumpa lagi!');
    }
}
