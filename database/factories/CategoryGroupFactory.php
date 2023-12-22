<?php

namespace Database\Factories;

use App\Enums\TransactionsType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryGroup>
 */
class CategoryGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => collect([TransactionsType::EXPENSE->value, TransactionsType::INCOME->value])->random(),
        ];
    }
}
