<?php

namespace Database\Seeders;

use App\Enums\AccountsType;
use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = AccountGroup::create([
            'name' => 'Cash',
            'type' => AccountsType::ASSETS,
        ]);
        $account1 = Account::create([
            'name' => 'Wallet',
            'type' => AccountsType::ASSETS,
        ]);
        $balance1 = rand(1000, 5000);
        $group1->accounts()->attach($account1->id, [
            'opening_date' => now()->format('d/m/Y'),
            'starting_balance' => $balance1,
            'latest_balance' => $balance1,
            'currency' => 'MYR',
            'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
        ]);

        $group2 = AccountGroup::create([
            'name' => 'Saving',
            'type' => AccountsType::ASSETS,
        ]);
        $account2 = Account::create([
            'name' => 'ASB',
            'type' => AccountsType::ASSETS,
        ]);
        $account3 = Account::create([
            'name' => 'Tabung Haji',
            'type' => AccountsType::ASSETS,
        ]);
        $balance2 = rand(1000, 5000);
        $balance3 = rand(1000, 5000);
        $group2->accounts()->attach([
            $account2->id => [
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => $balance2,
                'latest_balance' => $balance2,
                'currency' => 'MYR',
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ],
            $account3->id => [
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => $balance3,
                'latest_balance' => $balance3,
                'currency' => 'MYR',
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ],
        ]);

        $group3 = AccountGroup::create([
            'name' => 'Loan',
            'type' => AccountsType::LIABILITIES,
        ]);
        $account5 = Account::create([
            'name' => 'Hire Purchase',
            'type' => AccountsType::LIABILITIES,
        ]);
        $account6 = Account::create([
            'name' => 'Mortgage',
            'type' => AccountsType::LIABILITIES,
        ]);
        $account7 = Account::create([
            'name' => 'PTPTN',
            'type' => AccountsType::LIABILITIES,
        ]);
        $balance5 = rand(1000, 5000);
        $balance6 = rand(1000, 5000);
        $balance7 = rand(1000, 5000);
        $group3->accounts()->attach([
            $account5->id => [
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => $balance5,
                'latest_balance' => $balance5,
                'currency' => 'MYR',
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ],
            $account6->id => [
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => $balance6,
                'latest_balance' => $balance6,
                'currency' => 'MYR',
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ],
            $account7->id => [
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => $balance7,
                'latest_balance' => $balance7,
                'currency' => 'MYR',
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ],
        ]);

        // AccountGroup::factory(rand(5,15))
        //     ->hasAttached(
        //         Account::factory()->count(rand(1,10)),
        //         function() {
        //             return [
        //                 'opening_date' => now()->format('d/m/Y')->addDays(rand(30,90) + -1),
        //                 'starting_balance' => rand(1000, 5000),
        //                 'latest_balance' => rand(5000, 7000),
        //                 'currency' => 'MYR',
        //                 'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
        //             ];
        //         }
        //     )
        //     ->create();
    }
}
