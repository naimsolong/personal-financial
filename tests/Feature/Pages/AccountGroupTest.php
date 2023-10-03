<?php

use App\Enums\AccountsType;
use App\Models\AccountGroup;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access account group pages', function () {
    $user = User::factory()->create();

    $accountGroup = AccountGroup::factory(3)->create([
        'type' => AccountsType::ASSETS->value
    ]);
    $response = $this->actingAs($user)
        ->get(route('account.group.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/AccountGroup/Index')
            ->has('accounts', fn (Assert $page) => $page
                ->has('assets', 3)
                ->has('liabilities', 0)
                ->where('assets.0', $accountGroup->first()->only('id', 'name', 'type'))
            )
        );
    $response->assertStatus(200);
    
    $response = $this->actingAs($user)
        ->get(route('account.group.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/AccountGroup/Form')
            ->where('types', AccountsType::dropdown())
            ->where('data', [
                'id' => '',
                'name' => '',
                'type' => '',
            ])
        );
    $response->assertStatus(200);
    
    $accountGroup = AccountGroup::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('account.group.edit', ['group' => $accountGroup->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/AccountGroup/Form')
            ->where('edit_mode', true)
            ->where('types', AccountsType::dropdown())
            ->where('data', $accountGroup->only('id','name','type'))
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::ASSETS->value
    ];
    $response = $this->actingAs($user)
        ->post(route('account.group.store'), $data);
    $response->assertRedirectToRoute('account.group.index');
    $this->assertDatabaseHas('account_groups', $data);
    
    $accountGroup = AccountGroup::factory()->create();
    $data = [
        'name' => 'test'.rand(4,10),
        'type' => AccountsType::LIABILITIES->value
    ];
    $response = $this->actingAs($user)
        ->put(route('account.group.update', ['group' => $accountGroup->id]), $data);
    $response->assertRedirectToRoute('account.group.index');
    $this->assertDatabaseHas('account_groups', collect($data)->merge(['id' => $accountGroup->id])->toArray());
    
    $response = $this->actingAs($user)
        ->delete(route('account.group.destroy', ['group' => $accountGroup->id]));
    $response->assertRedirectToRoute('account.group.index');
    $this->assertSoftDeleted($accountGroup);
});