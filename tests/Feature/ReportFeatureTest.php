<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_view_reports_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan');
    }

    public function test_reports_calculate_accurate_financials(): void
    {
        $user = User::factory()->create();
        $incomeCat = Category::income()->first();
        $expenseCat = Category::expense()->first();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $incomeCat->id,
            'type' => 'income',
            'title' => 'Beasiswa Prestasi',
            'amount' => 500000,
            'date' => now()->toDateString(),
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCat->id,
            'type' => 'expense',
            'title' => 'Buku Cetak',
            'amount' => 120000,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertSee('500.000');
        $response->assertSee('120.000');
        $response->assertSee('380.000'); // Selisih
    }

    public function test_user_can_export_csv(): void
    {
        $user = User::factory()->create();
        $incomeCat = Category::income()->first();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $incomeCat->id,
            'type' => 'income',
            'title' => 'Gaji Magang',
            'amount' => 750000,
            'date' => '2026-11-01',
        ]);

        $response = $this->actingAs($user)->get(route('reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertStreamed();
    }
}
