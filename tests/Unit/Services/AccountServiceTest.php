<?php

use App\Enums\AccountsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Services\AccountService;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

it('able to store, update and destroy accounts table', function() {
    $service = app(AccountService::class);

    $model = Account::query();
    $data = collect([
        'account_group' => AccountGroup::factory()->create()->id,
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value,
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => rand(4,10),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'notes' => 'TEST'.rand(4,10),
    ]);

    $is_created = $service->store($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('accounts', $data->only('name','type')->toArray());
    $this->assertDatabaseHas('account_pivot', $data->merge(['account_group_id' => $data->get('account_group'), 'account_id' => $model->id])->only('account_group_id','account_id','starting_balance')->toArray());
    expect($is_created)->toBeTrue();
    
    $model = Account::factory()->create();
    $data = collect([
        'account_group' => AccountGroup::factory()->create()->id,
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

it('able to throw exeception if model is null', function() {
    $service = app(AccountService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});
