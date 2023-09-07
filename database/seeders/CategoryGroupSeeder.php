<?php

namespace Database\Seeders;

use App\Models\CategoryGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryGroup::factory(rand(5,15))->create();
    }
}
