<?php

use App\Enums\TransactionsType;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access category pages', function () {
    $user = User::factory()->create();

    $categoryGroup = CategoryGroup::factory(3)
        ->has(Category::factory(rand(1,10), ['type' => TransactionsType::EXPENSE->value]))
        ->create([
            'type' => TransactionsType::EXPENSE->value
        ]);
    $response = $this->actingAs($user)
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
        ->get(route('categories.edit', ['category' => $category->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Categories/Form')
            ->has('category_group.expense', 3)
            ->has('category_group.income', 0)
            ->where('edit_mode', true)
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', collect($category->only('id','name','type'))->merge(['category_group' => $categoryGroup->first()->id])->toArray())
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();
    $categoryGroup1 = CategoryGroup::factory()
        ->create([
            'type' => TransactionsType::EXPENSE->value
        ]);
    $categoryGroup2 = CategoryGroup::factory()
        ->create([
            'type' => TransactionsType::EXPENSE->value
        ]);

    $data = [
        'name' => 'test'.rand(4,10),
        'category_group' => $categoryGroup1->id,
        'type' => TransactionsType::EXPENSE->value
    ];
    $response = $this->actingAs($user)
        ->post(route('categories.store'), $data);
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseHas('categories', collect($data)->except('category_group')->toArray());
    
    $category = Category::factory()->create();
    $this->assertModelExists($category);
    $data = [
        'name' => 'test'.rand(4,10),
        'category_group' => $categoryGroup2->id,
        'type' => TransactionsType::INCOME->value
    ];
    $response = $this->actingAs($user)
        ->put(route('categories.update', ['category' => $category->id]), $data);
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseHas('categories', collect($data)->merge(['id' => $category->id])->except('category_group')->toArray());
    
    $response = $this->actingAs($user)
        ->delete(route('categories.destroy', ['category' => $category->id]));
    $response->assertRedirectToRoute('categories.index');
    $this->assertDatabaseMissing('category_pivot', [
        'category_id' => $category->id,
        'category_group_id' => $data['category_group'],
    ]);
});