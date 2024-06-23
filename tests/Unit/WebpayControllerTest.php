<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class WebpayControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_success()
    {
        // Crear el rol 'client'
        Role::create(['name' => 'client', 'guard_name' => 'api']);

        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => 'Pending', 'total' => 1000]);

        // Asigna el rol de 'client' al usuario
        $user->assignRole('client');

        // Mockear la función startWebpayPlusTransaction
        $this->partialMock(\App\Http\Controllers\WebpayController::class, function ($mock) {
            $mock->shouldReceive('startWebpayPlusTransaction')
                 ->andReturn('http://test-url.com');
        });

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/webpay');

        $response->assertStatus(201)
                 ->assertJson(['url' => 'http://test-url.com']);
    }
}
