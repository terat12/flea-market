<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    public function definition(): array
    {
        return [
            'title'       => $this->faker->words(2, true),
            'brand'       => $this->faker->randomElement(['Apple', 'Sony', 'UNIQLO', 'NIKE', null]),
            'description' => $this->faker->realText(80),
            'price'       => $this->faker->numberBetween(1000, 80000),
            'condition'   => $this->faker->numberBetween(1, 6),
            'category'    => $this->faker->randomElement(['ファッション', '家電', 'ホビー', '生活雑貨', 'スポーツ']),
            'image_path'  => null,
        ];
    }
}
