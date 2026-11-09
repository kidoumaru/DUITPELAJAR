<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $preset = $request->input('preset', 'this_month');
        $now = Carbon::now();

        $startDate = null;
        $endDate = null;

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $preset = 'custom';
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            switch ($preset) {
                case 'this_week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    break;
                case 'last_month':
                    $startDate = $now->copy()->subMonth()->startOfMonth();
                    $endDate = $now->copy()->subMonth()->endOfMonth();
                    break;
                case 'this_year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    break;
                case 'all':
                    $startDate = Carbon::create(2020, 1, 1);
                    $endDate = $now->copy()->endOfDay();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $preset = 'this_month';
                    break;
            }
        }

        $baseQuery = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()]);

        $totalIncome = (int) (clone $baseQuery)->income()->sum('amount');
        $totalExpense = (int) (clone $baseQuery)->expense()->sum('amount');
        $netCashFlow = $totalIncome - $totalExpense;
        $totalTransactionsCount = (clone $baseQuery)->count();

        // Rekap Kategori Pengeluaran
        $categoryBreakdown = Transaction::where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('transactions.type', 'expense')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, categories.color, categories.icon, SUM(transactions.amount) as total, COUNT(transactions.id) as count')
            ->groupBy('categories.name', 'categories.color', 'categories.icon')
            ->orderByDesc('total')
            ->get();

        // Rekap Harian
        $dailyRecap = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('date,
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense,
                COUNT(id) as total_count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Chart Data for Monthly/Period Overview (group by day or month)
        $chartDates = [];
        $chartIncome = [];
        $chartExpense = [];

        // If range <= 31 days, show daily chart, else monthly chart
        $diffDays = $startDate->diffInDays($endDate);
        if ($diffDays <= 31) {
            $period = Carbon::parse($startDate);
            while ($period->lte($endDate)) {
                $dStr = $period->toDateString();
                $chartDates[] = $period->locale('id')->translatedFormat('d M');

                $dayInc = (int) Transaction::where('user_id', $user->id)
                    ->whereDate('date', $dStr)
                    ->income()
                    ->sum('amount');

                $dayExp = (int) Transaction::where('user_id', $user->id)
                    ->whereDate('date', $dStr)
                    ->expense()
                    ->sum('amount');

                $chartIncome[] = $dayInc;
                $chartExpense[] = $dayExp;

                $period->addDay();
            }
        } else {
            // Group by month
            $period = Carbon::parse($startDate)->startOfMonth();
            while ($period->lte($endDate)) {
                $m = $period->month;
                $y = $period->year;
                $chartDates[] = $period->locale('id')->translatedFormat('M Y');

                $mInc = (int) Transaction::where('user_id', $user->id)
                    ->whereMonth('date', $m)
                    ->whereYear('date', $y)
                    ->income()
                    ->sum('amount');

                $mExp = (int) Transaction::where('user_id', $user->id)
                    ->whereMonth('date', $m)
                    ->whereYear('date', $y)
                    ->expense()
                    ->sum('amount');

                $chartIncome[] = $mInc;
                $chartExpense[] = $mExp;

                $period->addMonth();
            }
        }

        $formattedRange = $startDate->locale('id')->translatedFormat('d M Y').' - '.$endDate->locale('id')->translatedFormat('d M Y');

        return view('reports.index', compact(
            'preset',
            'startDate',
            'endDate',
            'formattedRange',
            'totalIncome',
            'totalExpense',
            'netCashFlow',
            'totalTransactionsCount',
            'categoryBreakdown',
            'dailyRecap',
            'chartDates',
            'chartIncome',
            'chartExpense'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::with('category')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        $transactions = $query->get();

        $filename = 'Laporan_Keuangan_DuitPelajar_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Header
            fputcsv($handle, [
                'ID Transaksi',
                'Tanggal',
                'Jenis',
                'Kategori',
                'Nama Transaksi',
                'Nominal (Rp)',
                'Metode Pembayaran',
                'Catatan',
            ]);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->id,
                    $t->date->format('Y-m-d'),
                    $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $t->category ? $t->category->name : '-',
                    $t->title,
                    $t->amount,
                    $t->payment_method ?? '-',
                    $t->notes ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
