<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('DuitPelajar');
        $response->assertSee('Masuk');
    }

    public function test_user_can_view_register_page(): void
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertSee('DuitPelajar');
        $response->assertSee('Jenjang Pendidikan');
    }

    public function test_user_can_register_successfully(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Budi Pelajar',
            'email' => 'budi@sekolah.sch.id',
            'school_level' => 'SMA/SMK',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Pelajar',
            'email' => 'budi@sekolah.sch.id',
            'school_level' => 'SMA/SMK',
        ]);
    }

    public function test_user_cannot_register_with_short_password(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Budi Pelajar',
            'email' => 'budi@sekolah.sch.id',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'pelajar@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'pelajar@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'pelajar@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'pelajar@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Nama Lama',
            'school_level' => 'SMP',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru Pelajar',
            'school_level' => 'SMA/SMK',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru Pelajar',
            'school_level' => 'SMA/SMK',
        ]);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passwordLama123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'passwordLama123',
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue(Hash::check('passwordBaru123', $user->fresh()->password));
    }
}
