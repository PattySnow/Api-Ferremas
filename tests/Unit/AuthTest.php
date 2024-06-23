<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear el rol 'client' en la base de datos
        Role::create(['name' => 'client', 'guard_name' => 'api']);
    }

    public function test_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email'],
                     'token'
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_login_failure()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Las credenciales son incorrectas.']);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('token-name')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Has cerrado sesión']);
    }

    public function test_update_user()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'password' => Hash::make('password')
        ]);
        Auth::login($user);

        $response = $this->putJson("/api/users/{$user->id}", [
            'name' => 'New Name',
            'password' => 'newpassword'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => 'New Name'
                 ]);

        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }

    public function test_delete_user()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Usuario eliminado exitosamente']);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
