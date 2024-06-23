<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Laravel\Sanctum\Sanctum;

class RoleAndPermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin', 'guard_name' => 'api']);
        }
    }

    // Pruebas de RoleController

    public function test_index_roles()
    {
        // Crear roles de ejemplo
        Role::create(['name' => 'role1', 'guard_name' => 'api']);
        Role::create(['name' => 'role2', 'guard_name' => 'api']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'guard_name', 'created_at', 'updated_at']
                 ]);
    }

    public function test_store_role_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/roles', [
            'name' => 'newRole'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Rol creado exitosamente!',
                     'rol' => ['name' => 'newRole']
                 ]);
    }

    public function test_destroy_role_success()
    {
        $role = Role::create(['name' => 'role1', 'guard_name' => 'api']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => "Rol '{$role->name}' eliminado con éxito."]);

        $this->assertDatabaseMissing('roles', [
            'id' => $role->id
        ]);
    }

    // Pruebas de PermissionController

    public function test_index_permissions()
    {
        // Crear permisos de ejemplo
        Permission::create(['name' => 'permission1', 'guard_name' => 'api']);
        Permission::create(['name' => 'permission2', 'guard_name' => 'api']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->getJson('/api/permissions');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name', 'guard_name', 'created_at', 'updated_at']
                 ]);
    }

    public function test_store_permission_success()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->postJson('/api/permissions', [
            'name' => 'newPermission'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Permiso creado exitosamente!',
                     'permission' => ['name' => 'newPermission']
                 ]);
    }

    public function test_destroy_permission_success()
    {
        $permission = Permission::create(['name' => 'permission1', 'guard_name' => 'api']);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => "Permiso '{$permission->name}' eliminado con éxito."]);

        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id
        ]);
    }
}
