<?php

namespace Database\Factories;

use App\Enums\AccountsType;
use App\Enums\TransactionsStatus;
use App\Enums\TransactionsType;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = collect([TransactionsType::EXPENSE->value, TransactionsType::INCOME->value])->random();

        [$amount, $category, $account] = match($type) {
            TransactionsType::INCOME->value => [rand(300,9000), Category::where('type', $type)->inRandomOrder()->first(), Account::where('type', AccountsType::ASSETS)->inRandomOrder()->first()],
            TransactionsType::EXPENSE->value => [rand(300,9000) * -1, Category::where('type', $type)->inRandomOrder()->first(), Account::where('type', AccountsType::ASSETS)->inRandomOrder()->first()],
            default => [null, null, null], // Transfer type transaction is manually handle in TransactionSeeder
        };

        return [
            'due_at' => now()->addDays(rand(5,30)),
            'type' => $type,
            'category_id' => $category->id,
            'account_id' => $account->id,
            'amount' => $amount,
            'currency' => CurrencyAlpha3::from('MYR'),
            // 'currency_amount',
            // 'currency_rate',
            // 'transfer_pair_id',
            'status' => TransactionsStatus::NONE,
            'notes' => 'Whut'.rand(1010,9090),
        ];
    }
}
