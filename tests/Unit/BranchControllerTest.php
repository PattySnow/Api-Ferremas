<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BranchController;
use App\Services\AddItemsToBranchesService;

class BranchControllerTest extends TestCase
{

    protected function tearDown(): void
    {
        // Cierra y limpia los mocks de Mockery
        Mockery::close();

        parent::tearDown();
    }
    /**
     * @test
     */
    public function returns_all_branches()
    {
        $branches = collect(['branch1', 'branch2']);

        // Mock del modelo Branch
        $branchRepositoryMock = Mockery::mock('overload:App\Models\Branch');
        $branchRepositoryMock->shouldReceive('all')->once()->andReturn($branches);

        // Mock del servicio AddItemsToBranchesService
        $addItemsToBranchesServiceMock = Mockery::mock(AddItemsToBranchesService::class);

        // Crear una instancia del controlador con el mock del servicio
        $branchController = new BranchController($addItemsToBranchesServiceMock);

        // Reemplazar la instancia del modelo Branch en la aplicación con el mock
        $this->app->instance(Branch::class, $branchRepositoryMock);

        // Llamar al método index del controlador
        $response = $branchController->index();

        // Comprobar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(200, $response->getStatusCode());

        // Comprobar que el contenido de la respuesta sea el esperado
        $this->assertCount(2, $response->original);
    }

    /**
     * @test
     */
    public function createBranch_creates_branch_with_valid_data()
    {
        $request = new Request([
            'name' => 'New Branch',
            'address' => '123 Street'
        ]);

        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => []
        ]));

        $branchMock = Mockery::mock('overload:App\Models\Branch');
        $branchMock->shouldReceive('create')->once()->andReturn($branchMock);
        $branchMock->shouldReceive('toArray')->once()->andReturn([
            'id' => 1,
            'name' => 'New Branch',
            'address' => '123 Street'
        ]);

        $addItemsToBranchesServiceMock = Mockery::mock(AddItemsToBranchesService::class);
        $addItemsToBranchesServiceMock->shouldReceive('addItemsToBranches')->once();

        $branchController = new BranchController($addItemsToBranchesServiceMock);

        $response = $branchController->createBranch($request);

        $this->assertEquals(201, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Sucursal creada exitosamente y productos asociados con cantidad de inventario 0', $responseData['mensaje']);
        $this->assertArrayHasKey('sucursal', $responseData);

        $sucursalData = $responseData['sucursal'];

        $this->assertArrayHasKey('id', $sucursalData);
        $this->assertEquals(1, $sucursalData['id']);
        $this->assertArrayHasKey('name', $sucursalData);
        $this->assertEquals('New Branch', $sucursalData['name']);
        $this->assertArrayHasKey('address', $sucursalData);
        $this->assertEquals('123 Street', $sucursalData['address']);
    }

    /**
     * @test
     */

    public function test_show_branch_returns_branch_with_valid_id()
    {
        // Crear un mock parcial para el modelo Branch
        $branchMock = Mockery::mock('overload:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Branch 1',
            'address' => '123 Street'
        ]);

        // Crear una instancia del controlador BranchController
        $branchController = new BranchController(new AddItemsToBranchesService());

        // Llamar al método show del controlador
        $response = $branchController->show(1);

        // Verificar que la respuesta sea 200 OK
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar los datos de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('sucursal', $responseData);
        $this->assertEquals('Branch 1', $responseData['sucursal']['name']);
        $this->assertEquals('123 Street', $responseData['sucursal']['address']);
    }
    /**
     * @test
     */
    public function show_branch_returns_404_with_invalid_id()
    {
        // Mock del modelo Branch
        $branchRepositoryMock = Mockery::mock('overload:App\Models\Branch');
        $branchRepositoryMock->shouldReceive('find')->with(1)->andReturn(null);

        // Crear una instancia del controlador con el mock del servicio
        $branchController = new BranchController(new AddItemsToBranchesService());

        // Reemplazar la instancia del modelo Branch en la aplicación con el mock
        $this->app->instance(Branch::class, $branchRepositoryMock);

        // Llamar al método show del controlador
        $response = $branchController->show(1);

        // Comprobar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(404, $response->getStatusCode());

        // Comprobar que el contenido de la respuesta sea el esperado
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Sucursal no encontrada', $responseData['mensaje']);
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function test_update_branch_with_valid_data()
    {
        $request = new Request([
            'name' => 'Updated Branch',
            'address' => '456 Avenue'
        ]);

        // Crear una clase anónima para simular el objeto Branch con el método update
        $branchObject = new class
        {
            public $id = 1;
            public $name = 'Old Branch';
            public $address = '123 Street';
            public function update($data)
            {
                $this->name = $data['name'];
                $this->address = $data['address'];
                return true;
            }
        };

        $branchMock = Mockery::mock('overload:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn($branchObject);

        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => []
        ]));

        $branchController = new BranchController(new AddItemsToBranchesService());
        $response = $branchController->update($request, 1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Sucursal actualizada exitosamente', $responseData['mensaje']);
        $this->assertArrayHasKey('sucursal', $responseData);

        $sucursalData = $responseData['sucursal'];

        $this->assertArrayHasKey('id', $sucursalData);
        $this->assertEquals(1, $sucursalData['id']);
        $this->assertArrayHasKey('name', $sucursalData);
        $this->assertEquals('Updated Branch', $sucursalData['name']);
        $this->assertArrayHasKey('address', $sucursalData);
        $this->assertEquals('456 Avenue', $sucursalData['address']);
    }


    /**
     * @test
     */
    public function test_destroy_branch_with_valid_id()
    {
        // Crear una clase anónima para simular el objeto Branch con el método delete
        $branchObject = new class
        {
            public $id = 1;
            public $name = 'Branch to Delete';
            public $address = '123 Street';
            public function delete()
            {
                return true;
            }
        };

        $branchMock = Mockery::mock('overload:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn($branchObject);

        $requestMock = Mockery::mock('Illuminate\Http\Request');
        $requestMock->shouldReceive('validate')->andReturn(true);

        $branchController = new BranchController(new AddItemsToBranchesService());
        $response = $branchController->destroy(1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Sucursal eliminada', $responseData['mensaje']);
    }
}
