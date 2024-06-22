<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Services\AddItemsToBranchesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

class BranchControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $addItemsToBranchesService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock del servicio AddItemsToBranchesService
        $this->addItemsToBranchesService = Mockery::mock(AddItemsToBranchesService::class);
        $this->app->instance(AddItemsToBranchesService::class, $this->addItemsToBranchesService);
    }

    public function test_index_with_no_branches()
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
        Branch::factory()->count(3)->create();

        $response = $this->getJson('/api/branches');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_create_branch_with_valid_data()
    {
        $this->addItemsToBranchesService
             ->shouldReceive('addItemsToBranches')
             ->once();

        $response = $this->postJson('/api/branches', [
            'name' => 'Test Branch',
            'address' => '123 Test Street'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'mensaje' => 'Sucursal creada exitosamente y productos asociados con cantidad de inventario 0',
                     'status' => 201
                 ]);
    }

    public function test_create_branch_with_invalid_data()
    {
        $response = $this->postJson('/api/branches', [
            'name' => 'T',
            'address' => ''
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'mensaje' => 'Error en la validación de los datos',
                     'status' => 400
                 ]);
    }

    public function test_show_branch_with_valid_id()
    {
        $branch = Branch::factory()->create();

        $response = $this->getJson('/api/branches/' . $branch->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'sucursal' => [
                         'id' => $branch->id,
                         'name' => $branch->name,
                         'address' => $branch->address
                     ],
                     'status' => 200
                 ]);
    }

    public function test_show_branch_with_invalid_id()
    {
        $response = $this->getJson('/api/branches/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'mensaje' => 'Sucursal no encontrada',
                     'status' => 404
                 ]);
    }

    public function test_update_branch_with_valid_data()
    {
        $branch = Branch::factory()->create();

        $response = $this->putJson('/api/branches/' . $branch->id, [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Sucursal actualizada exitosamente',
                     'status' => 200
                 ]);
    }

    public function test_update_branch_with_invalid_data()
    {
        $branch = Branch::factory()->create();

        $response = $this->putJson('/api/branches/' . $branch->id, [
            'name' => 'T'
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'mensaje' => 'Error al actualizar la sucursal',
                     'status' => 400
                 ]);
    }

    public function test_update_branch_with_invalid_id()
    {
        $response = $this->putJson('/api/branches/999', [
            'name' => 'Updated Name'
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Sucursal no encontrada',
                     'status' => 404
                 ]);
    }

    public function test_destroy_branch_with_valid_id()
    {
        $branch = Branch::factory()->create();

        $response = $this->deleteJson('/api/branches/' . $branch->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'mensaje' => 'Sucursal eliminada',
                     'status' => 200
                 ]);
    }

    public function test_destroy_branch_with_invalid_id()
    {
        $response = $this->deleteJson('/api/branches/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'mensaje' => 'Sucursal no encontrada',
                     'status' => 404
                 ]);
    }
}
