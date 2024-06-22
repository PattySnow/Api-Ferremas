<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear el rol de admin
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        
        // Crear el rol de client
        Role::create(['name' => 'client', 'guard_name' => 'api']);
        
        // Crear el rol de employeed
        Role::create(['name' => 'employed', 'guard_name' => 'api']);
    }
}
