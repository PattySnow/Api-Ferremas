<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            'Herramientas manuales' => [
                'Martillo',
                'Destornillador',
                'Llave inglesa',
                'Alicates',
                'Sierra manual'
            ],
            'Materiales de construcción' => [
                'Cemento',
                'Ladrillos',
                'Arena',
                'Grava',
                'Madera'
            ],
            'Pinturas y accesorios' => [
                'Pintura blanca',
                'Rodillo',
                'Brocha',
                'Cinta de pintor',
                'Lijadora'
            ],
            'Electricidad' => [
                'Cable eléctrico',
                'Interruptor',
                'Enchufe',
                'Lámpara',
                'Caja de fusibles'
            ],
            'Fontanería' => [
                'Tubería PVC',
                'Llave de paso',
                'Grifo',
                'Sifón',
                'Cinta de teflón'
            ],
        ];

        $categoryName = array_rand($categories);
        $items = $categories[$categoryName];

        return [
            'name' => $this->faker->randomElement($items),
            'description' => $this->faker->sentence,
            'category_id' => Category::where('name', $categoryName)->first()->id,
            'price' => $this->faker->randomFloat(2, 1, 1000), // Precio entre 1 y 1000 con dos decimales
        ];
    }
}
