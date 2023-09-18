<?php

use App\Models\Label;

test('model class has correct properties', function () {
    expect(app(Label::class)->getFillable())->toBeArray()->toBe([
        'name',
    ]);
});

test('model able to perform CRUD', function () {
    $label = Label::factory()->create();
    $this->assertModelExists($label);

    $label->update([
        'name' => 'TEST',
    ]);
    $this->assertDatabaseHas('Labels', [
        'id' => $label->id,
        'name' => $label->name,
    ]);

    $label->delete();
    $this->assertSoftDeleted($label);
});