<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
    }

    public function test_system_categories_are_present(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Uang Saku');
        $response->assertSee('Makan');
    }

    public function test_user_can_create_custom_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Les Bahasa Jepang',
            'type' => 'expense',
            'color' => '#8b5cf6',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Les Bahasa Jepang',
            'type' => 'expense',
            'is_system' => false,
        ]);
    }

    public function test_user_can_update_own_custom_category(): void
    {
        $user = User::factory()->create();
        $customCat = Category::create([
            'user_id' => $user->id,
            'name' => 'Nama Awal',
            'type' => 'expense',
            'color' => '#3b82f6',
            'is_system' => false,
        ]);

        $response = $this->actingAs($user)->put(route('categories.update', $customCat), [
            'name' => 'Nama Diubah',
            'color' => '#10b981',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $customCat->id,
            'name' => 'Nama Diubah',
            'color' => '#10b981',
        ]);
    }

    public function test_user_cannot_update_system_category(): void
    {
        $user = User::factory()->create();
        $sysCat = Category::where('is_system', true)->first();

        $response = $this->actingAs($user)->put(route('categories.update', $sysCat), [
            'name' => 'Bajak Sistem',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_unused_custom_category(): void
    {
        $user = User::factory()->create();
        $customCat = Category::create([
            'user_id' => $user->id,
            'name' => 'Akan Dihapus',
            'type' => 'expense',
            'is_system' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $customCat));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $customCat->id]);
    }

    public function test_user_cannot_delete_category_with_transactions(): void
    {
        $user = User::factory()->create();
        $customCat = Category::create([
            'user_id' => $user->id,
            'name' => 'Kategori Terpakai',
            'type' => 'expense',
            'is_system' => false,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $customCat->id,
            'type' => 'expense',
            'title' => 'Beli barang',
            'amount' => 50000,
            'date' => '2026-11-02',
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $customCat));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $customCat->id]);
    }
}
