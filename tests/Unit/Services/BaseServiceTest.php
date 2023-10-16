<?php

use App\Exceptions\ServiceException;
use App\Models\User;
use App\Services\BaseService;

test('default model is null', function () {
    expect(app(BaseService::class)->getModel())->toBeNull();
});

it('able to store, update and destroy model', function() {
    $service = app(BaseService::class);

    $user = User::query();
    $data = collect([
        'name' => 'Name '.rand(5,10),
        'email' => 'test'.rand(5,10).'@email.com',
        'password' => 'password',
    ]);

    $is_created = $service->store($user, $data);
    $this->assertDatabaseHas('users', $data->toArray());
    expect($is_created)->toBeTrue();
    
    $user = User::factory()->create();
    $data = collect([
        'name' => 'Name '.rand(1000,200),
        'email' => 'test'.rand(1000,200).'@email.com',
        'password' => 'password',
    ]);
    $is_updated = $service->update($user, $data);
    $this->assertDatabaseHas('users', collect($data)->merge([
        'id' => $user->id
    ])->toArray());
    expect($is_updated)->toBeTrue();
    
    $user = User::factory()->create();
    $is_destroyed = $service->destroy($user);
    $this->assertModelMissing($user);
    expect($is_destroyed)->toBeTrue();
});

it('able to throw exeception', function() {
    $service = app(BaseService::class);

    expect(fn () => ($service->store(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->update(null, collect([]))))->toThrow(ServiceException::class, 'Model Not Found');
    expect(fn () => ($service->destroy(null)))->toThrow(ServiceException::class, 'Model Not Found');
});