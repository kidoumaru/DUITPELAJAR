<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function show(): View
    {
        $admin = request()->user();

        return view('admin.profile.show', compact('admin'));
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $admin->id],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
        ]);

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'current_password.current_password' => 'Kata sandi saat ini salah.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $admin->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $admin = $request->user();

        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ], [
            'photo.required' => 'Foto wajib diunggah.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $path = $request->file('photo')->store('profile-photos', 'public');

        $admin->update(['photo' => $path]);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Foto profil berhasil diperbarui.');
    }
}
