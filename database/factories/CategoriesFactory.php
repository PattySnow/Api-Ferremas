<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word, // Este no se usará, pero es necesario definirlo
        ];
    }

    /**
     * Define realistic categories for a hardware store.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function predefinedCategories()
    {
        $categories = [
            'Herramientas manuales',
            'Materiales de construcción',
            'Pinturas y accesorios',
            'Electricidad',
            'Fontanería',
        ];

        return $this->state(function (array $attributes) use ($categories) {
            return [
                'name' => $categories[array_rand($categories)],
            ];
        });
    }
}
