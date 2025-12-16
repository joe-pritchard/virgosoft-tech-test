<?php

namespace Database\Factories;

use App\Enums\AssetSymbol;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
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
            'symbol' => collect(AssetSymbol::cases())->map(fn ($case) => $case->value)->random(),
            'side' => $this->faker->randomElement(['buy', 'sell']),
            'price' => $this->faker->randomFloat(8, 1, 100000),
            'amount' => $this->faker->randomFloat(8, 0.01, 10),
            'status' => collect(OrderStatus::cases())->map(fn ($case) => $case->value)->random(),
        ];
    }
}
