<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_view_budgets_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('budgets.index'));

        $response->assertStatus(200);
        $response->assertSee('Anggaran');
    }

    public function test_user_can_set_overall_monthly_budget(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('budgets.store'), [
            'category_id' => '',
            'month' => 11,
            'year' => 2026,
            'amount' => 500000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => null,
            'month' => 11,
            'year' => 2026,
            'amount' => 500000,
        ]);
    }

    public function test_user_can_set_category_budget(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $response = $this->actingAs($user)->post(route('budgets.store'), [
            'category_id' => $category->id,
            'month' => 11,
            'year' => 2026,
            'amount' => 200000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 11,
            'year' => 2026,
            'amount' => 200000,
        ]);
    }

    public function test_budget_calculates_spending_accurately(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'month' => 11,
            'year' => 2026,
            'amount' => 100000,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Belanja Awal',
            'amount' => 40000,
            'date' => '2026-11-05',
        ]);

        $this->assertEquals(40000, $budget->spent_amount);
        $this->assertEquals(60000, $budget->remaining_amount);
        $this->assertEquals(40.0, $budget->percentage);
        $this->assertEquals('success', $budget->status_color);
    }

    public function test_user_can_delete_budget(): void
    {
        $user = User::factory()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'category_id' => null,
            'month' => 11,
            'year' => 2026,
            'amount' => 300000,
        ]);

        $response = $this->actingAs($user)->delete(route('budgets.destroy', $budget));

        $response->assertRedirect();
        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
    }
}
