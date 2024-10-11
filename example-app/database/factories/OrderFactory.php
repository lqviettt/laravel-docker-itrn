<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name,
            'customer_phone' => $this->faker->numerify('09########'),
            'status' => $this->faker->randomElement(['pending', 'shipping', 'delivered', 'canceled']),
            'shipping_address' => $this->faker->address,
        ];
    }
}
