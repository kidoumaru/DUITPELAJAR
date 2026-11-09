<?php

namespace Tests\Feature;

use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsGoalFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_view_savings_goals(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('savings-goals.index'));

        $response->assertStatus(200);
        $response->assertSee('Target Tabungan');
    }

    public function test_user_can_create_savings_goal(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('savings-goals.store'), [
            'name' => 'Beli Sepatu Olahraga',
            'target_amount' => 350000,
            'current_amount' => 50000,
            'target_date' => '2026-12-31',
            'notes' => 'Sepatu lari',
        ]);

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseHas('savings_goals', [
            'user_id' => $user->id,
            'name' => 'Beli Sepatu Olahraga',
            'target_amount' => 350000,
            'current_amount' => 50000,
            'status' => 'in_progress',
        ]);
    }

    public function test_deposit_without_expense_transaction(): void
    {
        $user = User::factory()->create();
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Buku Ensiklopedia',
            'target_amount' => 100000,
            'current_amount' => 20000,
        ]);

        $response = $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 30000,
            'record_as_expense' => 0,
        ]);

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertEquals(50000, $goal->fresh()->current_amount);
        $this->assertEquals(0, Transaction::where('user_id', $user->id)->count());
    }

    public function test_deposit_with_expense_transaction(): void
    {
        $user = User::factory()->create();
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Laptop Belajar',
            'target_amount' => 5000000,
            'current_amount' => 1000000,
        ]);

        $response = $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 500000,
            'record_as_expense' => 1,
        ]);

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertEquals(1500000, $goal->fresh()->current_amount);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 500000,
        ]);
    }

    public function test_goal_status_updates_to_achieved_when_target_reached(): void
    {
        $user = User::factory()->create();
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Target Cepat',
            'target_amount' => 50000,
            'current_amount' => 40000,
            'status' => 'in_progress',
        ]);

        $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 10000,
        ]);

        $this->assertEquals('achieved', $goal->fresh()->status);
        $this->assertTrue($goal->fresh()->isAchieved());
    }

    public function test_user_can_delete_savings_goal(): void
    {
        $user = User::factory()->create();
        $goal = SavingsGoal::create([
            'user_id' => $user->id,
            'name' => 'Target Batal',
            'target_amount' => 100000,
        ]);

        $response = $this->actingAs($user)->delete(route('savings-goals.destroy', $goal));

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseMissing('savings_goals', ['id' => $goal->id]);
    }
}
