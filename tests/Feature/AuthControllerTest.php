<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar los seeders
        $this->seed(\Database\Seeders\CreateRolesSeeder::class);
        $this->seed(\Database\Seeders\CreateUsersSeeder::class);
    }

    public function test_register_client()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de client',
            ]);
    }

    public function test_register_employed()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/register_employed', [
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de employed',
            ]);
    }

    public function test_register_admin()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/register_admin', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Usuario registrado exitosamente con el rol de admin',
            ]);
    }

    public function test_login()
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);

        // Guardar el token para pruebas subsecuentes
        $this->token = $response->json('token');
    }

    public function test_logout()
    {
        // Primero, llama a la prueba de login para obtener el token
        $this->test_login();


        // Realiza la solicitud de logout
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
                         ->postJson('/api/logout');

        // Verifica que la respuesta sea exitosa
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Has cerrado sesión']);

  
    }

    public function test_revoke_all_tokens()
    {
        // Primero, llama a la prueba de login para obtener el token
        $this->test_login();

        // Utiliza el token generado en la prueba de login
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/revoke_all_tokens');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Todos los tokens revocados']);

    }
}
