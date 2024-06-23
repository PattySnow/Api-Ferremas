<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_no_branch()
    {
        $response = $this->getJson('/api/inventories/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Sucursal no encontrada']);
    }

    public function test_index_no_products()
    {
        $branch = Branch::factory()->create();

        $response = $this->getJson("/api/inventories/{$branch->id}");

        $response->assertStatus(404)
                 ->assertJson(['message' => 'No hay productos en esta sucursal']);
    }

    public function test_index_with_products()
    {
        $branch = Branch::factory()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create([
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 10
        ]);

        $response = $this->getJson("/api/inventories/{$branch->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['quantity' => 10]);
    }

    public function test_show_item_not_found_in_branch()
    {
        $branch = Branch::factory()->create();
        $item = Item::factory()->create();

        $response = $this->getJson("/api/inventories/{$branch->id}/{$item->id}");

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Producto no encontrado en esta sucursal']);
    }

    public function test_update_item_quantity_success()
    {
        $branch = Branch::factory()->create();
        $item = Item::factory()->create();
        $inventory = Inventory::factory()->create([
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 10
        ]);

        $response = $this->putJson("/api/inventories/{$branch->id}/{$item->id}", [
            'quantity' => 20
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => "Cantidad del producto '{$item->name}' en la sucursal '{$branch->name}' actualizada con éxito",
                     'data' => [
                         'quantity' => 20
                     ]
                 ]);

        $this->assertDatabaseHas('inventories', [
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 20
        ]);
    }

    public function test_reset_stock_success()
    {
        $branch = Branch::factory()->create();
        $item = Item::factory()->create();
        $inventory = Inventory::factory()->create([
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 10
        ]);

        $response = $this->patchJson("/api/inventories/{$branch->id}/{$item->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => "Cantidad del producto '{$item->name}' en la sucursal '{$branch->name}' establecida en 0",
                     'data' => [
                         'quantity' => 0
                     ]
                 ]);

        $this->assertDatabaseHas('inventories', [
            'branch_id' => $branch->id,
            'item_id' => $item->id,
            'quantity' => 0
        ]);
    }
}
