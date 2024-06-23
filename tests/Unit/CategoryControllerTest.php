<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_with_categories()
    {
        Category::factory()->create(['name' => 'Category 1']);
        Category::factory()->create(['name' => 'Category 2']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'created_at', 'updated_at']
                 ])
                 ->assertJsonFragment(['name' => 'Category 1'])
                 ->assertJsonFragment(['name' => 'Category 2']);
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

    public function test_create_category_validation_error()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Cat'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
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

    public function test_update_nonexistent_category()
    {
        $response = $this->putJson("/api/categories/999", [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'Categoria no encontrada'
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
