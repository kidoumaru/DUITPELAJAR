<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $incomeCategories = Category::active()->income()->orderBy('name')->get();
        $expenseCategories = Category::active()->expense()->orderBy('name')->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    /**
     * Store a user-defined custom category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori harus Pemasukan atau Pengeluaran.',
        ]);

        Category::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? 'tag',
            'color' => $validated['color'] ?? '#3b82f6',
            'is_system' => false,
            'is_active' => true,
        ]);

        return redirect()->route('categories.index')
            ->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan.");
    }

    /**
     * Update a user-defined custom category (cannot edit system categories).
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        // Only allow editing own custom categories.
        if ($category->is_system || $category->user_id !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengubah kategori sistem atau milik pengguna lain.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'in:income,expense'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:50'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
        ]);

        $category->update([
            'name' => $validated['name'],
            'type' => $validated['type'] ?? $category->type,
            'color' => $validated['color'] ?? $category->color,
            'icon' => $validated['icon'] ?? $category->icon,
        ]);

        return redirect()->route('categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    /**
     * Delete a user-defined custom category (not usable if has transactions).
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Only allow deleting own custom categories.
        if ($category->is_system || $category->user_id !== Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus kategori sistem atau milik pengguna lain.');
        }

        if ($category->transactions()->exists()) {
            return back()->with('error', "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan pada transaksi.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
