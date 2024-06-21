<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_client()
    {
        $response = $this->postJson('/register', [
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de client',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'client@test.com']);
    }

    public function test_register_worker()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/register_worker', [
            'name' => 'Test Worker',
            'email' => 'worker@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de worker',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'worker@test.com']);
    }

    public function test_register_admin()
    {
        $this->actingAs(User::factory()->create());

        $response = $this->postJson('/register_admin', [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de admin',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'admin@test.com']);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/login', [
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Has cerrado sesión']);
    }

    public function test_revoke_all_tokens()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/revoke_all_tokens');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Todos los tokens revocados']);
    }
}
