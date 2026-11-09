<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Pelajar',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Siti Pelajar');
        $response->assertSee('Total Saldo');
        $response->assertSee('Pemasukan Bulan Ini');
        $response->assertSee('Pengeluaran Bulan Ini');
    }

    public function test_dashboard_displays_accurate_financial_summary(): void
    {
        $user = User::factory()->create();
        $incomeCat = Category::income()->first();
        $expenseCat = Category::expense()->first();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $incomeCat->id,
            'type' => 'income',
            'title' => 'Uang Saku',
            'amount' => 300000,
            'date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCat->id,
            'type' => 'expense',
            'title' => 'Makan Siang',
            'amount' => 50000,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        // Total Saldo = 250.000
        $response->assertSee('250.000');
        // Pemasukan = 300.000
        $response->assertSee('300.000');
        // Pengeluaran = 50.000
        $response->assertSee('50.000');
        // Recent transaction
        $response->assertSee('Makan Siang');
    }

    public function test_dashboard_shows_empty_state_when_user_has_no_transactions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Belum Ada Transaksi Tercatat');
        $response->assertSee('Catat Transaksi Pertama');
    }
}
