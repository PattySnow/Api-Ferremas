<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            CreateRolesSeeder::class,
            CreateUsersSeeder::class,
            BranchSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            InventoriesSeeder::class,
            
        ]);

    }
}
