<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_user_can_view_transaction_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Transaksi');
    }

    public function test_user_can_create_income_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::income()->first();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'income',
            'title' => 'Uang Saku Mingguan',
            'amount' => 150000,
            'category_id' => $category->id,
            'date' => '2026-11-02',
            'payment_method' => 'Tunai',
            'notes' => 'Dari orang tua',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Uang Saku Mingguan',
            'amount' => 150000,
            'type' => 'income',
        ]);
    }

    public function test_user_can_create_expense_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'title' => 'Makan Bakso Kantin',
            'amount' => 15000,
            'category_id' => $category->id,
            'date' => '2026-11-02',
            'payment_method' => 'Tunai',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'title' => 'Makan Bakso Kantin',
            'amount' => 15000,
            'type' => 'expense',
        ]);
    }

    public function test_transaction_validation_requires_positive_amount(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'title' => 'Belanja',
            'amount' => -5000,
            'category_id' => $category->id,
            'date' => '2026-11-02',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    public function test_user_can_update_own_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Judul Lama',
            'amount' => 10000,
            'date' => '2026-11-01',
        ]);

        $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'type' => 'expense',
            'title' => 'Judul Baru',
            'amount' => 12000,
            'category_id' => $category->id,
            'date' => '2026-11-01',
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'title' => 'Judul Baru',
            'amount' => 12000,
        ]);
    }

    public function test_user_cannot_edit_or_update_other_users_transaction(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::expense()->first();

        $tx = Transaction::create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Transaksi Milik User 1',
            'amount' => 20000,
            'date' => '2026-11-01',
        ]);

        $response = $this->actingAs($user2)->get(route('transactions.edit', $tx));
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($user2)->put(route('transactions.update', $tx), [
            'type' => 'expense',
            'title' => 'Mencoba Bajak',
            'amount' => 99999,
            'category_id' => $category->id,
            'date' => '2026-11-01',
        ]);
        $updateResponse->assertStatus(403);
    }

    public function test_user_can_delete_own_transaction(): void
    {
        $user = User::factory()->create();
        $category = Category::expense()->first();

        $tx = Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Akan Dihapus',
            'amount' => 5000,
            'date' => '2026-11-01',
        ]);

        $response = $this->actingAs($user)->delete(route('transactions.destroy', $tx));

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseMissing('transactions', ['id' => $tx->id]);
    }

    public function test_user_cannot_delete_other_users_transaction(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $category = Category::expense()->first();

        $tx = Transaction::create([
            'user_id' => $user1->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Rahasia User 1',
            'amount' => 5000,
            'date' => '2026-11-01',
        ]);

        $response = $this->actingAs($user2)->delete(route('transactions.destroy', $tx));
        $response->assertStatus(403);
        $this->assertDatabaseHas('transactions', ['id' => $tx->id]);
    }
}
