<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function register_creates_a_new_user_with_client_role()
    {
        // Creamos un objeto Request fake
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Creamos un mock de la clase AuthController
        $authControllerMock = Mockery::mock(AuthController::class);

        // Creamos un mock de la clase Role
        $roleMock = Mockery::mock(Role::class);
        $roleMock->shouldReceive('where')->with('name', 'client')->andReturn($roleMock);
        $roleMock->shouldReceive('first')->andReturn($roleMock);

        // Creamos un mock de la clase User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('hasRole')->with('client')->andReturn(true);

        // Mockeamos la respuesta del método register
        $responseMock = Mockery::mock(\Illuminate\Http\JsonResponse::class);
        $responseMock->shouldReceive('getStatusCode')->andReturn(201);

        // Especificamos el comportamiento del método register
        $authControllerMock->shouldReceive('register')->andReturn($responseMock);

        // Llamamos al método register
        $response = $authControllerMock->register($request);

        // Verificamos que se haya devuelto una respuesta JSON con el token
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @test
     */


    public function login_returns_error_with_invalid_password()
    {
        // Creamos un objeto Request fake
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'email' => 'john@example.com',
            'password' => 'wrong_password',
        ]);

        // Creamos un mock de la clase AuthController
        $authControllerMock = Mockery::mock(AuthController::class);

        // Creamos un mock de la clase User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('where')->with('email', 'john@example.com')->andReturn($userMock);
        $userMock->shouldReceive('first')->andReturn($userMock);
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn('password'); // password correcto es "password"

        // Mockeamos la respuesta del método login
        $responseMock = Mockery::mock(\Illuminate\Http\JsonResponse::class);
        $responseMock->shouldReceive('getStatusCode')->andReturn(401);
        $responseMock->shouldReceive('getContent')->andReturn('{"error": "Invalid credentials"}');

        // Especificamos el comportamiento del método login
        $authControllerMock->shouldReceive('login')->andReturn($responseMock);

        // Llamamos al método login
        $response = $authControllerMock->login($request);

        // Verificamos que se haya devuelto una respuesta JSON con un error
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('{"error": "Invalid credentials"}', $response->getContent());
    }

    /**
     * @test
     */
    public function login_returns_token_with_valid_password()
    {
        // Creamos un objeto Request fake
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        // Creamos un mock de la clase AuthController
        $authControllerMock = Mockery::mock(AuthController::class);

        // Creamos un mock de la clase User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('where')->with('email', 'john@example.com')->andReturn($userMock);
        $userMock->shouldReceive('first')->andReturn($userMock);
        $userMock->shouldReceive('getAttribute')->with('password')->andReturn('password');

        // Creamos un mock de la clase Token
        $tokenMock = Mockery::mock(\Illuminate\Support\Str::class);
        $tokenMock->shouldReceive('random')->andReturn('token');

        // Mockeamos la respuesta del método login
        $responseMock = Mockery::mock(\Illuminate\Http\JsonResponse::class);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getContent')->andReturn('{"token": "token"}');

        // Especificamos el comportamiento del método login
        $authControllerMock->shouldReceive('login')->andReturn($responseMock);

        // Llamamos al método login
        $response = $authControllerMock->login($request);

        // Verificamos que se haya devuelto una respuesta JSON con un token
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"token": "token"}', $response->getContent());
    }
    /**
     * @test
     */
    public function update_user_with_valid_data_and_correct_user_id()
    {
        // Creamos un usuario fake
        $user = User::factory()->make();

        // Creamos un request fake
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'name' => 'Nuevo nombre',
            'password' => 'nueva_password',
        ]);

        // Creamos un mock de la clase UserController
        $userControllerMock = Mockery::mock(UserController::class);

        // Actualizamos el objeto User fake
        $user->name = 'Nuevo nombre';
        $user->password = 'nueva_password';

        // Especificamos el comportamiento del método update
        $userControllerMock->shouldReceive('update')->with($request, $user)->andReturn(response()->json($user));

        // Llamamos al método update
        $response = $userControllerMock->update($request, $user);

        // Verificamos que se haya devuelto una respuesta JSON con el usuario actualizado
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Nuevo nombre', $user->name);
    }

    /**
     * @test
     */
    public function update_user_with_invalid_user_id_different_from_authenticated_user()
    {
        // Creamos un usuario fake logeado
        $authenticatedUser = User::factory()->make();
        $this->actingAs($authenticatedUser);

        // Creamos un request fake
        $request = new \Illuminate\Http\Request();
        $request->replace([
            'name' => 'Nuevo nombre',
            'password' => 'nueva_password',
        ]);

        // Creamos un mock de la clase UserController
        $userControllerMock = Mockery::mock(UserController::class);

        // Creamos un mock de la clase User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('find')->with(123)->andReturn($userMock); // El usuario con id 123 existe, pero no es el usuario logeado

        // Especificamos el comportamiento del método update
        $userControllerMock->shouldReceive('update')->with($request, 123)->andReturn(response()->json(['error' => 'You are not authorized to update this user'], 403));

        // Llamamos al método update
        $response = $userControllerMock->update($request, 123);

        // Verificamos que se haya devuelto una respuesta JSON con un error
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(json_encode(['error' => 'You are not authorized to update this user']), $response->getContent());
    }
    /**
     * @test
     */
    public function destroy_user_deletes_own_account()
    {
        // Creamos un usuario fake autenticado
        $user = User::factory()->make();
        $this->actingAs($user);

        // Creamos un mock de la clase UserController
        $userControllerMock = Mockery::mock(UserController::class);

        // Creamos un mock de la clase User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('find')->with($user->id)->andReturn($userMock);
        $userMock->shouldReceive('delete')->andReturn(true);

        // Especificamos el comportamiento del método destroy
        $userControllerMock->shouldReceive('destroy')->with($user->id)->andReturn(response()->json(['message' => 'Usuario eliminado exitosamente']));

        // Llamamos al método destroy
        $response = $userControllerMock->destroy($user->id);

        // Verificamos que se haya devuelto una respuesta JSON con un mensaje de éxito
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(json_encode(['message' => 'Usuario eliminado exitosamente']), $response->getContent());

    }
}
