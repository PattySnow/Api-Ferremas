<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

class ItemAndCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'api']);
        }
        if (!Role::where('name', 'client')->exists()) {
            Role::create(['name' => 'client', 'guard_name' => 'api']);
        }
    }

    public function test_store_item_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/items', [
            'name' => 'Sample Item',
            'description' => 'This is a sample item description.',
            'price' => 100,
            'category_id' => $category->id
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Producto creado exitosamente y stock inicial establecido en 0 en todas las sucursales',
                 ]);
    }

 

    public function test_show_item_success()
    {
        $item = Item::factory()->create();

        $response = $this->getJson("/api/items/{$item->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('producto.id', $item->id)
                 ->assertJsonPath('producto.name', $item->name)
                 ->assertJsonPath('producto.description', $item->description)
                 ->assertJsonPath('producto.price', (string) $item->price)
                 ->assertJsonPath('producto.category_id', $item->category_id);
    }


    public function test_destroy_item_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $item = Item::factory()->create();

        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(200)
                 ->assertJson(['mensaje' => 'Producto eliminado']);

        $this->assertDatabaseMissing('items', [
            'id' => $item->id
        ]);
    }

    
    public function test_create_category_success()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'New Category'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Categoria creada exitosamente!',
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category'
        ]);
    }



    public function test_update_category_success()
    {
        $category = Category::factory()->create([
            'name' => 'Old Name'
        ]);

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'name' => 'Updated Name'
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name'
        ]);
    }


    public function test_delete_category_success()
    {
        $category = Category::factory()->create([
            'name' => 'Category to delete'
        ]);

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => "Categoria 'Category to delete' eliminada con éxito."
                 ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }
}
