<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCentralizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => Role::Admin,
            'is_active' => true,
        ]);
    }

    private function makeUser(): User
    {
        return User::factory()->create([
            'role' => Role::User,
            'is_active' => true,
        ]);
    }

    private function makeActiveCategory(string $type = 'expense'): Category
    {
        return Category::create([
            'name' => 'Test ' . $type,
            'type' => $type,
            'icon' => 'tag',
            'color' => '#3b82f6',
            'is_system' => false,
            'is_active' => true,
        ]);
    }

    private function makeInactiveCategory(string $type = 'expense'): Category
    {
        return Category::create([
            'name' => 'Inactive ' . $type,
            'type' => $type,
            'icon' => 'tag',
            'color' => '#ef4444',
            'is_system' => false,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_create_category(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Kategori Baru',
            'type' => 'expense',
            'icon' => 'tag',
            'color' => '#3b82f6',
        ]);
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Kategori Baru', 'type' => 'expense']);
    }

    /** @test */
    public function regular_user_cannot_create_category(): void
    {
        $user = $this->makeUser();
        $response = $this->actingAs($user)->post(route('admin.categories.store'), [
            'name' => 'Kategori User',
            'type' => 'expense',
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_deactivate_category(): void
    {
        $admin = $this->makeAdmin();
        $category = $this->makeActiveCategory();
        $response = $this->actingAs($admin)->put(route('admin.categories.deactivate', $category));
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => false]);
    }

    /** @test */
    public function admin_can_activate_category(): void
    {
        $admin = $this->makeAdmin();
        $category = $this->makeInactiveCategory();
        $response = $this->actingAs($admin)->put(route('admin.categories.activate', $category));
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_active' => true]);
    }

    /** @test */
    public function cannot_delete_category_with_transactions(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $category = $this->makeActiveCategory();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'title' => 'Test',
            'amount' => 10000,
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function can_delete_unused_category(): void
    {
        $admin = $this->makeAdmin();
        $category = $this->makeActiveCategory();
        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category));
        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function user_transaction_store_rejects_deactivated_category(): void
    {
        $user = $this->makeUser();
        $inactive = $this->makeInactiveCategory('expense');
        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'title' => 'Test Transaksi',
            'amount' => 5000,
            'category_id' => $inactive->id,
            'date' => now()->toDateString(),
        ]);
        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function user_transaction_store_accepts_active_category(): void
    {
        $user = $this->makeUser();
        $active = $this->makeActiveCategory('expense');
        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'expense',
            'title' => 'Test Transaksi',
            'amount' => 5000,
            'category_id' => $active->id,
            'date' => now()->toDateString(),
        ]);
        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('transactions', ['title' => 'Test Transaksi', 'user_id' => $user->id]);
    }
}
