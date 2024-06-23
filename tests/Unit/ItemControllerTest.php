<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Role::create(['name' => 'client', 'guard_name' => 'api']);
    }

    public function test_store_item_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->postJson('/api/items', [
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

    public function test_store_item_validation_error()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'api')->postJson('/api/items', [
            'name' => 'It',
            'description' => 'Short',
            'price' => 40,
            'category_id' => null
        ]);

        $response->assertStatus(400)
                 ->assertJsonStructure([
                     'mensaje',
                     'errors'
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

    public function test_show_item_not_found()
    {
        $response = $this->getJson('/api/items/999');

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'Producto no encontrado']);
    }

    public function test_update_item_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $item = Item::factory()->create();

        $response = $this->actingAs($admin, 'api')->putJson("/api/items/{$item->id}", [
            'name' => 'Updated Item Name',
            'description' => 'This is an updated description for the item.',
            'price' => 150,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('mensaje', 'Producto actualizado exitosamente')
                 ->assertJsonPath('producto.id', $item->id)
                 ->assertJsonPath('producto.name', 'Updated Item Name')
                 ->assertJsonPath('producto.description', 'This is an updated description for the item.')
                 ->assertJsonPath('producto.price', 150);
    }

    public function test_update_item_not_found()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'api')->putJson('/api/items/999', [
            'name' => 'Updated Item Name',
            'description' => 'This is an updated description for the item.',
            'price' => 150,
        ]);

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'Producto no encontrado']);
    }

    public function test_destroy_item_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $item = Item::factory()->create();

        $response = $this->actingAs($admin, 'api')->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(200)
                 ->assertJson(['mensaje' => 'Producto eliminado']);

        $this->assertDatabaseMissing('items', [
            'id' => $item->id
        ]);
    }

    public function test_destroy_item_not_found()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'api')->deleteJson('/api/items/999');

        $response->assertStatus(404)
                 ->assertJson(['mensaje' => 'Producto no encontrado']);
    }
}
