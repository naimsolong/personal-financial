<?php

use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Transaction;
use App\Services\TransactionService;

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
