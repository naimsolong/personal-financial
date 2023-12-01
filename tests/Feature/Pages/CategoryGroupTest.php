<?php

use App\Enums\TransactionsType;
use App\Models\CategoryGroup;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access category group pages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    app(WorkspaceService::class)->change($workspace->id);
    $categoryGroup = CategoryGroup::factory(3)->create([
        'type' => TransactionsType::EXPENSE->value,
    ]);
    $workspace->categoryGroups()->sync($categoryGroup->pluck('id'));

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('category.group.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/CategoryGroup/Index')
            ->has('categories', fn (Assert $page) => $page
                ->has('expense', 3)
                ->has('income', 0)
                ->where('expense.0', $categoryGroup->first()->only('id', 'name', 'type'))
            )
        );
    $response->assertSuccessful();

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('category.group.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/CategoryGroup/Form')
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', [
                'id' => '',
                'name' => '',
                'type' => '',
            ])
        );
    $response->assertSuccessful();

    $categoryGroup = CategoryGroup::factory()->create();
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('category.group.edit', ['group' => $categoryGroup->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/CategoryGroup/Form')
            ->where('edit_mode', true)
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', $categoryGroup->only('id', 'name', 'type'))
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
        'type' => TransactionsType::EXPENSE->value,
    ];

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('category.group.store'), $data);
    $response->assertRedirectToRoute('category.group.index');
    $this->assertDatabaseHas('category_groups', $data);

    $categoryGroup = CategoryGroup::factory()->create();

    $data = [
        'name' => 'tested'.rand(4, 10),
        'type' => TransactionsType::INCOME->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->put(route('category.group.update', ['group' => $categoryGroup->id]), $data);
    $response->assertRedirectToRoute('category.group.index');
    $this->assertDatabaseHas('category_groups', $data);

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('category.group.destroy', ['group' => $categoryGroup->id]));
    $response->assertRedirectToRoute('category.group.index');
});
