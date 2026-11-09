<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        if ($month < 1 || $month > 12) {
            $month = Carbon::now()->month;
        }

        // Overall budget for this month
        $overallBudget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        // Total expense in this month
        $totalMonthlyExpense = (int) Transaction::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->expense()
            ->sum('amount');

        // Category-specific budgets
        $categoryBudgets = Budget::with('category')
            ->where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Sum of all budgets set (if overall exists, use it; otherwise use sum of category budgets)
        $totalBudgetLimit = $overallBudget
            ? $overallBudget->amount
            : $categoryBudgets->sum('amount');

        $overallSpent = $totalMonthlyExpense;
        $overallRemaining = $totalBudgetLimit > 0 ? max(0, $totalBudgetLimit - $overallSpent) : null;
        $overallPercentage = $totalBudgetLimit > 0 ? min(100, round(($overallSpent / $totalBudgetLimit) * 100, 1)) : 0;

        // Warnings count
        $warningCount = 0;
        $exceededCount = 0;

        if ($totalBudgetLimit > 0) {
            if ($overallSpent >= $totalBudgetLimit) {
                $exceededCount++;
            } elseif ($overallPercentage >= 75) {
                $warningCount++;
            }
        }

        foreach ($categoryBudgets as $cb) {
            if ($cb->percentage >= 100) {
                $exceededCount++;
            } elseif ($cb->percentage >= 75) {
                $warningCount++;
            }
        }

        // Expense categories available for budget creation
        $expenseCategories = Category::active()
            ->expense()
            ->orderBy('name')
            ->get();

        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F Y');

        return view('budgets.index', compact(
            'month',
            'year',
            'monthName',
            'overallBudget',
            'categoryBudgets',
            'totalBudgetLimit',
            'overallSpent',
            'overallRemaining',
            'overallPercentage',
            'warningCount',
            'exceededCount',
            'expenseCategories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'amount' => ['required', 'integer', 'min:1000'],
        ], [
            'amount.required' => 'Nominal anggaran wajib diisi.',
            'amount.integer' => 'Nominal anggaran harus berupa angka.',
            'amount.min' => 'Nominal anggaran minimal Rp 1.000.',
            'month.required' => 'Bulan wajib dipilih.',
            'year.required' => 'Tahun wajib dipilih.',
        ]);

        $categoryId = $validated['category_id'] ? (int) $validated['category_id'] : null;

        // Update or create budget
        Budget::updateOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => $categoryId,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            [
                'amount' => $validated['amount'],
            ]
        );

        return redirect()->route('budgets.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Anggaran berhasil disimpan!');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $user = Auth::user();

        if ($budget->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus anggaran ini.');
        }

        $month = $budget->month;
        $year = $budget->year;
        $budget->delete();

        return redirect()->route('budgets.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Anggaran berhasil dihapus.');
    }
}
