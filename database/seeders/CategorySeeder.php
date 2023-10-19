<?php

namespace Database\Seeders;

use App\Enums\TransactionsType;
use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group1 = CategoryGroup::create([
            'name' => 'Bill',
            'type' => TransactionsType::EXPENSE,
        ]);
        $group1->categories()->createMany([
            [
                'name' => 'Electricity',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Internet',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Water',
                'type' => TransactionsType::EXPENSE,
            ],
        ]);

        $group2 = CategoryGroup::create([
            'name' => 'Food',
            'type' => TransactionsType::EXPENSE,
        ]);
        $group2->categories()->createMany([
            [
                'name' => 'Breakfast',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Lunch',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Dinner',
                'type' => TransactionsType::EXPENSE,
            ],
        ]);

        $group3 = CategoryGroup::create([
            'name' => 'Transportation',
            'type' => TransactionsType::EXPENSE,
        ]);
        $group3->categories()->createMany([
            [
                'name' => 'Fuel',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Parking',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Toll',
                'type' => TransactionsType::EXPENSE,
            ],
            [
                'name' => 'Others',
                'type' => TransactionsType::EXPENSE,
            ],
        ]);

        $group5 = CategoryGroup::create([
            'name' => 'Others',
            'type' => TransactionsType::EXPENSE,
        ]);
        $group5->categories()->createMany([
            [
                'name' => 'Other',
                'type' => TransactionsType::EXPENSE,
            ],
        ]);

        $group4 = CategoryGroup::create([
            'name' => 'Active',
            'type' => TransactionsType::INCOME,
        ]);
        $group4->categories()->createMany([
            [
                'name' => 'Net Salary',
                'type' => TransactionsType::INCOME,
            ],
            [
                'name' => 'Freelance',
                'type' => TransactionsType::INCOME,
            ],
        ]);

        $group5 = CategoryGroup::create([
            'name' => 'Others',
            'type' => TransactionsType::INCOME,
        ]);
        $group5->categories()->createMany([
            [
                'name' => 'Other',
                'type' => TransactionsType::INCOME,
            ],
        ]);

        // CategoryGroup::factory(rand(5,15))
        //     ->has(Category::factory()->count(rand(1,10)))
        //     ->create();
    }
}
