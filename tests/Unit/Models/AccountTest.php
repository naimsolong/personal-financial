<?php

use App\Models\Account;

test('Model class has correct properties', function () {
    expect(app(Account::class)->getFillable())->toBeArray()->toBe([
        'name',
        'icon',
        'type',
    ]);
});

test('Model able to perform CRUD', function () {
    $account = Account::factory()->create();
    $this->assertModelExists($account);

    $account->update([
        'name' => 'TEST',
    ]);
    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'name' => $account->name,
    ]);

    $account->delete();
    $this->assertSoftDeleted($account);
});