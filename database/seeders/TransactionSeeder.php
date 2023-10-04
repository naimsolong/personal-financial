<?php

namespace Database\Seeders;

use App\Enums\AccountsType;
use App\Enums\TransactionsType;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For INCOME and EXPENSE
        for($i = 0; $i < 300; $i++)
            Transaction::factory(rand(1,10))->create([
                'due_at' => now()->subDay($i)
            ]);

        $accounts = Account::where('type', AccountsType::ASSETS)->inRandomOrder()->get();

        // For TRANSFER
        for($i = 0; $i < 300; $i++) {
            $due_at = now()->subDay($i);
            $amount = rand(300,9000);

            $transaction = Transaction::factory(2)
                ->state(new Sequence(
                    [
                        'due_at' => $due_at,
                        'type' => TransactionsType::TRANSFER,
                        'category_id' => null,
                        'account_id' => $accounts->first()->id,
                        'amount' => $amount
                    ],
                    [
                        'due_at' => $due_at,
                        'type' => TransactionsType::TRANSFER,
                        'category_id' => null,
                        'account_id' => $accounts->last()->id,
                        'amount' => $amount * -1
                    ]
                ))->create();

            $transaction->first()->update([
                'transfer_pair_id' => $transaction->last()->id
            ]);
            $transaction->last()->update([
                'transfer_pair_id' => $transaction->first()->id
            ]);
        }

        // TODO: Run transaction seeder with currency
    }
}
