<?php

namespace Database\Seeders;

use App\Enums\SystemCategoryCode;
use App\Enums\TransactionsType;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! CategoryGroup::where('name', 'LIKE', '[System %')->exists()) {
            $expense_category_group = CategoryGroup::firstOrCreate([
                'name' => '[System (-)]',
                'type' => TransactionsType::EXPENSE,
                'only_system_flag' => true,
            ]);
            $income_category_group = CategoryGroup::firstOrCreate([
                'name' => '[System (+)]',
                'type' => TransactionsType::INCOME,
                'only_system_flag' => true,
            ]);
        }

        if (! Category::where('name', 'LIKE', '[OPENING BALANCE %')->exists()) {
            $expense_opening_balance = Category::firstOrCreate([
                'name' => '[OPENING BALANCE (-)]',
                'type' => TransactionsType::EXPENSE,
                'code' => SystemCategoryCode::OPENING_NEGATIVE->value,
                'only_system_flag' => true,
            ]);
            $income_opening_balance = Category::firstOrCreate([
                'name' => '[OPENING BALANCE (+)]',
                'type' => TransactionsType::INCOME,
                'code' => SystemCategoryCode::OPENING_POSITIVE->value,
                'only_system_flag' => true,
            ]);
        }

        if (! Category::where('name', 'LIKE', '[ADJUSTMENT %')->exists()) {
            $expense_adjustment = Category::firstOrCreate([
                'name' => '[ADJUSTMENT (-)]',
                'type' => TransactionsType::EXPENSE,
                'code' => SystemCategoryCode::ADJUST_NEGATIVE->value,
                'only_system_flag' => true,
            ]);
            $income_adjustment = Category::firstOrCreate([
                'name' => '[ADJUSTMENT (+)]',
                'type' => TransactionsType::INCOME,
                'code' => SystemCategoryCode::ADJUST_POSITIVE->value,
                'only_system_flag' => true,
            ]);
        }
    }
}
