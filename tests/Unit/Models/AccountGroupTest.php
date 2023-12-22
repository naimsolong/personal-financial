<?php

use App\Models\AccountGroup;

test('model class has correct properties', function () {
    expect(app(AccountGroup::class)->getFillable())->toBeArray()->toBe([
        'name',
        'type',
    ]);
});

test('model able to perform CRUD', function () {
    $accountGroup = AccountGroup::factory()->create();
    $this->assertModelExists($accountGroup);

    $accountGroup->update([
        'name' => 'TEST',
    ]);
    $this->assertDatabaseHas('account_groups', [
        'id' => $accountGroup->id,
        'name' => $accountGroup->name,
    ]);

    $accountGroup->delete();
    $this->assertSoftDeleted($accountGroup);
});
