<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Cart;
use App\Models\Branch;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::factory()->create();
        $this->user = User::factory()->create();
        $this->item = Item::factory()->create(['price' => 100]);
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_show_cart_empty()
    {
        $response = $this->getJson('/api/cart');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'El usuario no tiene un carrito.']);
    }

    public function test_add_items_to_cart()
    {
        $response = $this->postJson('/api/cart/add_items', [
            'items' => [
                ['item_id' => $this->item->id, 'quantity' => 2]
            ],
            'delivery_type' => 'Pick Up'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Productos agregados al carrito', 'total' => 200]);

        $this->assertDatabaseHas('cart_items', [
            'item_id' => $this->item->id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200
        ]);
    }

    public function test_remove_item_from_cart()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id, 'branch_id' => $this->branch->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'item_id' => $this->item->id, 'quantity' => 2, 'unit_price' => 100, 'subtotal' => 200]);

        $response = $this->deleteJson("/api/cart/remove_item/{$this->item->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Producto eliminado del carrito', 'total' => 0]);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'item_id' => $this->item->id,
        ]);
    }

    public function test_empty_cart()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id, 'branch_id' => $this->branch->id]);
        CartItem::factory()->create(['cart_id' => $cart->id, 'item_id' => $this->item->id, 'quantity' => 2, 'unit_price' => 100, 'subtotal' => 200]);

        $response = $this->deleteJson('/api/cart/empty_cart');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'El carrito ha sido vaciado correctamente.']);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    public function test_change_branch()
    {
        $cart = Cart::factory()->create(['user_id' => $this->user->id, 'branch_id' => $this->branch->id]);
        $newBranch = Branch::factory()->create();

        $response = $this->patchJson("/api/cart/change_branch/{$newBranch->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => "Sucursal actualizada con éxito. Ha cambiado a la sucursal {$newBranch->name}."]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'branch_id' => $newBranch->id,
        ]);
    }
}
