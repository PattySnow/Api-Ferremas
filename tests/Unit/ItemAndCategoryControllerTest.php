<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Services\AddItemWithoutStockService;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;


class ItemAndCategoryControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_store_item_with_valid_data()
    {
        $request = new Request([
            'name' => 'New Item',
            'description' => 'Item description',
            'price' => 100,
            'category_id' => 1
        ]);

        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => []
        ]));

        $itemMock = Mockery::mock('alias:App\Models\Item');
        $addItemServiceMock = Mockery::mock(AddItemWithoutStockService::class);
        $addItemServiceMock->shouldReceive('AddItemWithoutStock')->once()->andReturn($itemMock);

        $itemController = new ItemController($addItemServiceMock);
        $response = $itemController->store($request);

        $this->assertEquals(201, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Producto creado exitosamente y stock inicial establecido en 0 en todas las sucursales', $responseData['mensaje']);
        $this->assertArrayHasKey('producto', $responseData);
    }

    /**
     * @test
     */
    public function test_show_item_with_valid_id()
    {
        // Crear un mock del modelo Item
        $itemMock = Mockery::mock('alias:App\Models\Item');

        // Configurar el mock para que devuelva un objeto específico cuando se llama a find(1)
        $itemMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Item 1',
            'description' => 'Description',
            'price' => 100,
            'category_id' => 1
        ]);

        // Crear una instancia del controlador
        $itemController = new ItemController(new AddItemWithoutStockService());

        // Llamar al método show del controlador con el ID 1
        $response = $itemController->show(1);

        // Verificar que el estado de la respuesta sea 200
        $this->assertEquals(200, $response->status());

        // Decodificar la respuesta JSON
        $responseData = json_decode($response->getContent(), true);

        // Verificar que la clave producto exista en la respuesta
        $this->assertArrayHasKey('producto', $responseData);

        // Verificar que los valores del producto sean correctos
        $this->assertEquals('Item 1', $responseData['producto']['name']);
        $this->assertEquals('Description', $responseData['producto']['description']);
        $this->assertEquals(100, $responseData['producto']['price']);
        $this->assertEquals(1, $responseData['producto']['category_id']);
    }
    /**
     * @test
     */
    public function test_update_item_with_valid_data()
    {
        $request = new Request([
            'name' => 'Updated Item',
            'description' => 'Updated description',
            'price' => 150
        ]);

        // Crear una clase anónima para simular el objeto Item con el método update
        $itemObject = new class
        {
            public $id = 1;
            public $name = 'Old Item';
            public $description = 'Old description';
            public $price = 100;
            public $category_id = 1;
            public function update($data)
            {
                $this->name = $data['name'];
                $this->description = $data['description'];
                $this->price = $data['price'];
                return true;
            }
        };

        $itemMock = Mockery::mock('overload:App\Models\Item');
        $itemMock->shouldReceive('find')->with(1)->andReturn($itemObject);

        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => []
        ]));

        $itemController = new ItemController(new AddItemWithoutStockService());
        $response = $itemController->update($request, 1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('mensaje', $responseData);
        $this->assertEquals('Producto actualizado exitosamente', $responseData['mensaje']);
        $this->assertArrayHasKey('producto', $responseData);

        $productoData = $responseData['producto'];

        $this->assertArrayHasKey('id', $productoData);
        $this->assertEquals(1, $productoData['id']);
        $this->assertArrayHasKey('name', $productoData);
        $this->assertEquals('Updated Item', $productoData['name']);
        $this->assertArrayHasKey('description', $productoData);
        $this->assertEquals('Updated description', $productoData['description']);
        $this->assertArrayHasKey('price', $productoData);
        $this->assertEquals(150, $productoData['price']);
    }


    /**
     * @test
     */
    public function test_store_category_with_valid_data()
    {
        $request = new Request([
            'name' => 'New Category'
        ]);

        // Mockear el modelo Category
        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('create')->once()->andReturn((object)[
            'id' => 1,
            'name' => 'New Category'
        ]);

        // Crear una instancia del controlador
        $categoryController = new CategoryController();

        // Llamar al método store del controlador
        $response = $categoryController->store($request);

        // Verificar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(201, $response->status());

        // Verificar que el contenido de la respuesta sea el esperado
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Categoria creada exitosamente!', $responseData['message']);
        $this->assertArrayHasKey('category', $responseData);
    }


    /**
     * @test
     */
    public function test_show_category_with_valid_id()
    {
        $categoryMock = Mockery::mock('alias:App\Models\Category');
        $categoryMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Category 1'
        ]);

        $categoryController = new CategoryController();
        $response = $categoryController->show(1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Category 1', $responseData['name']);
    }

    /**
     * @test
     */
    public function test_update_category_with_valid_data()
    {
        $request = new Request([
            'name' => 'Updated Category'
        ]);

        // Crear una clase anónima para simular el objeto Category con el método update
        $categoryObject = new class
        {
            public $id = 1;
            public $name = 'Old Category';
            public function update($data)
            {
                $this->name = $data['name'];
                return true;
            }
        };

        $categoryMock = Mockery::mock('overload:App\Models\Category');
        $categoryMock->shouldReceive('find')->with(1)->andReturn($categoryObject);

        // Mock del validador para asegurarse de que los métodos make y validate sean llamados
        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => [],
            'validate' => null
        ]));

        // Reemplazar la instancia de Validator en la aplicación
        $this->app->instance('validator', $validatorMock);

        $categoryController = new CategoryController();
        $response = $categoryController->update($request, 1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Updated Category', $responseData['name']);
    }
}
