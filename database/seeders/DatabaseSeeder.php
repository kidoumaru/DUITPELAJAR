<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        // Create Demo Pelajar Account
        $demoUser = User::firstOrCreate(
            ['email' => 'pelajar@duitpelajar.id'],
            [
                'name' => 'Budi Pratama',
                'school_level' => 'SMA/SMK',
                'password' => Hash::make('password123'),
            ]
        );

        $uangSakuCat = Category::where('name', 'Uang Saku')->first();
        $makanCat = Category::where('name', 'Makan')->where('type', 'expense')->first();
        $transportCat = Category::where('name', 'Transportasi')->where('type', 'expense')->first();
        $sekolahCat = Category::where('name', 'Kebutuhan Sekolah')->first();

        // Sample Transactions for current month
        if (Transaction::where('user_id', $demoUser->id)->count() === 0) {
            Transaction::create([
                'user_id' => $demoUser->id,
                'category_id' => $uangSakuCat->id,
                'type' => 'income',
                'title' => 'Uang Saku Bulanan',
                'amount' => 600000,
                'date' => now()->startOfMonth()->toDateString(),
                'payment_method' => 'Transfer Bank',
                'notes' => 'Transfer uang saku dari orang tua',
            ]);

            Transaction::create([
                'user_id' => $demoUser->id,
                'category_id' => $makanCat->id,
                'type' => 'expense',
                'title' => 'Makan Siang & Es Teh Kantin',
                'amount' => 22000,
                'date' => now()->toDateString(),
                'payment_method' => 'Tunai',
                'notes' => 'Makan siang bareng teman sekelas',
            ]);

            Transaction::create([
                'user_id' => $demoUser->id,
                'category_id' => $transportCat->id,
                'type' => 'expense',
                'title' => 'Ongkos Angkot & Busway',
                'amount' => 10000,
                'date' => now()->toDateString(),
                'payment_method' => 'Tunai',
            ]);

            Transaction::create([
                'user_id' => $demoUser->id,
                'category_id' => $sekolahCat->id,
                'type' => 'expense',
                'title' => 'Buku Tulis & Pulpen Gel',
                'amount' => 18000,
                'date' => now()->subDays(1)->toDateString(),
                'payment_method' => 'Tunai',
            ]);
        }

        // Sample Budget
        Budget::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'category_id' => null,
                'month' => now()->month,
                'year' => now()->year,
            ],
            [
                'amount' => 450000,
            ]
        );

        // Sample Savings Goal
        SavingsGoal::firstOrCreate(
            [
                'user_id' => $demoUser->id,
                'name' => 'Beli Sepatu Olahraga Baru',
            ],
            [
                'target_amount' => 350000,
                'current_amount' => 120000,
                'target_date' => now()->addMonths(2)->toDateString(),
                'notes' => 'Nabung Rp 5.000 setiap hari dari sisa uang saku',
                'status' => 'in_progress',
            ]
        );
    }
}
