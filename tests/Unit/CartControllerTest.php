<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Branch;
use App\Services\CheckStockService;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_items_to_cart()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear algunos ítems
        $item1 = Item::factory()->create(['price' => 100]);
        $item2 = Item::factory()->create(['price' => 200]);

        // Crear un carrito para el usuario
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        // Mock del servicio CheckStockService
        $checkStockService = Mockery::mock(CheckStockService::class);
        $checkStockService->shouldReceive('checkStockService')->andReturn(null);

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new \App\Http\Controllers\CartController($checkStockService);

        // Crear una solicitud falsa con los ítems a añadir
        $request = new Request([
            'items' => [
                ['item_id' => $item1->id, 'quantity' => 2],
                ['item_id' => $item2->id, 'quantity' => 3],
            ],
            'delivery_type' => 'Pick Up',
        ]);

        // Llamar al método addItems del controlador
        $response = $controller->addItems($request, new \App\Http\Controllers\ShippingOrderController());

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('Productos agregados al carrito', $response->getData(true)['message']);

        // Verificar que los ítems fueron agregados al carrito
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'item_id' => $item1->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'item_id' => $item2->id,
            'quantity' => 3,
        ]);
    }

    public function test_add_items_to_cart_without_quantity()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear un ítem
        $item = Item::factory()->create(['price' => 100]);

        // Mock del servicio CheckStockService
        $checkStockService = Mockery::mock(CheckStockService::class);
        $checkStockService->shouldReceive('checkStockService')->andReturn(null);

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new \App\Http\Controllers\CartController($checkStockService);

        // Crear una solicitud falsa con un ítem sin cantidad
        $request = new Request([
            'items' => [
                ['item_id' => $item->id],  // Sin 'quantity'
            ],
            'delivery_type' => 'Pick Up',
        ]);

        // Llamar al método addItems del controlador
        $response = $controller->addItems($request, new \App\Http\Controllers\ShippingOrderController());

        // Verificar que la respuesta sea un error de validación
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('Error en los datos.', $response->getData(true)['message']);
    }

    public function test_add_items_to_cart_without_item_id()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Mock del servicio CheckStockService
        $checkStockService = Mockery::mock(CheckStockService::class);
        $checkStockService->shouldReceive('checkStockService')->andReturn(null);

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new \App\Http\Controllers\CartController($checkStockService);

        // Crear una solicitud falsa con un ítem sin item_id
        $request = new Request([
            'items' => [
                ['quantity' => 2],  // Sin 'item_id'
            ],
            'delivery_type' => 'Pick Up',
        ]);

        // Llamar al método addItems del controlador
        $response = $controller->addItems($request, new \App\Http\Controllers\ShippingOrderController());

        // Verificar que la respuesta sea un error de validación
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('Error en los datos.', $response->getData(true)['message']);
    }

    public function test_add_items_to_cart_with_negative_quantity()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear un ítem
        $item = Item::factory()->create(['price' => 100]);

        // Mock del servicio CheckStockService
        $checkStockService = Mockery::mock(CheckStockService::class);
        $checkStockService->shouldReceive('checkStockService')->andReturn(null);

        // Crear una instancia del controlador con el servicio mockeado
        $controller = new \App\Http\Controllers\CartController($checkStockService);

        // Crear una solicitud falsa con un ítem con cantidad negativa
        $request = new Request([
            'items' => [
                ['item_id' => $item->id, 'quantity' => -1],  // Cantidad negativa
            ],
            'delivery_type' => 'Pick Up',
        ]);

        // Llamar al método addItems del controlador
        $response = $controller->addItems($request, new \App\Http\Controllers\ShippingOrderController());

        // Verificar que la respuesta sea un error de validación
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('Error en los datos.', $response->getData(true)['message']);
    }

    public function test_remove_item_from_cart()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear un ítem
        $item = Item::factory()->create(['price' => 100]);

        // Crear un carrito y agregar un ítem
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        // Crear una instancia del controlador
        $controller = new \App\Http\Controllers\CartController(new CheckStockService());

        // Llamar al método removeItem del controlador
        $response = $controller->removeItem($item->id);

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('Producto eliminado del carrito', $response->getData(true)['message']);

        // Verificar que el ítem fue eliminado del carrito
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_remove_nonexistent_item_from_cart()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear un carrito para el usuario
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        // Crear una instancia del controlador
        $controller = new \App\Http\Controllers\CartController(new CheckStockService());

        // Llamar al método removeItem del controlador con un ítem que no existe en el carrito
        $response = $controller->removeItem(999);

        // Verificar que la respuesta sea un error de ítem no encontrado
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('El producto no está en el carrito.', $response->getData(true)['message']);
    }

    public function test_empty_cart()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear un carrito para el usuario y agregar un ítem
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        $item = Item::factory()->create(['price' => 100]);
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'item_id' => $item->id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);

        // Crear una instancia del controlador
        $controller = new \App\Http\Controllers\CartController(new CheckStockService());

        // Llamar al método emptyCart del controlador
        $response = $controller->emptyCart();

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals('El carrito ha sido vaciado correctamente.', $response->getData(true)['message']);

        // Verificar que el carrito está vacío
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);
    }

    public function test_change_branch()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        Auth::login($user);

        // Crear una sucursal
        $branch = Branch::factory()->create();

        // Crear un carrito para el usuario
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'status' => 'Pending',
        ]);

        // Crear una instancia del controlador
        $controller = new \App\Http\Controllers\CartController(new CheckStockService());

        // Llamar al método changeBranch del controlador
        $response = $controller->changeBranch($branch->id);

        // Verificar que la respuesta sea correcta
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getData(true));
        $this->assertEquals("Sucursal actualizada con éxito. Ha cambiado a la sucursal {$branch->name}.", $response->getData(true)['message']);

        // Verificar que la sucursal del carrito ha cambiado
        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'branch_id' => $branch->id,
        ]);
    }
}
