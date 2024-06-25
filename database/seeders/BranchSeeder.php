<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $branches = [
            [
                'name' => 'Puente Alto',
                'address' => 'Puente Alto',
            ],
            [
                'name' => 'La Florida',
                'address' => 'La Florida',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
