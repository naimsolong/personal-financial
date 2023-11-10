<?php

namespace Database\Seeders\Locals;

use App\Enums\TransactionsType;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workspace = Workspace::first();

        $groups_id = [];

        $group = $this->createCategoryGroup('Bill', TransactionsType::EXPENSE);
        $categories_id = $this->createCategories([
            'Electricity',
            'Internet',
            'Water',
        ], TransactionsType::EXPENSE);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);

        $group = $this->createCategoryGroup('Food', TransactionsType::EXPENSE);
        $categories_id = $this->createCategories([
            'Breakfast',
            'Lunch',
            'Dinner',
        ], TransactionsType::EXPENSE);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);

        $group = $this->createCategoryGroup('Transportation', TransactionsType::EXPENSE);
        $categories_id = $this->createCategories([
            'Fuel',
            'Parking',
            'Toll',
            'Others',
        ], TransactionsType::EXPENSE);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);

        $group = $this->createCategoryGroup('Others', TransactionsType::EXPENSE);
        $categories_id = $this->createCategories([
            'Others',
        ], TransactionsType::EXPENSE);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);
        


        $group = $this->createCategoryGroup('Active', TransactionsType::INCOME);
        $categories_id = $this->createCategories([
            'Net Salary',
            'Freelance',
        ], TransactionsType::INCOME);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);

        $group = $this->createCategoryGroup('Others', TransactionsType::INCOME);
        $categories_id = $this->createCategories([
            'Others',
        ], TransactionsType::INCOME);
        $group->categories()->syncWithPivotValues($categories_id, ['workspace_id' => $workspace->id]);
        array_push($groups_id, $group->id);

        $workspace->categoryGroups()->sync($groups_id);
    }

    protected function createCategoryGroup(string $name, TransactionsType $type): CategoryGroup
    {
        return CategoryGroup::firstOrCreate([
            'name' => $name,
            'type' => $type,
        ]);
    }

    protected function createCategories(array $categories, TransactionsType $type): array
    {
        return array_map(function($category) use ($type) {
            return Category::firstOrCreate([
                'name' => $category,
                'type' => $type,
            ])->id;
        }, $categories);
    }
}
