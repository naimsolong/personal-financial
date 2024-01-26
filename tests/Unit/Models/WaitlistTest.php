<?php

use App\Models\Waitlist;

test('model class has correct properties', function () {
    expect(app(Waitlist::class)->getFillable())->toBeArray()->toBe([
        'email',
    ]);
});

test('model able to perform CRUD', function () {
    $waitlist = Waitlist::factory()->create();
    $this->assertModelExists($waitlist);

    $waitlist->update([
        'email' => 'TEST@email.com',
    ]);
    $this->assertDatabaseHas('waitlists', [
        'id' => $waitlist->id,
        'email' => $waitlist->email,
    ]);

    $waitlist->delete();
    $this->assertModelMissing($waitlist);
});
