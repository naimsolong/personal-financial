<?php

use App\Enums\TransactionsType;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access category pages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    $categoryGroup = CategoryGroup::factory(3)
        ->hasAttached(
            Category::factory(rand(1, 10), ['type' => TransactionsType::EXPENSE->value]),
            [
                'workspace_id' => $workspace->id,
            ]
        )
        ->create([
            'type' => TransactionsType::EXPENSE->value,
        ]);
    $workspace->categoryGroups()->sync($categoryGroup->pluck('id'));

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('categories.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Categories/Index')
            ->has('categories', fn (Assert $page) => $page
                ->has('expense', 3)
                ->has('income', 0)
            )
        );
    $response->assertStatus(200);

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('categories.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Categories/Form')
            ->has('category_group.expense', 3)
            ->has('category_group.income', 0)
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', [
                'id' => '',
                'name' => '',
                'category_group' => '',
                'type' => '',
            ])
        );
    $response->assertStatus(200);

    $category = $categoryGroup->first()->categories()->first();
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('categories.edit', ['category' => $category->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Categories/Form')
            ->has('category_group.expense', 3)
            ->has('category_group.income', 0)
            ->where('edit_mode', true)
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', collect($category->only('id', 'name', 'type'))->merge(['category_group' => $categoryGroup->first()->id])->toArray())
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    app(WorkspaceService::class)->change($workspace->id);
    $categoryGroup1 = CategoryGroup::factory()
        ->create([
            'type' => TransactionsType::EXPENSE->value,
        ]);
    $categoryGroup2 = CategoryGroup::factory()
        ->create([
            'type' => TransactionsType::EXPENSE->value,
        ]);
    $workspace->categoryGroups()->syncWithoutDetaching($categoryGroup1->pluck('id'));
    $workspace->categoryGroups()->syncWithoutDetaching($categoryGroup2->pluck('id'));

    $data = [
        'name' => 'testcreate'.rand(4, 10),
        'category_group' => $categoryGroup1->id,
        'type' => TransactionsType::EXPENSE->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('categories.store'), $data);
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseHas('categories', collect($data)->except('category_group')->toArray());

    $created_category = Category::where('name', $data['name'])->first();
    $this->assertModelExists($created_category);
    $data = [
        'name' => 'testupdate'.rand(4, 10),
        'category_group' => $categoryGroup2->id,
        'type' => TransactionsType::INCOME->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->put(route('categories.update', ['category' => $created_category->id]), $data);
    $updated_category = Category::where('name', $data['name'])->first();
    $this->assertModelExists($updated_category);
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseHas('category_pivot', [
        'workspace_id' => $workspace->id,
        'category_id' => $updated_category->id,
        'category_group_id' => $data['category_group'],
    ]);
    $this->assertDatabaseHas('categories', collect($data)->merge(['id' => $updated_category->id])->except('category_group')->toArray());

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('categories.destroy', ['category' => $updated_category->id]));
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseMissing('category_pivot', [
        'category_id' => $updated_category->id,
        'category_group_id' => $data['category_group'],
    ]);
});
