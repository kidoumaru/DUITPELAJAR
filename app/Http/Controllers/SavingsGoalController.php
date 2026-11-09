<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SavingsGoalController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $goals = SavingsGoal::where('user_id', $user->id)
            ->orderBy('status', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $totalTarget = $goals->sum('target_amount');
        $totalCollected = $goals->sum('current_amount');
        $achievedCount = $goals->where('status', 'achieved')->count();
        $inProgressCount = $goals->where('status', 'in_progress')->count();

        return view('savings_goals.index', compact(
            'goals',
            'totalTarget',
            'totalCollected',
            'achievedCount',
            'inProgressCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'integer', 'min:1000'],
            'current_amount' => ['nullable', 'integer', 'min:0'],
            'target_date' => ['nullable', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama target tabungan wajib diisi.',
            'target_amount.required' => 'Nominal target wajib diisi.',
            'target_amount.min' => 'Nominal target minimal Rp 1.000.',
            'target_date.after_or_equal' => 'Tanggal target tidak boleh di masa lampau.',
        ]);

        $current = $validated['current_amount'] ?? 0;
        $status = $current >= $validated['target_amount'] ? 'achieved' : 'in_progress';

        SavingsGoal::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $current,
            'target_date' => $validated['target_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $status,
        ]);

        return redirect()->route('savings-goals.index')
            ->with('success', "Target tabungan \"{$validated['name']}\" berhasil dibuat!");
    }

    public function update(Request $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        $user = Auth::user();

        if ($savingsGoal->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah target ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'integer', 'min:1000'],
            'current_amount' => ['required', 'integer', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required' => 'Nama target tabungan wajib diisi.',
            'target_amount.required' => 'Nominal target wajib diisi.',
            'current_amount.required' => 'Nominal terkumpul wajib diisi.',
        ]);

        $status = $validated['current_amount'] >= $validated['target_amount'] ? 'achieved' : 'in_progress';

        $savingsGoal->update([
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $validated['current_amount'],
            'target_date' => $validated['target_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $status,
        ]);

        return redirect()->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil diperbarui!');
    }

    public function deposit(Request $request, SavingsGoal $savingsGoal): RedirectResponse
    {
        $user = Auth::user();

        if ($savingsGoal->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk target tabungan ini.');
        }

        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
            'record_as_expense' => ['nullable', 'boolean'],
        ], [
            'amount.required' => 'Nominal simpanan wajib diisi.',
            'amount.min' => 'Nominal simpanan minimal Rp 1.000.',
        ]);

        $depositAmount = (int) $validated['amount'];
        $recordExpense = $request->boolean('record_as_expense');

        DB::transaction(function () use ($user, $savingsGoal, $depositAmount, $recordExpense) {
            $newCurrent = $savingsGoal->current_amount + $depositAmount;
            $status = $newCurrent >= $savingsGoal->target_amount ? 'achieved' : 'in_progress';

            $savingsGoal->update([
                'current_amount' => $newCurrent,
                'status' => $status,
            ]);

            if ($recordExpense) {
                // Find or use Tabungan category
                $tabunganCategory = Category::active()
                    ->expense()
                    ->where('name', 'Tabungan')
                    ->first() ?? Category::active()->expense()->first();

                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $tabunganCategory->id,
                    'type' => 'expense',
                    'title' => 'Simpanan: '.$savingsGoal->name,
                    'amount' => $depositAmount,
                    'date' => Carbon::today()->toDateString(),
                    'payment_method' => 'Tunai',
                    'notes' => "Alokasi dana ke target tabungan '{$savingsGoal->name}'",
                ]);
            }
        });

        $msg = 'Berhasil menambahkan Rp '.number_format($depositAmount, 0, ',', '.')." ke \"{$savingsGoal->name}\"!";
        if ($recordExpense) {
            $msg .= ' (Tercatat sebagai transaksi pengeluaran)';
        }

        return redirect()->route('savings-goals.index')->with('success', $msg);
    }

    public function destroy(SavingsGoal $savingsGoal): RedirectResponse
    {
        $user = Auth::user();

        if ($savingsGoal->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus target tabungan ini.');
        }

        $name = $savingsGoal->name;
        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')
            ->with('success', "Target tabungan \"{$name}\" berhasil dihapus.");
    }
}
