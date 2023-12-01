<?php

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountPivot;
use App\Models\Workspace;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

test('pivot table able to attach, detach and sync', function () {
    $workspace = Workspace::factory()->create();
    $accountGroup = AccountGroup::factory()->create();
    $accounts = Account::factory(10)->create();

    $random = $accounts->random();
    $accountGroup->accounts()->attach($random->id, [
        'workspace_id' => $workspace->id,
        'opening_date' => now()->addDays(rand(30, 90) + -1)->format('d/m/Y'),
        'starting_balance' => rand(1000, 5000),
        'latest_balance' => rand(5000, 7000),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => rand(0, 1) == 1 ? 'whut'.rand(3000, 9000) : null,
    ]);

    expect(AccountPivot::where(function ($query) use ($accountGroup, $random) {
        $query->where('account_group_id', $accountGroup->id)->where('account_id', $random->id);
    })->exists())->toBeTrue();

    $accountGroup->accounts()->detach($random->id);
    expect(AccountPivot::where(function ($query) use ($accountGroup, $random) {
        $query->where('account_group_id', $accountGroup->id)->where('account_id', $random->id);
    })->exists())->toBeFalse();

    $accountGroup->accounts()->syncWithPivotValues($accounts->pluck('id'), [
        'workspace_id' => $workspace->id,
        'opening_date' => now()->addDays(rand(30, 90) + -1)->format('d/m/Y'),
        'starting_balance' => rand(1000, 5000),
        'latest_balance' => rand(5000, 7000),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => rand(0, 1) == 1 ? 'whut'.rand(3000, 9000) : null,
    ]);
    expect(AccountPivot::where(function ($query) use ($accountGroup, $accounts) {
        $query->where('account_group_id', $accountGroup->id)->whereIn('account_id', $accounts->pluck('id'));
    })->exists())->toBeTrue();

    $accountGroup->accounts()->sync([
        $random->id => [
            'workspace_id' => $workspace->id,
            'opening_date' => now()->addDays(rand(30, 90) + -1)->format('d/m/Y'),
            'starting_balance' => rand(1000, 5000),
            'latest_balance' => rand(5000, 7000),
            'currency' => CurrencyAlpha3::from('MYR')->value,
            'notes' => rand(0, 1) == 1 ? 'whut'.rand(3000, 9000) : null,
        ],
    ]);
    expect(AccountPivot::where(function ($query) use ($accountGroup, $random) {
        $query->where('account_group_id', $accountGroup->id)->whereIn('account_id', [$random->id]);
    })->exists())->toBeTrue();
});
