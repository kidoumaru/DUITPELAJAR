<?php

namespace App\Http\Controllers;

use App\Models\AdminActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::query();

        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        $categories = $query->withCount('transactions')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Jenis kategori wajib dipilih.',
            'type.in' => 'Jenis kategori harus Pemasukan atau Pengeluaran.',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? 'tag',
            'color' => $validated['color'] ?? '#3b82f6',
            'is_system' => false,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AdminActivityLog::record(
            $request->user()->id,
            'created',
            'category',
            $category->id,
            $category->name,
            "Membuat kategori baru: {$category->name} ({$category->type})"
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:income,expense'],
            'icon' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ]);

        $oldName = $category->name;

        $category->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'icon' => $validated['icon'] ?? $category->icon,
            'color' => $validated['color'] ?? $category->color,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AdminActivityLog::record(
            $request->user()->id,
            'updated',
            'category',
            $category->id,
            $category->name,
            "Memperbarui kategori: {$oldName} → {$category->name}"
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diperbarui.");
    }

    public function activate(Category $category): RedirectResponse
    {
        $category->update(['is_active' => true]);

        AdminActivityLog::record(
            request()->user()->id,
            'activated',
            'category',
            $category->id,
            $category->name,
            "Mengaktifkan kategori: {$category->name}"
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil diaktifkan.");
    }

    public function deactivate(Category $category): RedirectResponse
    {
        $category->update(['is_active' => false]);

        AdminActivityLog::record(
            request()->user()->id,
            'deactivated',
            'category',
            $category->id,
            $category->name,
            "Menonaktifkan kategori: {$category->name}"
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$category->name}\" berhasil dinonaktifkan.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error',
                "Kategori \"{$category->name}\" tidak dapat dihapus karena masih digunakan pada transaksi."
            );
        }

        $name = $category->name;
        $id = $category->id;

        AdminActivityLog::record(
            request()->user()->id,
            'deleted',
            'category',
            $id,
            $name,
            "Menghapus kategori: {$name}"
        );

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori \"{$name}\" berhasil dihapus.");
    }
}
