<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Transaction::with('category')
            ->where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && in_array($request->input('type'), ['income', 'expense'])) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $datePreset = $request->input('date_preset', 'this_month');
        $now = Carbon::now();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $datePreset = 'custom';
            $query->whereBetween('date', [$request->input('start_date'), $request->input('end_date')]);
        } else {
            switch ($datePreset) {
                case 'today':
                    $query->whereDate('date', $now->toDateString());
                    break;
                case 'this_week':
                    $query->whereBetween('date', [$now->copy()->startOfWeek()->toDateString(), $now->copy()->endOfWeek()->toDateString()]);
                    break;
                case 'this_month':
                    $query->whereMonth('date', $now->month)->whereYear('date', $now->year);
                    break;
                case 'this_year':
                    $query->whereYear('date', $now->year);
                    break;
                case 'all':
                    break;
                default:
                    $query->whereMonth('date', $now->month)->whereYear('date', $now->year);
                    $datePreset = 'this_month';
                    break;
            }
        }

        $summaryQuery = clone $query;
        $totalIncome = (int) (clone $summaryQuery)->income()->sum('amount');
        $totalExpense = (int) (clone $summaryQuery)->expense()->sum('amount');
        $netAmount = $totalIncome - $totalExpense;

        $sort = $request->input('sort', 'date_desc');
        switch ($sort) {
            case 'date_asc':
                $query->orderBy('date', 'asc')->orderBy('id', 'asc');
                break;
            case 'amount_desc':
                $query->orderBy('amount', 'desc');
                break;
            case 'amount_asc':
                $query->orderBy('amount', 'asc');
                break;
            case 'date_desc':
            default:
                $query->orderBy('date', 'desc')->orderBy('id', 'desc');
                break;
        }

        $transactions = $query->paginate(15)->withQueryString();

        $categories = Category::active()->orderBy('name')->get();

        return view('transactions.index', compact(
            'transactions',
            'categories',
            'totalIncome',
            'totalExpense',
            'netAmount',
            'datePreset'
        ));
    }

    public function create(Request $request): View
    {
        $defaultType = $request->input('type', 'expense');
        if (! in_array($defaultType, ['income', 'expense'])) {
            $defaultType = 'expense';
        }

        $categories = Category::active()->orderBy('name')->get();

        return view('transactions.create', compact('categories', 'defaultType'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Jenis transaksi harus berupa Pemasukan atau Pengeluaran.',
            'title.required' => 'Nama transaksi wajib diisi.',
            'title.max' => 'Nama transaksi maksimal 255 karakter.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.integer' => 'Nominal harus berupa angka bulat positif.',
            'amount.min' => 'Nominal minimal Rp 1.',
            'category_id.required' => 'Kategori transaksi wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
        ]);

        // Validate that the selected category type matches the transaction type.
        $category = Category::where('id', $validated['category_id'])
            ->where('is_active', true)
            ->first();

        if (! $category || $category->type !== $validated['type']) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang dipilih tidak sesuai dengan jenis transaksi. Kategori ' .
                    ($validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran') .
                    ' harus dipilih untuk transaksi ' .
                    ($validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran') . '.',
            ]);
        }

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'] ?? 'Tunai',
            'notes' => $validated['notes'] ?? null,
        ]);

        $typeName = $validated['type'] === 'income' ? 'Pemasukan' : 'Pengeluaran';

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi {$typeName} \"{$validated['title']}\" berhasil dicatat!");
    }

    public function edit(Transaction $transaction): View
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk transaksi ini.');
        }

        $categories = Category::active()->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah transaksi ini.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer', 'min:1'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'type.required' => 'Jenis transaksi wajib dipilih.',
            'title.required' => 'Nama transaksi wajib diisi.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.integer' => 'Nominal harus berupa angka bulat.',
            'amount.min' => 'Nominal minimal Rp 1.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'date.required' => 'Tanggal transaksi wajib diisi.',
        ]);

        // Validate that the selected category type matches the transaction type.
        $category = Category::where('id', $validated['category_id'])
            ->where('is_active', true)
            ->first();

        if (! $category || $category->type !== $validated['type']) {
            return back()->withInput()->withErrors([
                'category_id' => 'Kategori yang dipilih tidak sesuai dengan jenis transaksi. Kategori ' .
                    ($validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran') .
                    ' harus dipilih untuk transaksi ' .
                    ($validated['type'] === 'income' ? 'pemasukan' : 'pengeluaran') . '.',
            ]);
        }

        $transaction->update([
            'category_id' => $validated['category_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'] ?? 'Tunai',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $user = Auth::user();

        if ($transaction->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus transaksi ini.');
        }

        $title = $transaction->title;
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', "Transaksi \"{$title}\" berhasil dihapus.");
    }
}
