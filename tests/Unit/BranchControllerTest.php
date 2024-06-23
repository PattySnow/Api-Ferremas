<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Services\AddItemsToBranchesService;
use Mockery;

class BranchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock del servicio AddItemsToBranchesService
        $this->addItemsToBranchesService = Mockery::mock(AddItemsToBranchesService::class);
        $this->app->instance(AddItemsToBranchesService::class, $this->addItemsToBranchesService);
    }

    public function test_index_no_branches()
    {
        $response = $this->getJson('/api/branches');

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'No hay sucursales disponibles',
                     'status' => 200
                 ]);
    }

    public function test_index_with_branches()
    {
        Branch::factory()->create();

        $response = $this->getJson('/api/branches');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'address', 'created_at', 'updated_at']
                 ]);
    }

    public function test_create_branch_success()
    {
        $this->addItemsToBranchesService->shouldReceive('addItemsToBranches')->once();

        $response = $this->postJson('/api/branches', [
            'name' => 'New Branch',
            'address' => '123 Main St'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Sucursal creada exitosamente y productos asociados con cantidad de inventario 0',
                     'status' => 201
                 ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'New Branch',
            'address' => '123 Main St'
        ]);
    }

    public function test_create_branch_validation_error()
    {
        $response = $this->postJson('/api/branches', [
            'name' => 'Br',
            'address' => ''
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'mensaje' => 'Error en la validación de los datos',
                     'status' => 400
                 ]);
    }

    public function test_update_branch_success()
    {
        $branch = Branch::factory()->create([
            'name' => 'Old Name',
            'address' => 'Old Address'
        ]);

        $response = $this->putJson("/api/branches/{$branch->id}", [
            'name' => 'Updated Name',
            'address' => 'Updated Address'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Sucursal actualizada exitosamente',
                     'status' => 200
                 ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Updated Name',
            'address' => 'Updated Address'
        ]);
    }

    public function test_delete_branch_success()
    {
        $branch = Branch::factory()->create();

        $response = $this->deleteJson("/api/branches/{$branch->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Sucursal eliminada',
                     'status' => 200
                 ]);

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id
        ]);
    }
}
