<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Services\CheckStockService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ShippingOrderController;

class CartControllerTest extends TestCase
{

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_show_cart()
    {
        $userMock = Mockery::mock('alias:App\Models\User');
        $cartMock = Mockery::mock('overload:App\Models\Cart');

        $cartMock->shouldReceive('load')->with('items')->andReturnSelf();
        $cartMock->id = 1;
        $cartMock->user_id = 1;
        $cartMock->branch_id = 1;
        $cartMock->status = 'Pending';
        $cartMock->items = collect([
            (object)[
                'id' => 1,
                'name' => 'Item 1',
                'pivot' => (object)['quantity' => 2]
            ]
        ]);

        $userMock->shouldReceive('carts->where->first')->andReturn($cartMock);
        $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);

        Auth::shouldReceive('user')->andReturn($userMock);

        $cartController = new CartController(new CheckStockService());
        $response = $cartController->showCart();

        $this->assertEquals(200, $response->status());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('cart', $responseData);
        $this->assertArrayHasKey('items', $responseData);
    }

    /**
 * @test
 */
public function test_show_cart_not_found()
{
    $userMock = Mockery::mock('alias:App\Models\User');
    $cartMock = Mockery::mock('overload:App\Models\Cart');

    // Simular que el método 'first' devuelve null para simular un carrito que no existe
    $userMock->shouldReceive('carts->where->first')->andReturn(null);
    $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);

    Auth::shouldReceive('user')->andReturn($userMock);

    $cartController = new CartController(new CheckStockService());
    $response = $cartController->showCart();

    $this->assertEquals(404, $response->status());
    $responseData = json_decode($response->getContent(), true);
    $this->assertArrayHasKey('message', $responseData);
    $this->assertEquals('El usuario no tiene un carrito.', $responseData['message']);
}

   
    /**
     * @test
     */
    public function test_remove_item_from_cart()
    {
        $userMock = Mockery::mock('alias:App\Models\User');
        $cartMock = Mockery::mock('overload:App\Models\Cart');
        $cartMock->shouldReceive('items')->andReturnSelf();
        $cartMock->shouldReceive('sum')->andReturn(100);
        $cartMock->id = 1;
        $cartMock->user_id = 1;
        $cartMock->branch_id = 1;
        $cartMock->status = 'Pending';
        $cartMock->items = collect([
            (object)[
                'id' => 1,
                'pivot' => (object)['quantity' => 2]
            ]
        ]);

        $userMock->shouldReceive('carts->where->first')->andReturn($cartMock);
        $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($userMock);
        Auth::shouldReceive('id')->andReturn(1);

        // Mock de CartItem
        $cartItemMock = Mockery::mock('overload:App\Models\CartItem');
        $cartItemMock->shouldReceive('where')->andReturn($cartItemMock);
        $cartItemMock->shouldReceive('first')->andReturn($cartItemMock);  // Mockear el método 'first' correctamente
        $cartItemMock->shouldReceive('delete')->andReturn(true);


        // Configurar save() en el mock de Cart
        $cartMock->shouldReceive('save')->andReturnNull();

        $cartController = new CartController(new CheckStockService());
        $response = $cartController->removeItem(1);

        $this->assertEquals(200, $response->status());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Producto eliminado del carrito', $responseData['message']);
    }

 /**
 * @test
 */
public function test_remove_item_from_cart_item_not_found()
{
    $userMock = Mockery::mock('alias:App\Models\User');
    $cartMock = Mockery::mock('overload:App\Models\Cart');

    // Simular un carrito con un ítem que no existe
    $cartMock->shouldReceive('items')->andReturnSelf();
    $cartMock->shouldReceive('sum')->andReturn(100);
    $cartMock->id = 1;
    $cartMock->user_id = 1;
    $cartMock->branch_id = 1;
    $cartMock->status = 'Pending';
    $cartMock->items = collect([]);

    $userMock->shouldReceive('carts->where->first')->andReturn($cartMock);
    $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);
    Auth::shouldReceive('user')->andReturn($userMock);
    Auth::shouldReceive('id')->andReturn(1);

    // Mock de CartItem
    $cartItemMock = Mockery::mock('overload:App\Models\CartItem');
    $cartItemMock->shouldReceive('where')->andReturn($cartItemMock);
    $cartItemMock->shouldReceive('first')->andReturn(null); // Simular que no se encontró ningún cartItem
    // No es necesario mockear 'delete' en este caso

    // Configurar save() en el mock de Cart
    $cartMock->shouldReceive('save')->andReturnNull();

    $cartController = new CartController(new CheckStockService());
    $response = $cartController->removeItem(1);

    $this->assertEquals(404, $response->status());
    $responseData = json_decode($response->getContent(), true);
    $this->assertArrayHasKey('message', $responseData);
    $this->assertEquals('El producto no está en el carrito.', $responseData['message']);
}


    /**
     * @test
     */
    public function test_empty_cart()
    {
        $userMock = Mockery::mock('alias:App\Models\User');
        $cartMock = Mockery::mock('overload:App\Models\Cart');
        $cartMock->shouldReceive('items')->andReturnSelf();
        $cartMock->shouldReceive('detach')->andReturn(null);
        $cartMock->shouldReceive('save')->andReturn(null);
        $cartMock->id = 1;
        $cartMock->user_id = 1;
        $cartMock->branch_id = 1;
        $cartMock->status = 'Pending';

        $userMock->shouldReceive('carts->where->first')->andReturn($cartMock);
        $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);

        Auth::shouldReceive('user')->andReturn($userMock);
        Auth::shouldReceive('id')->andReturn(1);

        $cartController = new CartController(new CheckStockService());
        $response = $cartController->emptyCart();

        $this->assertEquals(200, $response->status());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('El carrito ha sido vaciado correctamente.', $responseData['message']);
    }

    /**
     * @test
     */
    public function test_change_branch()
    {
        $branchMock = Mockery::mock('alias:App\Models\Branch');
        $branchMock->shouldReceive('find')->with(2)->andReturn((object)[
            'id' => 2,
            'name' => 'New Branch'
        ]);

        $userMock = Mockery::mock('alias:App\Models\User');
        $cartMock = Mockery::mock('overload:App\Models\Cart');
        $cartMock->shouldReceive('save')->andReturn(null);
        $cartMock->id = 1;
        $cartMock->user_id = 1;
        $cartMock->branch_id = 1;
        $cartMock->status = 'Pending';

        $userMock->shouldReceive('carts->where->first')->andReturn($cartMock);
        $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);

        Auth::shouldReceive('user')->andReturn($userMock);
        Auth::shouldReceive('id')->andReturn(1);

        $cartController = new CartController(new CheckStockService());
        $response = $cartController->changeBranch(2);

        $this->assertEquals(200, $response->status());
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Sucursal actualizada con éxito. Ha cambiado a la sucursal New Branch.', $responseData['message']);
    }




}
