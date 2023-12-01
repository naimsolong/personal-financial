<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Workspace;

test('model class has correct properties', function () {
    expect(app(Transaction::class)->getFillable())->toBeArray()->toBe([
        'workspace_id',
        'due_at',
        'type',
        'category_id',
        'account_id',
        'amount',
        'currency',
        'currency_amount',
        'currency_rate',
        'transfer_pair_id',
        'status',
        'notes',
    ]);
});

test('model able to perform CRUD', function () {
    Category::factory(rand(5, 10))->create();
    Account::factory(rand(5, 10))->create();
    Workspace::factory()->create();
    $transaction = Transaction::factory()->create();
    $this->assertModelExists($transaction);

    $amount = rand(900, 4000);
    $transaction->update([
        'amount' => $amount,
    ]);
    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'amount' => $amount,
    ]);

    $transaction->delete();
    $this->assertModelMissing($transaction);
});
