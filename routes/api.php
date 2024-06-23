<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\WebpayController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ShippingOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Rutas para autenticación

Route::post('/register', [AuthController::class, 'register']); //Registra a un cliente
Route::post('/register_employed', [AuthController::class, 'registerEmployed'])->middleware('auth:sanctum', 'customRole:admin'); //Registra a un trabajador
Route::post('/register_admin', [AuthController::class, 'registerAdmin'])->middleware('auth:sanctum', 'customRole:admin'); //Registra a un administrador
Route::post('/login', [AuthController::class, 'login']); //Loggea a un usuario
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum'); //Logout a un usuario
Route::post('/revoke_all_tokens', [AuthController::class, 'revokeAllTokens'])->middleware('auth:sanctum'); //Elimina todos los tokens del usuario


//Rutas para usuarios

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users', [UserController::class, 'index']); //obtiene los usuarios registrados
    Route::get('users/{id}', [UserController::class, 'show']); //obtiene a un usuario en especifico
    Route::put('users/{id}', [UserController::class, 'update']); //actualiza los datos del usuario (Nombre y contraseña)
    Route::delete('users/{id}', [UserController::class, 'destroy']); //elimina a un usuario
});


//Rutas para manejo de roles

Route::middleware(['auth:sanctum', 'customRole:admin'])->group(function () {
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']); // Obtiene los roles registrados
        Route::post('/', [RoleController::class, 'store']); //crear rol
        Route::get('/{id}', [RoleController::class, 'show']); // Obtiene un rol en específico
        Route::put('/{id}', [RoleController::class, 'update']); // Actualiza los datos del rol
        Route::delete('/{id}', [RoleController::class, 'destroy']); // Elimina un rol
        Route::post('/{user_id}', [RoleController::class, 'assignRoleToUser']); // Asigna rol a un usuario
    });
});


//Rutas para manejo de permisos

Route::middleware(['auth:sanctum', 'customRole:admin'])->group(function () {
    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index']); //obtiene los permisos registrados
        Route::post('/', [PermissionController::class, 'store']); //crear permiso
        Route::get('/{id}', [PermissionController::class, 'show']); //obtiene un permiso en especifico
        Route::put('/{id}', [PermissionController::class, 'update']); //actualiza los datos del permiso
        Route::delete('/{id}', [PermissionController::class, 'destroy']); //elimina un permiso
        Route::post('/assing_rol/{role_id}', [PermissionController::class, 'assignPermissionToRole']); //Asignar permisos a un rol
        Route::post('/assign_user/{user_id}', [PermissionController::class, 'assignPermissionToUser']); //Asignar permisos a un usuario
        Route::post('/revoke/{roleId}', [PermissionController::class, 'revokePermissionFromRole']); // Revoca permiso de un rol
        Route::post('/revoke/user/{userId}', [PermissionController::class, 'revokePermissionFromUser']); // Revoca permiso de un usuario

    });
});


// Rutas para productos
Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index']); // Obtiene todos los productos
    Route::get('/{id}', [ItemController::class, 'show']); // Obtiene un producto
    Route::post('/', [ItemController::class, 'store'])->middleware(['auth:sanctum', 'customRole:admin']); // Crea un producto
    Route::put('/{id}', [ItemController::class, 'update'])->middleware(['auth:sanctum', 'customRole:admin']); // Actualiza un producto
    Route::delete('/{id}', [ItemController::class, 'destroy'])->middleware(['auth:sanctum', 'customRole:admin']); // Elimina un producto
});



// Rutas para categoría de los productos

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // Obtiene todas las categorias
    Route::get('/{id}', [CategoryController::class, 'show']); // Obtiene una categoria
    Route::post('/', [CategoryController::class, 'store']); // Crea una categoria
    Route::put('/{id}', [CategoryController::class, 'update']); // Actualiza una categoria
    Route::delete('/{id}', [CategoryController::class, 'destroy']); // Elimina una categoria
});


//Rutas para sucursales

Route::prefix('branches')->group(function () {
    Route::get('/', [BranchController::class, 'index']); // Obtiene las sucursales
    Route::get('/{id}', [BranchController::class, 'show']); // Obtiene una sucursal
    Route::post('/', [BranchController::class, 'createBranch']); // Crea una sucursal
    Route::put('/{id}', [BranchController::class, 'update']); // Actualiza una sucursal
    Route::delete('/{id}', [BranchController::class, 'destroy']); // Elimina una sucursal
});


// Rutas para inventario

Route::prefix('inventories')->group(function () {
    Route::get('/{branch_id}', [InventoryController::class, 'index']); // Obtiene una sucursal en específico
    Route::get('/{branch_id}/{item_id}', [InventoryController::class, 'show']); // Obtiene una sucursal y un producto en específico
    Route::put('/{branch_id}/{item_id}', [InventoryController::class, 'update']); // Actualiza el stock de una sucursal y un producto en específico
    Route::patch('/{branch_id}/{item_id}', [InventoryController::class, 'resetStock']); // Reinicia el stock a 0 de una sucural y producto en específico
});


//Rutas para el carrito de compras (Solo puede interactuar el usuario relacionado al carrito de compras)

Route::middleware(['auth:sanctum', 'customRole:client'])->group(function () {
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'showCart']); // Mostrar carrito
        Route::post('/add_items', [CartController::class, 'addItems']); // Agregar productos al carrito
        Route::delete('/remove_item/{item_id}', [CartController::class, 'removeItem']); // Remover un producto del carrito
        Route::delete('/empty_cart', [CartController::class, 'emptyCart']); // Vaciar todos los productos del carrito
        Route::patch('/change_branch/{branch_id}', [CartController::class, 'changeBranch']); // Cambiar la sucursal donde quiere comprar
    });
});


// Rutas para la compra o proceso de pago

Route::middleware(['auth:sanctum', 'customRole:client'])->group(function () {
    Route::post('/webpay', [WebpayController::class, 'checkout']); //Inicia proceso de compra en webpay
    Route::put('/webpay/cancel', [WebpayController::class, 'cancel']); //Cancela la compra

});

Route::any('/webpay/confirm', [WebpayController::class, 'confirm'])->name('confirmar_pago'); //Confirma el pago de webpay

// Rutas para ver ordenes de compra

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('order_details', [OrderDetailController::class, 'index'])->middleware('customRole:admin');
    Route::get('order_details/{id}', [OrderDetailController::class, 'show'])->middleware('checkBuyOrderAccess');
    Route::put('order_details/{id}', [OrderDetailController::class, 'update'])->middleware('customRole:admin');
    Route::delete('order_details/{id}', [OrderDetailController::class, 'destroy'])->middleware('customRole:admin');
});



//Rutas para las ordenes de despacho

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('shipping_order')->group(function () {
        Route::get('/', [ShippingOrderController::class, 'index']);
        Route::get('/{shippingOrder_id}', [ShippingOrderController::class, 'show']);//->middleware(['checkShippingOrderAccess']);
        Route::patch('/{shippingOrder_id}', [ShippingOrderController::class, 'update'])->middleware(['customRole:employed']);
        Route::delete('/{shippingOrder_id}', [ShippingOrderController::class, 'destroy'])->middleware('customRole:admin');
    });
});
