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
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        return [
            'name' => $this->faker->word,
            'price' => $this->faker->numberBetween(100, 100000),
            'description' => $this->faker->sentence,
            'category_id' => $category->id,
        ];
    }
}
