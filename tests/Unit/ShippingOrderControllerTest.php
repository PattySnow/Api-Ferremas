<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ShippingOrder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingOrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'api']);
        \Spatie\Permission\Models\Role::create(['name' => 'employed', 'guard_name' => 'api']);
        \Spatie\Permission\Models\Role::create(['name' => 'client', 'guard_name' => 'api']);
    }

    public function test_index()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin, ['*']);

        ShippingOrder::factory()->count(3)->create();

        $response = $this->getJson('/api/shipping_order');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }



    public function test_show_shipping_order_not_found()
    {
        $user = User::factory()->create();
        $user->assignRole('client');
        Sanctum::actingAs($user, ['*']);
    
        $response = $this->getJson('/api/shipping_order/999');
    
        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Shipping Order no encontrado',
                 ]);
    }
    

    public function test_update_shipping_order_success()
    {
        $employed = User::factory()->create();
        $employed->assignRole('employed');
        Sanctum::actingAs($employed, ['*']);

        $shippingOrder = ShippingOrder::factory()->create();

        $response = $this->patchJson("/api/shipping_order/{$shippingOrder->id}", [
            'status' => 'delivered',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'delivered');
    }

    public function test_update_shipping_order_not_found()
    {
        $employed = User::factory()->create();
        $employed->assignRole('employed');
        Sanctum::actingAs($employed, ['*']);

        $response = $this->patchJson('/api/shipping_order/999', [
            'status' => 'delivered',
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'No query results for model [App\\Models\\ShippingOrder] 999',
                 ]);
    }

    public function test_delete_shipping_order_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin, ['*']);

        $shippingOrder = ShippingOrder::factory()->create();

        $response = $this->deleteJson("/api/shipping_order/{$shippingOrder->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Orden de envío eliminada con éxito']);

        $this->assertDatabaseMissing('shipping_orders', ['id' => $shippingOrder->id]);
    }

    public function test_delete_shipping_order_not_found()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson('/api/shipping_order/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'No query results for model [App\\Models\\ShippingOrder] 999',
                 ]);
    }
}
