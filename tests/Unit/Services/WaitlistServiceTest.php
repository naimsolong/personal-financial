<?php

use App\Enums\WaitlistStatus;
use App\Models\Waitlist;
use App\Services\WaitlistService;

it('able to store, update and destroy waitlist table', function (string $email) {
    $service = app(WaitlistService::class);

    $data = collect([
        'email' => $email,
    ]);
    $is_created = $service->store($data);
    $this->assertDatabaseHas('waitlists', $data->toArray());
    expect($is_created)->toBeTrue();

    $model = Waitlist::factory()->create();
    $data = collect([
        'email' => '2'.$email,
        'status' => WaitlistStatus::APPROVE
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
})->with('waitlist-email');

it('allow user to join', function (string $email) {
    $service = app(WaitlistService::class);

    $is_created = $service->join($email);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
    ]);
    expect($is_created)->toBeTrue();
})->with('waitlist-email');

it('allow user to re-join after ignored', function (string $email) {
    Waitlist::factory()->create([
        'email' => $email,
        'status' => WaitlistStatus::IGNORE,
    ]);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
        'status' => WaitlistStatus::IGNORE,
    ]);

    $service = app(WaitlistService::class);

    $is_rejoin = $service->join($email);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
        'status' => WaitlistStatus::PENDING,
    ]);
    expect($is_rejoin)->toBeTrue();
})->with('waitlist-email');

it('not allow user to re-join after rejected', function (string $email) {
    Waitlist::factory()->create([
        'email' => $email,
        'status' => WaitlistStatus::REJECT,
    ]);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
        'status' => WaitlistStatus::REJECT,
    ]);

    $service = app(WaitlistService::class);

    $is_rejected = $service->join($email);
    $this->assertDatabaseHas('waitlists', [
        'email' => $email,
        'status' => WaitlistStatus::REJECT,
    ]);
    expect($is_rejected)->toBeTrue();
})->with('waitlist-email');