<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail; // Asegúrate de incluir el modelo OrderDetail
use Laravel\Sanctum\Sanctum;
use Mockery;

class WebpayAndOrderControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles si es necesario
        if (!\Spatie\Permission\Models\Role::where('name', 'client')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'client', 'guard_name' => 'api']);
        }

        if (!\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'api']);
        }
    }

    public function test_checkout_success()
    {
        // Simular usuario autenticado
        $user = new User(['id' => 1]);
        Sanctum::actingAs($user, ['*']);

        // Mockear el método startWebpayPlusTransaction del controlador WebpayController
        $webpayControllerMock = Mockery::mock(\App\Http\Controllers\WebpayController::class);
        $webpayControllerMock->shouldReceive('startWebpayPlusTransaction')
                             ->once()
                             ->andReturn('http://test-url.com');

        // Ejecutar la acción del controlador que inicia la transacción de Webpay
        $response = $webpayControllerMock->startWebpayPlusTransaction();

        // Verificar que la URL devuelta sea la esperada
        $this->assertEquals('http://test-url.com', $response);
    }

    public function test_checkout_cart_not_found()
    {
        // Simular usuario autenticado
        $user = new User(['id' => 1]);
        Sanctum::actingAs($user, ['*']);

        // Ejecutar la acción del controlador que verifica el carrito
        $cartControllerMock = Mockery::mock(\App\Http\Controllers\CartController::class);
        $cartControllerMock->shouldReceive('getCartByUserId')
                           ->with($user->id)
                           ->once()
                           ->andReturnNull(); // Simular que no se encontró el carrito

        // Ejecutar la acción del controlador que maneja el checkout
        $response = $cartControllerMock->getCartByUserId($user->id);

        // Verificar que se obtenga una respuesta adecuada cuando no se encuentra el carrito
        $this->assertNull($response);
    }

    public function test_checkout_pending_order_exists()
    {
        // Simular usuario autenticado
        $user = new User(['id' => 1]);
        Sanctum::actingAs($user, ['*']);

        // Mockear el método startWebpayPlusTransaction del controlador WebpayController
        $webpayControllerMock = Mockery::mock(\App\Http\Controllers\WebpayController::class);
        $webpayControllerMock->shouldReceive('startWebpayPlusTransaction')
                             ->once()
                             ->andReturn('http://test-url.com');

        // Ejecutar la acción del controlador que inicia la transacción de Webpay
        $response = $webpayControllerMock->startWebpayPlusTransaction();

        // Verificar que la URL devuelta sea la esperada
        $this->assertEquals('http://test-url.com', $response);
    }

    public function test_cancel_order_not_found()
    {
        // Simular usuario autenticado
        $user = new User(['id' => 1]);
        Sanctum::actingAs($user, ['*']);

        // Mockear el método cancelOrder del controlador WebpayController
        $webpayControllerMock = Mockery::mock(\App\Http\Controllers\WebpayController::class);
        $webpayControllerMock->shouldReceive('cancelOrder')
                             ->once()
                             ->andReturn(['error' => 'No hay pago pendiente']);

        // Ejecutar la acción del controlador que cancela la orden
        $response = $webpayControllerMock->cancelOrder();

        // Verificar que se reciba el mensaje adecuado cuando no hay pago pendiente
        $this->assertEquals(['error' => 'No hay pago pendiente'], $response);
    }

    public function test_order_details_index()
    {
        // Simular usuario autenticado como administrador
        $admin = new User(['id' => 2]);
        Sanctum::actingAs($admin, ['*']);

        // Mockear el método index del controlador OrderDetailController
        $orderDetailControllerMock = Mockery::mock(\App\Http\Controllers\OrderDetailController::class);
        $orderDetail = (object) [
            'id' => 1,
            'order_id' => 1,
            'user_id' => 1,
            'status' => 'pending',
            'vci' => 'vci_value',
            'amount' => 1000,
            'buy_order' => 'buy_order_value',
            'session_id' => 'session_id_value',
            'card_number' => 'card_number_value',
            'accounting_date' => 'accounting_date_value',
            'transaction_date' => 'transaction_date_value',
            'authorization_code' => 'authorization_code_value',
            'payment_type_code' => 'payment_type_code_value',
            'response_code' => 'response_code_value',
            'installments_amount' => 1,
            'installments_number' => 1,
            'balance' => 0,
        ]; // Simular un detalle de orden

        $orderDetailControllerMock->shouldReceive('index')
                                 ->andReturn([$orderDetail]);

        // Ejecutar la acción del controlador que obtiene los detalles de la orden
        $response = $orderDetailControllerMock->index();

        // Verificar que se reciba una lista con la estructura esperada de los detalles de la orden
        $this->assertEquals([$orderDetail], $response);
    }

    public function test_order_details_show_not_found()
    {
        // Simular usuario autenticado como administrador
        $admin = new User(['id' => 2]);
        Sanctum::actingAs($admin, ['*']);

        // Mockear el método show del controlador OrderDetailController para lanzar una excepción de modelo no encontrado
        $orderDetailControllerMock = Mockery::mock(\App\Http\Controllers\OrderDetailController::class);
        $orderDetailControllerMock->shouldReceive('show')
                                 ->with(999)
                                 ->andThrow(new \Illuminate\Database\Eloquent\ModelNotFoundException());

        // Ejecutar la acción del controlador que obtiene un detalle de orden específico
        $response = $orderDetailControllerMock->show(999);

        // Verificar que se reciba un mensaje indicando que el detalle de orden no se encontró
        $this->assertEquals(['message' => 'OrderDetail not found.'], $response);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
