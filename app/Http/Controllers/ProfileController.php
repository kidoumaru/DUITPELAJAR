<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();

        return view('profile.show', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'school_level' => ['nullable', 'string', 'in:SMP,SMA/SMK,Mahasiswa'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'school_level.in' => 'Jenjang sekolah tidak valid.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'school_level' => $validated['school_level'] ?? $user->school_level,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Profil Anda berhasil diperbarui!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Kata sandi Anda berhasil diubah!');
    }

    /**
     * Upload and update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2 MB
                'dimensions:min_width=50,min_height=50,max_width=4096,max_height=4096',
            ],
        ], [
            'photo.required' => 'Foto profil wajib dipilih.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus JPEG, JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran gambar maksimal 2 MB.',
            'photo.dimensions' => 'Dimensi gambar minimal 50×50 piksel dan maksimal 4096×4096 piksel.',
        ]);

        // Delete old photo if exists.
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->update(['photo' => $path]);

        return redirect()->route('profile.show')
            ->with('success', 'Foto profil berhasil diperbarui!');
    }
}
