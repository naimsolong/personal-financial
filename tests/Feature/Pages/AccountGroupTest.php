<?php

use App\Enums\AccountsType;
use App\Models\AccountGroup;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access account group pages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    app(WorkspaceService::class)->change($workspace->id);
    $accountGroup = AccountGroup::factory(3)->create([
        'type' => AccountsType::ASSETS->value,
    ]);
    $workspace->accountGroups()->sync($accountGroup->pluck('id'));

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('account.group.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/AccountGroup/Index')
            ->has('accounts', fn (Assert $page) => $page
                ->has('assets', 3)
                ->has('liabilities', 0)
                ->where('assets.0', $accountGroup->first()->only('id', 'name', 'type'))
            )
        );
    $response->assertSuccessful();

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
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
    $response->assertSuccessful();

    $accountGroup = AccountGroup::factory()->create();
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('account.group.edit', ['group' => $accountGroup->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/AccountGroup/Form')
            ->where('edit_mode', true)
            ->where('types', AccountsType::dropdown())
            ->where('data', $accountGroup->only('id', 'name', 'type'))
        );
    $response->assertSuccessful();
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    app(WorkspaceService::class)->change($workspace->id);

    $data = [
        'name' => 'test'.rand(4, 10),
        'type' => AccountsType::ASSETS->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('account.group.store'), $data);
    $response->assertRedirectToRoute('account.group.index');
    $this->assertDatabaseHas('account_groups', $data);

    $accountGroup = AccountGroup::factory()->create();
    $data = [
        'name' => 'tested'.rand(4, 10),
        'type' => AccountsType::LIABILITIES->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->put(route('account.group.update', ['group' => $accountGroup->id]), $data);
    $response->assertRedirectToRoute('account.group.index');
    $this->assertDatabaseHas('account_groups', $data);

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('account.group.destroy', ['group' => $accountGroup->id]));
    $response->assertRedirectToRoute('account.group.index');
});
