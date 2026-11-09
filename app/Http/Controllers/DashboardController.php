<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;

        // Friendly Greeting based on Indonesian time
        $hour = (int) $now->format('H');
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        // Carbon locale Indonesian for date
        $now->locale('id');
        $formattedDate = $now->translatedFormat('l, d F Y');

        // All-time financial calculations
        $totalIncome = (int) Transaction::where('user_id', $user->id)->income()->sum('amount');
        $totalExpense = (int) Transaction::where('user_id', $user->id)->expense()->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        // Current month calculations
        $monthIncome = (int) Transaction::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->income()
            ->sum('amount');

        $monthExpense = (int) Transaction::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->expense()
            ->sum('amount');

        $monthCashFlow = $monthIncome - $monthExpense;

        // Monthly Budget calculations
        $overallBudget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        // If no overall budget, check sum of category budgets for this month
        $categoryBudgetsSum = (int) Budget::where('user_id', $user->id)
            ->whereNotNull('category_id')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->sum('amount');

        $monthlyBudgetAmount = $overallBudget ? $overallBudget->amount : $categoryBudgetsSum;
        $budgetRemaining = $monthlyBudgetAmount > 0 ? max(0, $monthlyBudgetAmount - $monthExpense) : null;
        $budgetPercentage = $monthlyBudgetAmount > 0 ? min(100, round(($monthExpense / $monthlyBudgetAmount) * 100, 1)) : 0;

        // Recent transactions (last 7)
        $recentTransactions = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->take(7)
            ->get();

        // Active savings goals
        $savingsGoals = SavingsGoal::where('user_id', $user->id)
            ->orderBy('status', 'asc')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        // Available categories for Quick Add modal
        $categories = Category::active()
            ->orderBy('name')
            ->get();

        // Chart 1: Cash flow last 6 months
        $monthsChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $m = $monthDate->month;
            $y = $monthDate->year;
            $label = $monthDate->locale('id')->translatedFormat('M Y');

            $inc = (int) Transaction::where('user_id', $user->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->income()
                ->sum('amount');

            $exp = (int) Transaction::where('user_id', $user->id)
                ->whereMonth('date', $m)
                ->whereYear('date', $y)
                ->expense()
                ->sum('amount');

            $monthsChart[] = [
                'label' => $label,
                'income' => $inc,
                'expense' => $exp,
            ];
        }

        // Chart 2: Category breakdown for expense this month
        $categoryBreakdown = Transaction::where('transactions.user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('transactions.type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, categories.color as category_color, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();

        // Chart 3: Daily trend for last 30 days
        $dailyTrend = [];
        for ($d = 29; $d >= 0; $d--) {
            $dayDate = Carbon::today()->subDays($d);
            $dayStr = $dayDate->format('Y-m-d');
            $dayLabel = $dayDate->locale('id')->translatedFormat('d M');

            $dayExp = (int) Transaction::where('user_id', $user->id)
                ->whereDate('date', $dayStr)
                ->expense()
                ->sum('amount');

            $dailyTrend[] = [
                'date' => $dayLabel,
                'expense' => $dayExp,
            ];
        }

        return view('dashboard.index', compact(
            'greeting',
            'formattedDate',
            'totalBalance',
            'monthIncome',
            'monthExpense',
            'monthCashFlow',
            'monthlyBudgetAmount',
            'budgetRemaining',
            'budgetPercentage',
            'recentTransactions',
            'savingsGoals',
            'categories',
            'monthsChart',
            'categoryBreakdown',
            'dailyTrend'
        ));
    }
}
