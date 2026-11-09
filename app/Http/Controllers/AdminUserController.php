<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$user->name}\" berhasil diaktifkan.");
    }

    public function deactivate(User $user): RedirectResponse
    {
        // Prevent admin from deactivating any admin account, including themselves.
        if ($user->role === Role::Admin) {
            return back()->with('error', 'Akun admin tidak dapat dinonaktifkan.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$user->name}\" berhasil dinonaktifkan.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role === Role::Admin) {
            return back()->with('error', 'Akun admin tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$name}\" berhasil dihapus.");
    }
}
