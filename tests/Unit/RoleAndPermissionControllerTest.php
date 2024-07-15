<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_store_role_with_valid_data()
    {
        $request = new Request([
            'name' => 'New Role'
        ]);

        // Mock del validador para asegurarse de que el método make sea llamado
        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => [],
            'validate' => null
        ]));

        $roleMock = Mockery::mock('overload:App\Models\Role');
        $roleMock->shouldReceive('create')->once()->andReturn((object)[
            'id' => 1,
            'name' => 'New Role',
            'guard_name' => 'api'
        ]);

        // Reemplazar la instancia de Validator en la aplicación
        $this->app->instance('validator', $validatorMock);

        $roleController = new RoleController();
        $response = $roleController->store($request);

        $this->assertEquals(201, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Rol creado exitosamente!', $responseData['message']);
        $this->assertArrayHasKey('rol', $responseData);
    }


    /**
     * @test
     */
    public function test_show_role_with_valid_id()
    {
        $roleMock = Mockery::mock('alias:App\Models\Role');
        $roleMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Role 1',
            'guard_name' => 'api'
        ]);

        $roleController = new RoleController();
        $response = $roleController->show(1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Role 1', $responseData['name']);
    }

    /**
     * @test
     */
    public function test_update_role_with_valid_data()
    {
        $request = new Request([
            'name' => 'Updated Role'
        ]);

        // Crear una clase anónima para simular el objeto Role con el método update
        $roleObject = new class
        {
            public $id = 1;
            public $name = 'Old Role';
            public $guard_name = 'api';
            public function update($data)
            {
                $this->name = $data['name'];
                return true;
            }
        };

        $roleMock = Mockery::mock('overload:App\Models\Role');
        $roleMock->shouldReceive('find')->with(1)->andReturn($roleObject);

        // Mock del validador para asegurarse de que los métodos make y validated sean llamados
        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => [],
            'validated' => [
                'name' => 'Updated Role'
            ]
        ]));

        // Reemplazar la instancia de Validator en la aplicación
        $this->app->instance('validator', $validatorMock);

        $roleController = new RoleController();
        $response = $roleController->update($request, 1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Updated Role', $responseData['name']);
    }


    /**
     * @test
     */
    public function test_store_permission_with_valid_data()
    {
        $request = new Request([
            'name' => 'New Permission'
        ]);

        // Mock del validador para asegurarse de que los métodos make y validated sean llamados
        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => [],
            'validated' => [
                'name' => 'New Permission'
            ]
        ]));

        $permissionMock = Mockery::mock('overload:Spatie\Permission\Models\Permission');
        $permissionMock->shouldReceive('create')->once()->andReturn((object)[
            'id' => 1,
            'name' => 'New Permission',
            'guard_name' => 'api'
        ]);

        // Reemplazar la instancia de Validator en la aplicación
        $this->app->instance('validator', $validatorMock);

        $permissionController = new PermissionController();
        $response = $permissionController->store($request);

        $this->assertEquals(201, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Permiso creado exitosamente!', $responseData['message']);
        $this->assertArrayHasKey('permission', $responseData);
    }


    /**
     * @test
     */
    public function test_show_permission_with_valid_id()
    {
        $permissionMock = Mockery::mock('alias:Spatie\Permission\Models\Permission');
        $permissionMock->shouldReceive('find')->with(1)->andReturn((object)[
            'id' => 1,
            'name' => 'Permission 1',
            'guard_name' => 'api'
        ]);

        $permissionController = new PermissionController();
        $response = $permissionController->show(1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Permission 1', $responseData['name']);
    }

    /**
     * @test
     */
    public function test_update_permission_with_valid_data()
    {
        $request = new Request([
            'name' => 'Updated Permission'
        ]);

        // Crear una clase anónima para simular el objeto Permission con el método update
        $permissionObject = new class
        {
            public $id = 1;
            public $name = 'Old Permission';
            public $guard_name = 'api';
            public function update($data)
            {
                $this->name = $data['name'];
                return true;
            }
        };

        $permissionMock = Mockery::mock('overload:Spatie\Permission\Models\Permission');
        $permissionMock->shouldReceive('find')->with(1)->andReturn($permissionObject);

        // Mock del validador para asegurarse de que los métodos make y validated sean llamados
        $validatorMock = Mockery::mock('alias:Illuminate\Support\Facades\Validator');
        $validatorMock->shouldReceive('make')->once()->andReturn(Mockery::mock([
            'fails' => false,
            'errors' => [],
            'validated' => [
                'name' => 'Updated Permission'
            ],
            'validate' => null
        ]));

        // Reemplazar la instancia de Validator en la aplicación
        $this->app->instance('validator', $validatorMock);

        $permissionController = new PermissionController();
        $response = $permissionController->update($request, 1);

        $this->assertEquals(200, $response->status());

        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals(1, $responseData['id']);
        $this->assertArrayHasKey('name', $responseData);
        $this->assertEquals('Updated Permission', $responseData['name']);
    }
}
