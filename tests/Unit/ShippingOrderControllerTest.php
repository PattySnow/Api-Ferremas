<?php

namespace Tests\Unit\Controllers;

use Mockery;
use Tests\TestCase;
use App\Http\Controllers\ShippingOrderController;
use App\Models\ShippingOrder;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

class ShippingOrderControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        // Cierra y limpia los mocks de Mockery
        Mockery::close();

        parent::tearDown();
    }
    public function testIndex()
    {
        // Mockear el modelo ShippingOrder
        $shippingOrders = [
            new ShippingOrder(['id' => 1, 'user_id' => 1, 'subtotal' => 100, 'shipping_cost' => 10, 'total' => 110]),
            new ShippingOrder(['id' => 2, 'user_id' => 2, 'subtotal' => 150, 'shipping_cost' => 15, 'total' => 165]),
        ];

        $mockShippingOrder = Mockery::mock(ShippingOrder::class);
        $mockShippingOrder->shouldReceive('all')->andReturn($shippingOrders);

        // Mockear el controlador ShippingOrderController para simular el método index
        $shippingOrderController = new ShippingOrderController();
        $this->app->instance(ShippingOrder::class, $mockShippingOrder);

        $response = $shippingOrderController->index();

        $this->assertEquals(json_encode($shippingOrders), $response->getContent());
    }

    /**
     * @test
     */
    public function create_shipping_order_from_cart()
    {
        // Mock de objetos necesarios
        $cart = Mockery::mock(Cart::class);
        $cart->user_id = 1;
        $cart->id = 123;

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->once()->andReturn([
            'subtotal' => 100.00,
            'shipping_cost' => 10.00,
            'total' => 110.00,
        ]);

        // Mock del modelo ShippingOrder
        $shippingOrderMock = Mockery::mock('overload:' . ShippingOrder::class);
        $shippingOrderMock->shouldReceive('create')->once()->andReturn($shippingOrderMock);

        // Crear una instancia del controlador
        $controller = new ShippingOrderController();

        // Reemplazar la instancia del modelo ShippingOrder en la aplicación con el mock
        $this->app->instance(ShippingOrder::class, $shippingOrderMock);

        // Llamar al método createFromCart del controlador
        $response = $controller->createFromCart($cart, 100.00, 10.00, 110.00);

        // Verificar que el resultado sea el mock de ShippingOrder
        $this->assertEquals($shippingOrderMock, $response);
    }

    /**
     * @test
     */
    public function show_shipping_order_with_valid_id()
    {
        // Mock del modelo ShippingOrder
        $shippingOrderMock = new ShippingOrder(['id' => 1, 'subtotal' => 100.00, 'shipping_cost' => 10.00, 'total' => 110.00]);

        $shippingOrderRepositoryMock = Mockery::mock('overload:' . ShippingOrder::class);
        $shippingOrderRepositoryMock->shouldReceive('findOrFail')->with(1)->andReturn($shippingOrderMock);

        // Crear una instancia del controlador
        $controller = new ShippingOrderController();

        // Reemplazar la instancia del modelo ShippingOrder en la aplicación con el mock
        $this->app->instance(ShippingOrder::class, $shippingOrderRepositoryMock);

        // Llamar al método show del controlador
        $response = $controller->show('1');

        // Verificar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar que el contenido de la respuesta sea el esperado
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(1, $responseData['id']); // Ejemplo específico basado en la estructura de respuesta
    }

    /**
     * @test
     */
    public function update_shipping_order_with_valid_data()
    {
        // Mock del modelo ShippingOrder
        $shippingOrderMock = new ShippingOrder(['id' => 1, 'subtotal' => 100.00, 'shipping_cost' => 10.00, 'total' => 110.00]);

        $shippingOrderRepositoryMock = Mockery::mock('overload:' . ShippingOrder::class);
        $shippingOrderRepositoryMock->shouldReceive('findOrFail')->with(1)->andReturn($shippingOrderMock);
        $shippingOrderRepositoryMock->shouldReceive('update')->once()->andReturn($shippingOrderMock);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('validate')->once()->andReturn([
            'status' => 'shipped',
        ]);

        // Crear una instancia del controlador
        $controller = new ShippingOrderController();

        // Reemplazar la instancia del modelo ShippingOrder en la aplicación con el mock
        $this->app->instance(ShippingOrder::class, $shippingOrderRepositoryMock);

        // Llamar al método update del controlador
        $response = $controller->update($request, '1');

        // Verificar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar que el contenido de la respuesta sea el esperado
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('shipped', $responseData['status']); // Ejemplo específico basado en la estructura de respuesta
    }

    /**
     * @test
     */
    public function destroy_shipping_order_with_valid_id()
    {
        // Mock del modelo ShippingOrder
        $shippingOrderMock = new ShippingOrder(['id' => 1]);

        $shippingOrderRepositoryMock = Mockery::mock('overload:' . ShippingOrder::class);
        $shippingOrderRepositoryMock->shouldReceive('findOrFail')->with(1)->andReturn($shippingOrderMock);
        $shippingOrderRepositoryMock->shouldReceive('delete')->once();

        // Crear una instancia del controlador
        $controller = new ShippingOrderController();

        // Reemplazar la instancia del modelo ShippingOrder en la aplicación con el mock
        $this->app->instance(ShippingOrder::class, $shippingOrderRepositoryMock);

        // Llamar al método destroy del controlador
        $response = $controller->destroy('1');

        // Verificar que la respuesta tenga el estado HTTP correcto
        $this->assertEquals(200, $response->getStatusCode());

        // Verificar que el contenido de la respuesta sea el esperado
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Orden de envío eliminada con éxito', $responseData['message']); // Ejemplo específico basado en la estructura de respuesta
    }
}
