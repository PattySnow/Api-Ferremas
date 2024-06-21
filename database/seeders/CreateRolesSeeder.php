<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class CreateRolesSeeder extends Seeder
{
    public function run()
    {
        // Lista de permisos
        $permissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            'view branches',
            'create branches',
            'edit branches',
            'delete branches',
            'view inventory',
            'edit inventory',
            'view shipping order',
            'edit shipping order'
        ];

        // Crear permisos con guard_name 'api'
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Crear roles
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $workerRole = Role::firstOrCreate(['name' => 'worker', 'guard_name' => 'api']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        // Asignar permisos a roles
        $clientRole->givePermissionTo([
            'view products',
            'view categories',
            'view branches',
            'view inventory'
        ]);

        $workerRole->givePermissionTo([
            'view products',
            'create products',
            'edit products',
            'view categories',
            'create categories',
            'edit categories',
            'view branches',
            'create branches',
            'edit branches',
            'view inventory',
            'edit inventory',
            'view shipping order',
            'edit shipping order'
        ]);

        $adminRole->givePermissionTo(Permission::all());

        // Asignar un rol a un usuario específico (ejemplo)
        $user = User::find(1); // Asegúrate de que este usuario exista en tu base de datos
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
