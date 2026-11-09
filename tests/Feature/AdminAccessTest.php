<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
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

    /** @test */
    public function admin_can_access_admin_dashboard(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    /** @test */
    public function regular_user_cannot_access_admin_dashboard(): void
    {
        $user = $this->makeUser();
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_visitor_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = $this->makeAdmin();
        $response = $this->post(route('login.post'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function regular_user_redirected_to_user_dashboard_after_login(): void
    {
        $user = $this->makeUser();
        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function deactivated_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'role' => Role::User,
            'is_active' => false,
        ]);
        $response = $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function admin_cannot_delete_another_admin_account(): void
    {
        $admin = $this->makeAdmin();
        $target = $this->makeAdmin();
        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $target));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    /** @test */
    public function admin_can_delete_regular_user_account(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser();
        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $user));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
