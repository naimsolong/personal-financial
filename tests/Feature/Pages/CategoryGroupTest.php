<?php

use App\Enums\TransactionsType;
use App\Models\CategoryGroup;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('user can access category group pages', function () {
    $user = User::factory()->create();

    $categoryGroup = CategoryGroup::factory(3)->create([
        'type' => TransactionsType::EXPENSE->value
    ]);
    $response = $this->actingAs($user)
        ->get(route('category.group.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/CategoryGroup/Index')
            ->has('categories', fn (Assert $page) => $page
                ->has('expense', 3)
                ->has('income', 0)
                ->where('expense.0', $categoryGroup->first()->only('id', 'name', 'type'))
            )
        );
    $response->assertStatus(200);
    
    $response = $this->actingAs($user)
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
    $response->assertStatus(200);
    
    $categoryGroup = CategoryGroup::factory()->create();
    $response = $this->actingAs($user)
        ->get(route('category.group.edit', ['group' => $categoryGroup->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/CategoryGroup/Form')
            ->where('edit_mode', true)
            ->where('types', collect(TransactionsType::dropdown())->whereIn('value', ['E', 'I'])->toArray())
            ->where('data', $categoryGroup->only('id','name','type'))
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();

    $data = [
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::EXPENSE->value
    ];
    $response = $this->actingAs($user)
        ->post(route('category.group.store'), $data);
    $response->assertRedirectToRoute('category.group.index');
    $this->assertDatabaseHas('category_groups', $data);
    
    $categoryGroup = CategoryGroup::factory()->create();
    $data = [
        'name' => 'test'.rand(4,10),
        'type' => TransactionsType::INCOME->value
    ];
    $response = $this->actingAs($user)
        ->put(route('category.group.update', ['group' => $categoryGroup->id]), $data);
    $response->assertRedirectToRoute('category.group.index');
    $this->assertDatabaseHas('category_groups', collect($data)->merge(['id' => $categoryGroup->id])->toArray());
    
    $response = $this->actingAs($user)
        ->delete(route('category.group.destroy', ['group' => $categoryGroup->id]));
    $response->assertRedirectToRoute('category.group.index');
    $this->assertSoftDeleted($categoryGroup);
});