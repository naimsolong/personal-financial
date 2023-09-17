<?php

namespace Database\Seeders;

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
        AccountGroup::factory(rand(5,15))
            ->hasAttached(
                Account::factory()->count(rand(1,10)),
                function() {
                    return [
                        'opening_date' => now()->addDays(rand(30,90) + -1),
                        'starting_balance' => rand(1000, 5000),
                        'latest_balance' => rand(5000, 7000),
                        'currency' => 'MYR',
                        'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
                    ];
                }
            )
            ->create();
    }
}
