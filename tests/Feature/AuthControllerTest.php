<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_client()
    {
        $role = Role::create(['name' => 'client', 'guard_name' => 'api']);

        $response = $this->postJson('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    /** @test */
    public function it_registers_a_worker()
    {
        $role = Role::create(['name' => 'worker', 'guard_name' => 'api']);

        $response = $this->postJson('/register_worker', [
            'name' => 'Test Worker',
            'email' => 'worker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    /** @test */
    public function it_registers_an_admin()
    {
        $role = Role::create(['name' => 'admin', 'guard_name' => 'api']);

        $response = $this->postJson('/register_admin', [
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    /** @test */
    public function it_logs_in_a_user()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/login', [
            'email' => 'user@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    /** @test */
    public function it_fails_to_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Las credenciales son incorrectas']);
    }

    /** @test */
    public function it_logs_out_a_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('token-name')->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
             ->postJson('/logout')
             ->assertStatus(200)
             ->assertJson(['message' => 'Has cerrado sesión']);
    }

    /** @test */
    public function it_revokes_all_tokens()
    {
        $user = User::factory()->create();
        $token = $user->createToken('token-name')->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
             ->postJson('/revoke_all_tokens')
             ->assertStatus(200)
             ->assertJson(['message' => 'Todos los tokens revocados']);
    }
}
