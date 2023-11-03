<?php

use App\Enums\AccountsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Workspace;
use App\Services\AccountService;
use App\Services\WorkspaceService;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

it('able to store, update and destroy accounts table', function() {
    $workspace = Workspace::factory()->create();
    $accountGroup = AccountGroup::factory()->create();
    app(WorkspaceService::class)->change($workspace->id);
    
    $workspace->accountGroups()->attach($accountGroup->id);

    $service = app(AccountService::class);

    $data = collect([
        'account_group' => $accountGroup->id,
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value,
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => rand(4,10),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => 'TEST'.rand(4,10),
    ]);

    $is_created = $service->store($data);
    $model = $service->getModel();
    $this->assertDatabaseHas('accounts', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('account_pivot', $data->merge(['account_group_id' => $data->get('account_group'), 'account_id' => $model->id])->only('account_group_id','account_id','starting_balance')->toArray());
    expect($is_created)->toBeTrue();
    
    $accountGroup = AccountGroup::factory()->create();
    $model = Account::factory()->create();
    $workspace->accountGroups()->attach($accountGroup->id);
    $data = collect([
        'account_group' => $accountGroup->id,
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value,
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => rand(4,10),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => 'TEST'.rand(4,10),
    ]);
    $is_updated = $service->update($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('accounts', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('account_pivot', $data->merge(['account_group_id' => $data->get('account_group'), 'account_id' => $model->id])->only('account_group_id','account_id','starting_balance')->toArray());
    expect($is_updated)->toBeTrue();
    
    $is_destroyed = $service->destroy($model);
    $this->assertDatabaseMissing('account_pivot', [
        'account_group_id' => $data->get('account_group'),
        'account_id' => $model->id
    ]);
    expect($is_destroyed)->toBeTrue();
});

it('able to update latest_balance column', function() {
    $service = app(AccountService::class);

    $group1 = AccountGroup::factory()->create([
        'type' => AccountsType::ASSETS,
    ]);
    $account1 = Account::factory()->create([
        'type' => AccountsType::ASSETS,
    ]);
    $balance1 = rand(1000, 5000);
    $group1->accounts()->attach($account1->id, [
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => $balance1,
        'latest_balance' => $balance1,
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
    ]);

    $is_updated = $service->updateLatestBalance($account1->group()->first(), 100.1);

    $this->assertDatabaseHas('account_pivot', [
        'account_group_id' => $group1->id,
        'account_id' => $account1->id,
        'latest_balance' => $balance1 + 100.1
    ]);
    expect($is_updated)->toBeTrue();

    $is_updated = $service->updateLatestBalance($account1->group()->first(), 99, -1);

    $this->assertDatabaseHas('account_pivot', [
        'account_group_id' => $group1->id,
        'account_id' => $account1->id,
        'latest_balance' => $balance1 + 100.1 + 99 - 1
    ]);
    expect($is_updated)->toBeTrue();
});

it('able to throw exeception', function() {
    $service = app(AccountService::class);

    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
    
    expect(fn () => ($service->updateLatestBalance(null, 0)))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->updateLatestBalance(true, 0)))->toThrow(ServiceException::class, 'Amount cannot be zero');
});
