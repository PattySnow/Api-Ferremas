<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear rol de admin si no existe
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        
        // Crear rol de employeed si no existe
        $employeedRole = Role::firstOrCreate(['name' => 'employed', 'guard_name' => 'api']);
        
        // Crear rol de client si no existe
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);

        // Crear usuario con rol admin
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
        ]);
        $admin->assignRole($adminRole);

        // Crear usuario con rol employeed
        $employeed = User::create([
            'name' => 'employed',
            'email' => 'employed@test.com',
            'password' => Hash::make('123456'),
        ]);
        $employeed->assignRole($employeedRole);

        // Crear usuario con rol client
        $client = User::create([
            'name' => 'client',
            'email' => 'client@test.com',
            'password' => Hash::make('123456'),
        ]);
        $client->assignRole($clientRole);
    }
}
