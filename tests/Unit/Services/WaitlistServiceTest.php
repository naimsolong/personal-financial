<?php

use App\Models\Waitlist;
use App\Services\WaitlistService;

it('able to store, update and destroy waitlist table', function () {
    $service = app(WaitlistService::class);

    $data = collect([
        'email' => 'test'.rand(4, 10).'@email.com',
    ]);
    $is_created = $service->store($data);
    $this->assertDatabaseHas('waitlists', $data->toArray());
    expect($is_created)->toBeTrue();

    $model = Waitlist::factory()->create();
    $data = collect([
        'email' => 'test'.rand(4, 10).'@email.com',
    ]);
    $is_updated = $service->update($model, $data);
    $this->assertDatabaseHas('waitlists', collect($data)->merge([
        'id' => $model->id,
    ])->toArray());
    expect($is_updated)->toBeTrue();

    $model = Waitlist::factory()->create();
    $is_destroyed = $service->destroy($model);
    $this->assertModelMissing($model);
    expect($is_destroyed)->toBeTrue();
});

it('allow user to join', function () {
    $service = app(WaitlistService::class);

    $email = 'test'.rand(4, 10).'@email.com';
    $is_created = $service->join($email);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email
    ]);
    expect($is_created)->toBeTrue();
});