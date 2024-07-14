<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Branch;
use App\Models\Inventory;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;

class InventoryControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_index_returns_inventory_list()
    {
        // Crear un mock para el modelo Inventory
        $inventoryMock = Mockery::mock('overload:' . Inventory::class);
        $inventoryMock->shouldReceive('with')->andReturnSelf();
        $inventoryMock->shouldReceive('where')->andReturnSelf();
        $inventoryMock->shouldReceive('get')->andReturn(collect([
            (object)['item_id' => 1, 'quantity' => 10],
            (object)['item_id' => 2, 'quantity' => 20],
        ]));

        // Crear un mock para el modelo Branch
        $branchMock = Mockery::mock('overload:' . Branch::class);
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Sucursal 1'
        ]);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Llamar al método index con un parámetro de sucursal
        $response = $controller->index(1);

        // Verificar que la respuesta sea un JSON con la lista de inventario
        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function test_show_returns_inventory_for_specific_item_and_branch()
    {
        // Mockear el modelo Branch
        $branchMock = \Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Sucursal 1'
        ]);

        // Mockear el modelo Item
        $itemMock = \Mockery::mock('alias:App\Models\Item');
        $itemMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Producto 1'
        ]);

        // Mockear el modelo Inventory
        $inventoryMock = \Mockery::mock('alias:App\Models\Inventory');
        $inventoryMock->shouldReceive('with')->andReturnSelf();
        $inventoryMock->shouldReceive('where')->andReturnSelf();
        $inventoryMock->shouldReceive('first')->andReturn((object)[
            'item_id' => 1,
            'quantity' => 10
        ]);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Llamar al método show con parámetros de sucursal y producto
        $response = $controller->show(1, 1);

        // Verificar que la respuesta sea un JSON con la cantidad del producto en la sucursal específica
        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function test_show_returns_404_when_branch_not_found()
    {
        // Mockear el modelo Branch para que no devuelva ninguna sucursal
        $branchMock = \Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn(null);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Llamar al método show con parámetros de sucursal y producto
        $response = $controller->show(1, 1);

        // Verificar que la respuesta sea un JSON con el mensaje de error
        $this->assertJson($response->getContent());
        $this->assertEquals(404, $response->getStatusCode());

        // Verificar el contenido de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Sucursal no encontrada', $responseData['message']);
    }

    /**
     * @test
     */
    public function test_show_returns_404_when_item_not_found()
    {
        // Mockear el modelo Branch para devolver una sucursal válida
        $branchMock = \Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Sucursal 1'
        ]);

        // Mockear el modelo Item para que no devuelva ningún producto
        $itemMock = \Mockery::mock('alias:App\Models\Item');
        $itemMock->shouldReceive('find')->with(1)->andReturn(null);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Llamar al método show con parámetros de sucursal y producto
        $response = $controller->show(1, 1);

        // Verificar que la respuesta sea un JSON con el mensaje de error
        $this->assertJson($response->getContent());
        $this->assertEquals(404, $response->getStatusCode());

        // Verificar el contenido de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Producto no encontrado', $responseData['message']);
    }



    /**
     * @test
     */
    public function test_update_quantity_for_specific_item_and_branch()
    {
        // Mockear el modelo Branch
        $branchMock = \Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Sucursal 1'
        ]);

        // Mockear el modelo Item
        $itemMock = \Mockery::mock('alias:App\Models\Item');
        $itemMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Producto 1'
        ]);

        // Crear una clase anónima para simular el objeto Inventory con el método save
        $inventoryObject = new class
        {
            public $item_id = 1;
            public $quantity = 10;
            public function save()
            {
                // Simular el guardado del objeto
            }
        };

        // Mockear el modelo Inventory
        $inventoryMock = \Mockery::mock('alias:App\Models\Inventory');
        $inventoryMock->shouldReceive('where')->andReturnSelf();
        $inventoryMock->shouldReceive('first')->andReturn($inventoryObject);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Crear una solicitud falsa
        $request = new Request([
            'quantity' => 15
        ]);

        // Llamar al método update con parámetros de sucursal y producto
        $response = $controller->update($request, 1, 1);

        // Verificar que la respuesta sea un JSON con el mensaje de éxito
        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar el contenido de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("Cantidad del producto 'Producto 1' en la sucursal 'Sucursal 1' actualizada con éxito", $responseData['message']);
        $this->assertEquals(15, $responseData['data']['quantity']);
    }

    /**
     * @test
     */
    public function test_reset_stock_for_specific_item_and_branch()
    {
        // Mockear el modelo Branch
        $branchMock = \Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Sucursal 1'
        ]);

        // Mockear el modelo Item
        $itemMock = \Mockery::mock('alias:App\Models\Item');
        $itemMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Producto 1'
        ]);

        // Crear una clase anónima para simular el objeto Inventory con el método save
        $inventoryObject = new class
        {
            public $item_id = 1;
            public $quantity = 10;
            public function save()
            {
                // Simular el guardado del objeto
            }
        };

        // Mockear el modelo Inventory
        $inventoryMock = \Mockery::mock('alias:App\Models\Inventory');
        $inventoryMock->shouldReceive('where')->andReturnSelf();
        $inventoryMock->shouldReceive('first')->andReturn($inventoryObject);

        // Crear una instancia del controlador con los mocks
        $controller = new InventoryController();

        // Llamar al método resetStock con parámetros de sucursal y producto
        $response = $controller->resetStock(1, 1);

        // Verificar que la respuesta sea un JSON con el mensaje de éxito
        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar el contenido de la respuesta
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals("Cantidad del producto 'Producto 1' en la sucursal 'Sucursal 1' establecida en 0", $responseData['message']);
        $this->assertEquals(0, $responseData['data']['quantity']);
    }
}
