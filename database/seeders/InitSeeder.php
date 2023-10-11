<?php

namespace Database\Seeders;

use App\Enums\SystemCategoryCode;
use App\Enums\TransactionsType;
use App\Models\CategoryGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!CategoryGroup::where('name', '[System]')->exists())
        {
            $group1 = CategoryGroup::create([
                'name' => '[System]',
                'type' => TransactionsType::EXPENSE,
                'only_system_flag' => true,
            ]);
            $group1->categories()->createMany([
                [
                    'name' => '[OPENING BALANCE (+)]',
                    'type' => TransactionsType::EXPENSE,
                    'code' => SystemCategoryCode::OPENING_POSITIVE->value,
                    'only_system_flag' => true,
                ],
                [
                    'name' => '[OPENING BALANCE (-)]',
                    'type' => TransactionsType::INCOME,
                    'code' => SystemCategoryCode::OPENING_NEGATIVE->value,
                    'only_system_flag' => true,
                ],
                [
                    'name' => '[ADJUSTMENT (+)]',
                    'type' => TransactionsType::EXPENSE,
                    'code' => SystemCategoryCode::ADJUST_POSITIVE->value,
                    'only_system_flag' => true,
                ],
                [
                    'name' => '[ADJUSTMENT (-)]',
                    'type' => TransactionsType::INCOME,
                    'code' => SystemCategoryCode::ADJUST_NEGATIVE->value,
                    'only_system_flag' => true,
                ],
            ]);
        }
    }
}
