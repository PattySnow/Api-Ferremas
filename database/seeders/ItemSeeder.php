<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            'Herramientas manuales',
            'Materiales de construcción',
            'Pinturas y accesorios',
            'Electricidad',
            'Fontanería',
        ];

        foreach ($categories as $categoryName) {
            $category = Category::firstOrCreate(['name' => $categoryName]);

            switch ($categoryName) {
                case 'Herramientas manuales':
                    $items = ['Martillo', 'Destornillador', 'Llave inglesa', 'Alicates', 'Sierra manual'];
                    break;
                case 'Materiales de construcción':
                    $items = ['Cemento', 'Ladrillos', 'Arena', 'Grava', 'Madera'];
                    break;
                case 'Pinturas y accesorios':
                    $items = ['Pintura blanca', 'Rodillo', 'Brocha', 'Cinta de pintor', 'Lijadora'];
                    break;
                case 'Electricidad':
                    $items = ['Cable eléctrico', 'Interruptor', 'Enchufe', 'Lámpara', 'Caja de fusibles'];
                    break;
                case 'Fontanería':
                    $items = ['Tubería PVC', 'Llave de paso', 'Grifo', 'Sifón', 'Cinta de teflón'];
                    break;
                default:
                    $items = [];
            }

            foreach ($items as $itemName) {
                Item::create([
                    'name' => $itemName,
                    'description' => 'Descripción de ' . $itemName,
                    'category_id' => $category->id,
                    'price' => rand(10, 500), // Precio aleatorio entre 10 y 500
                ]);
            }
        }
    }
}
