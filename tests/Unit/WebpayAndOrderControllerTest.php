<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail; // Asegúrate de incluir el modelo OrderDetail
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class WebpayAndOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'client', 'guard_name' => 'api']);
        Role::create(['name' => 'admin', 'guard_name' => 'api']); // Asegurarse de crear el rol admin
    }

    public function test_checkout_success()
    {
        $user = User::factory()->create();
        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => 'Pending', 'total' => 1000]);

        $user->assignRole('client');

        $this->partialMock(\App\Http\Controllers\WebpayController::class, function ($mock) {
            $mock->shouldReceive('startWebpayPlusTransaction')
                 ->andReturn('http://test-url.com');
        });

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/webpay');

        $response->assertStatus(201)
                 ->assertJson(['url' => 'http://test-url.com']);
    }

    public function test_checkout_cart_not_found()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/webpay');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Carrito no encontrado']);
    }

    public function test_checkout_pending_order_exists()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $cart = Cart::factory()->create(['user_id' => $user->id, 'status' => 'Pending']);
        $order = Order::factory()->create(['cart_id' => $cart->id, 'status' => 'Pending']);

        $this->partialMock(\App\Http\Controllers\WebpayController::class, function ($mock) {
            $mock->shouldReceive('startWebpayPlusTransaction')
                 ->andReturn('http://test-url.com');
        });

        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/webpay');

        $response->assertStatus(200)
                 ->assertJson(['url' => 'http://test-url.com']);
    }

   
    public function test_cancel_order_not_found()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        Sanctum::actingAs($user, ['*']);

        $response = $this->putJson('/api/webpay/cancel');

        $response->assertStatus(404)
                 ->assertJson(['error' => 'No hay pago pendiente']);
    }

    // Pruebas para OrderDetailController

    public function test_order_details_index()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('client');

        $order = Order::factory()->create(['user_id' => $user->id]);
        OrderDetail::factory()->create(['order_id' => $order->id]);

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/order_details');

        $response->assertStatus(200)
                 ->assertJsonStructure([[
                     'id',
                     'order_id',
                     'user_id',
                     'status',
                     'vci',
                     'amount',
                     'buy_order',
                     'session_id',
                     'card_number',
                     'accounting_date',
                     'transaction_date',
                     'authorization_code',
                     'payment_type_code',
                     'response_code',
                     'installments_amount',
                     'installments_number',
                     'balance',
                 ]]);
    }

  

    public function test_order_details_show_not_found()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/order_details/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'OrderDetail not found.']);
    }
}
