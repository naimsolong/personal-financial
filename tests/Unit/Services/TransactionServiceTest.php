<?php

use App\Enums\TransactionsStatus;
use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Transaction;
use App\Services\TransactionService;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

test('modify amount', function() {
    $service = app(TransactionService::class);

    expect($service->modifyNegativeAmount(0))->toBe(0);
    expect($service->modifyPositiveAmount(0))->toBe(0);
    
    expect($service->modifyNegativeAmount(1))->toBeLessThan(0);
    expect($service->modifyPositiveAmount(1))->toBeGreaterThan(0);
    
    expect($service->modifyNegativeAmount(-1))->toBeLessThan(0);
    expect($service->modifyPositiveAmount(-1))->toBeGreaterThan(0);
});

// TODO: Update these test
it('able to store, update and destroy for expense transaction', function() {
    $service = app(TransactionService::class);

    $model = Transaction::query();
    $category = Category::factory(5)
        ->has(CategoryGroup::factory(), 'group')
        ->create();
    $balance = rand(100,500);
    $account = Account::factory(5)
        ->hasAttached(AccountGroup::factory(), [
            'opening_date' => now()->format('d/m/Y'),
            'starting_balance' => $balance,
            'latest_balance' => $balance,
            'currency' => CurrencyAlpha3::from('MYR')->value,
            'notes' => rand(0,1) == 1 ? 'whut'.rand(3000,9000) : null,
        ], 'group')
        ->create();

    $store_category = $category->random();
    $store_account = $account->random();
    $store_data = collect([
        'due_date' => now()->format('d/m/Y'),
        'due_time' => now()->format('h:i A'),
        'type' => TransactionsType::EXPENSE->value,
        'category' => $store_category->id,
        'account_from' => $store_account->id,
        'amount' => rand(10,100),
        'currency' => CurrencyAlpha3::from('MYR')->value,
        'currency_rate' => 1,
        'status' => TransactionsStatus::NONE->value,
        'notes' => 'whut',
    ]);

    $is_created = $service->store($model, $store_data);
    $model = $service->getModel();
    $this->assertDatabaseHas('transactions', [
        'id' => $model->id,
        'type' => $store_data->get('type'),
        'category_id' => $store_data->get('category'),
        'account_id' => $store_data->get('account_from'),
        'amount' => $store_data->get('amount') * -1,
    ]);
    expect($is_created)->toBeTrue();
    $this->assertDatabaseHas('account_pivot', [
        'account_group_id' => $store_account->group()->first()->id,
        'account_id' => $store_account->id,
        'latest_balance' => $balance + ($store_data->get('amount') * -1),
    ]);
    
    $update_data = $store_data->merge([
        'amount' => rand(10,100),
    ]);
    $is_updated = $service->update($model, $update_data);
    $model = $service->getModel();
    $new_balance = $balance + ($store_data->get('amount') * -1);
    $this->assertDatabaseHas('transactions', [
        'id' => $model->id,
        'type' => $update_data->get('type'),
        'category_id' => $update_data->get('category'),
        'account_id' => $update_data->get('account_from'),
        'amount' => $update_data->get('amount') * -1,
    ]);
    expect($is_updated)->toBeTrue();
    $this->assertDatabaseHas('account_pivot', [
        'account_group_id' => $store_account->group()->first()->id,
        'account_id' => $store_account->id,
        'latest_balance' => $new_balance + ($update_data->get('amount') * -1) + ($store_data->get('amount')),
    ]);
    
    // $is_destroyed = $service->destroy($model);
    // $this->assertDatabaseMissing('transactions', [
    //     'category_group_id' => $data->get('category_group'),
    //     'category_id' => $model->id
    // ]);
    // expect($is_destroyed)->toBeTrue();
});

// TODO: Update these test
it('able to store, update and destroy for income transaction', function() {
    $service = app(TransactionService::class);

    $model = Transaction::query();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value,
    ]);

    $is_created = $service->store($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('transactions', $data->only('name','type')->toArray());
    expect($is_created)->toBeTrue();
    
    $model = Transaction::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value,
    ]);
    $is_updated = $service->update($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('transactions', $data->only('name','type')->toArray());
    expect($is_updated)->toBeTrue();
    
    $is_destroyed = $service->destroy($model);
    $this->assertDatabaseMissing('transactions', [
        'category_group_id' => $data->get('category_group'),
        'category_id' => $model->id
    ]);
    expect($is_destroyed)->toBeTrue();
})->skip();

// TODO: Update these test
it('able to store, update and destroy for transfer transaction', function() {
    $service = app(TransactionService::class);

    $model = Transaction::query();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::EXPENSE->value,
    ]);

    $is_created = $service->store($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('transactions', $data->only('name','type')->toArray());
    expect($is_created)->toBeTrue();
    
    $model = Transaction::factory()->create();
    $data = collect([
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value,
    ]);
    $is_updated = $service->update($model, $data);
    $model = $service->getModel();
    $this->assertDatabaseHas('transactions', $data->only('name','type')->toArray());
    expect($is_updated)->toBeTrue();
    
    $is_destroyed = $service->destroy($model);
    $this->assertDatabaseMissing('transactions', [
        'category_group_id' => $data->get('category_group'),
        'category_id' => $model->id
    ]);
    expect($is_destroyed)->toBeTrue();
})->skip();

it('able to throw exeception', function() {
    $service = app(TransactionService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});
