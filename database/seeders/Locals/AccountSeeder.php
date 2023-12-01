<?php

namespace Database\Seeders\Locals;

use App\Enums\AccountsType;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Workspace;
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
        $workspace = Workspace::first();

        $groups_id = [];

        $group = $this->createAccountGroup('Cash', AccountsType::ASSETS);
        $accounts = $this->createAccounts([
            'Wallet',
        ], AccountsType::ASSETS);
        $group->accounts()->attach($this->generatePivotValues($accounts, $workspace->id));
        array_push($groups_id, $group->id);

        $group = $this->createAccountGroup('Saving', AccountsType::ASSETS);
        $accounts = $this->createAccounts([
            'ASB',
            'Tabung Haji',
        ], AccountsType::ASSETS);
        $group->accounts()->attach($this->generatePivotValues($accounts, $workspace->id));
        array_push($groups_id, $group->id);
        

        $group = $this->createAccountGroup('Loan', AccountsType::LIABILITIES);
        $accounts = $this->createAccounts([
            'Hire Purchase',
            'Mortgage',
            'PTPTN',
        ], AccountsType::LIABILITIES);
        $group->accounts()->attach($this->generatePivotValues($accounts, $workspace->id));
        array_push($groups_id, $group->id);

        $workspace->accountGroups()->sync($groups_id);
    }

    protected function createAccountGroup(string $name, AccountsType $type): AccountGroup
    {
        return AccountGroup::firstOrCreate([
            'name' => $name,
            'type' => $type,
        ]);
    }

    protected function createAccounts(array $accounts, AccountsType $type): array
    {
        return array_map(function($account) use ($type) {
            return Account::firstOrCreate([
                'name' => $account,
                'type' => $type,
            ])->id;
        }, $accounts);
    }

    public function generatePivotValues(array $accounts, int $workspace_id): array
    {
        $array = [];

        foreach($accounts as $account) {
            $array[$account] =  [
                'workspace_id' => $workspace_id,
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => rand(1000, 5000),
                'latest_balance' => rand(1000, 5000),
                'currency' => CurrencyAlpha3::from('MYR')->value,
                'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
            ];
        }
        
        return $array;
    }
}
